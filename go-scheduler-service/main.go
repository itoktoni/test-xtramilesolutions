package main

import (
	"bytes"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"strings"
	"time"
)

type User struct {
	ID        int    `json:"id"`
	Name      string `json:"name"`
	Email     string `json:"email"`
	CreatedAt string `json:"created_at"`
	UpdatedAt string `json:"updated_at"`
}

type SyncState struct {
	LastCreatedID         int `json:"last_created_id"`
	LastProcessedUpdateID int `json:"last_processed_update_id"`
}

type Config struct {
	PHPAPIURL    string
	PythonAPIURL string
	StateFile    string
	UsersDir     string
	SleepSeconds int
	MaxUserID    int
}

const defaultSleepSeconds = 30
const defaultMaxUserID = 100

func main() {
	cfg := loadConfig()

	// Create users directory
	usersPath := cfg.UsersDir
	if err := os.MkdirAll(usersPath, 0755); err != nil {
		log.Fatalf("Failed to create users directory: %v", err)
	}
	log.Printf("Users directory: %s", usersPath)
	log.Printf("Max user ID to scan: %d", cfg.MaxUserID)

	// Create state.json if not exists
	if _, err := os.Stat(cfg.StateFile); os.IsNotExist(err) {
		log.Printf("State file not found, creating new state...")
		state := &SyncState{
			LastCreatedID:         0,
			LastProcessedUpdateID: 0,
		}
		saveState(cfg.StateFile, state)
	}

	log.Println("Starting Go scheduler service...")
	log.Printf("PHP API URL: %s", cfg.PHPAPIURL)
	log.Printf("Python API URL: %s", cfg.PythonAPIURL)

	syncState := loadState(cfg.StateFile)
	log.Printf("Initial state: last_created_id=%d, last_processed_update_id=%d",
		syncState.LastCreatedID, syncState.LastProcessedUpdateID)

	// Main sync loop
	for {
		// Fetch all users from PHP API (one request)
		usersProcessed := processAllUsers(cfg, syncState)

		if usersProcessed {
			log.Printf("Completed processing new users, sleeping for %d seconds...", cfg.SleepSeconds)
			time.Sleep(time.Duration(cfg.SleepSeconds) * time.Second)
		} else {
			// Error occurred, retry sooner
			log.Printf("Error occurred, retrying in 5 seconds...")
			time.Sleep(5 * time.Second)
		}
	}
}

// processAllUsers fetches all users from PHP API and processes only new ones
func processAllUsers(cfg Config, state *SyncState) bool {
	log.Printf("Fetching all users from PHP API: %s/users", cfg.PHPAPIURL)

	// Fetch all users in one request
	allUsers, err := fetchAllUsers(cfg.PHPAPIURL + "/users")
	if err != nil {
		log.Printf("Error fetching all users: %v", err)
		return false
	}

	// Skip if nil response (non-JSON response)
	if allUsers == nil {
		return true
	}

	if len(allUsers) == 0 {
		log.Printf("No users found in PHP API")
		return true
	}

	// Filter to only new users (ID > last_created_id)
	var newUsers []User
	for _, user := range allUsers {
		if user.ID > state.LastCreatedID {
			newUsers = append(newUsers, user)
		}
	}

	if len(newUsers) == 0 {
		log.Printf("No new users found (all users have ID <= %d)", state.LastCreatedID)
		return true
	}

	log.Printf("Found %d new users (ID > %d), processing each one...", len(newUsers), state.LastCreatedID)

	// Foreach: Process each new user
	for _, user := range newUsers {
		log.Printf("Processing user: %s (ID: %d, Email: %s)", user.Name, user.ID, user.Email)

		// Store user data to users folder
		err = storeUserData(&user, cfg.UsersDir)
		if err != nil {
			log.Printf("Error storing user %d: %v", user.ID, err)
			continue
		}

		log.Printf("Logged user %d to %s/user_%d.json", user.ID, cfg.UsersDir, user.ID)

		// Check if name contains "David" (case-insensitive)
		if strings.Contains(strings.ToLower(user.Name), "david") {
			log.Printf("User name contains 'David', sending to Python Flask API...")
			err = sendToPythonService(&user, cfg.PythonAPIURL)
			if err != nil {
				log.Printf("ERROR: Failed to send user %d to Python: %v", user.ID, err)
			} else {
				log.Printf("SUCCESS: Sent user '%s' (ID: %d, Email: %s) to Python Flask API",
					user.Name, user.ID, user.Email)
			}
		}

		// Update state with new last_created_id
		state.LastCreatedID = user.ID
		saveState(cfg.StateFile, state)
	}

	log.Printf("Completed processing %d new users", len(newUsers))
	return true
}

// fetchAllUsers fetches all users from the PHP API
func fetchAllUsers(url string) ([]User, error) {
	client := &http.Client{Timeout: 30 * time.Second}

	resp, err := client.Get(url)
	if err != nil {
		return nil, fmt.Errorf("failed to send request: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		// Skip non-JSON responses silently
		return nil, nil
	}

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, nil // Skip on read error
	}

	// Check if response is JSON array
	if len(body) == 0 || body[0] != '[' {
		return nil, nil // Skip non-JSON responses
	}

	var users []User
	err = json.Unmarshal(body, &users)
	if err != nil {
		return nil, nil // Skip on unmarshal error
	}

	return users, nil
}

// fetchSingleUser fetches a single user from the PHP API
func fetchSingleUser(url string) (*User, error) {
	client := &http.Client{Timeout: 30 * time.Second}

	resp, err := client.Get(url)
	if err != nil {
		return nil, fmt.Errorf("failed to send request: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode == 404 {
		return nil, fmt.Errorf("user not found")
	}

	if resp.StatusCode != http.StatusOK {
		body, _ := io.ReadAll(resp.Body)
		return nil, fmt.Errorf("unexpected status code %d: %s", resp.StatusCode, string(body))
	}

	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("failed to read response: %w", err)
	}

	var user User
	err = json.Unmarshal(body, &user)
	if err != nil {
		return nil, fmt.Errorf("failed to unmarshal response: %w", err)
	}

	return &user, nil
}

func loadConfig() Config {
	maxID := getEnvInt("MAX_USER_ID", defaultMaxUserID)
	return Config{
		PHPAPIURL:    getEnv("PHP_API_URL", "http://php-api:8080"),
		PythonAPIURL: getEnv("PYTHON_API_URL", "http://python-service:5000"),
		StateFile:    "data/state.json",
		UsersDir:     "users",
		SleepSeconds: getEnvInt("SLEEP_SECONDS", defaultSleepSeconds),
		MaxUserID:    maxID,
	}
}

func getEnv(key, defaultValue string) string {
	if value := os.Getenv(key); value != "" {
		return value
	}
	return defaultValue
}

func getEnvInt(key string, defaultValue int) int {
	if value := os.Getenv(key); value != "" {
		var result int
		fmt.Sscanf(value, "%d", &result)
		if result > 0 {
			return result
		}
	}
	return defaultValue
}

func loadState(filename string) *SyncState {
	data, err := os.ReadFile(filename)
	if err != nil {
		if os.IsNotExist(err) {
			state := &SyncState{
				LastCreatedID:         0,
				LastProcessedUpdateID: 0,
			}
			saveState(filename, state)
			return state
		}
		log.Printf("Warning: Could not read state file: %v", err)
		return &SyncState{}
	}

	var state SyncState
	if err := json.Unmarshal(data, &state); err != nil {
		log.Printf("Warning: Could not parse state file: %v", err)
		return &SyncState{}
	}

	return &state
}

func saveState(filename string, state *SyncState) {
	data, err := json.MarshalIndent(state, "", "  ")
	if err != nil {
		log.Printf("Error marshaling state: %v", err)
		return
	}

	if err := os.WriteFile(filename, data, 0644); err != nil {
		log.Printf("Error saving state: %v", err)
	}
}

func storeUserData(user *User, usersDir string) error {
	filename := fmt.Sprintf("%s/user_%d.json", usersDir, user.ID)
	data, err := json.MarshalIndent(user, "", "  ")
	if err != nil {
		return fmt.Errorf("failed to marshal user: %w", err)
	}

	err = os.WriteFile(filename, data, 0644)
	if err != nil {
		return fmt.Errorf("failed to write file: %w", err)
	}

	return nil
}

func sendToPythonService(user *User, baseURL string) error {
	client := &http.Client{Timeout: 10 * time.Second}

	jsonData, err := json.Marshal(user)
	if err != nil {
		return fmt.Errorf("failed to marshal user: %w", err)
	}

	log.Printf("Sending to Python Flask API: %s/receive_user", baseURL)
	log.Printf("Payload: %s", string(jsonData))

	resp, err := client.Post(baseURL+"/receive_user", "application/json", bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("failed to send request: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		body, _ := io.ReadAll(resp.Body)
		return fmt.Errorf("unexpected status code %d: %s", resp.StatusCode, string(body))
	}

	responseBody, _ := io.ReadAll(resp.Body)
	log.Printf("Python Flask API response: %s", string(responseBody))

	return nil
}
