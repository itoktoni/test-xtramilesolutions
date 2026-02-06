package main

import (
	"bufio"
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
	ID    int    `json:"id"`
	Name  string `json:"name"`
	Email string `json:"email"`
}

type UserData struct {
	Name  string `json:"name"`
	Email string `json:"email"`
}

const dataFilePath = "scheduler_data.json"

func main() {
	phpAPIBaseURL := os.Getenv("PHP_API_URL")
	if phpAPIBaseURL == "" {
		phpAPIBaseURL = "http://php-api:80"
	}

	pythonAPIBaseURL := os.Getenv("PYTHON_API_URL")
	if pythonAPIBaseURL == "" {
		pythonAPIBaseURL = "http://python-service:5000"
	}

	log.Println("Starting Go scheduler service...")
	log.Printf("PHP API URL: %s", phpAPIBaseURL)
	log.Printf("Python API URL: %s", pythonAPIBaseURL)

	for {
		// Fetch user from PHP API
		user, err := fetchUserFromAPI(phpAPIBaseURL)
		if err != nil {
			log.Printf("Error fetching user from API: %v", err)
		} else if user != nil {
			// Store user data in file
			err = storeUserData(user)
			if err != nil {
				log.Printf("Error storing user data: %v", err)
			} else {
				log.Printf("Stored user: %s (%s)", user.Name, user.Email)

				// Check if name starts with "David" and send to Python service
				if strings.HasPrefix(strings.ToLower(user.Name), "david") {
					err = sendToPythonService(*user, pythonAPIBaseURL)
					if err != nil {
						log.Printf("Error sending to Python service: %v", err)
					} else {
						log.Printf("Sent user %s to Python service", user.Name)
					}
				}
			}
		}

		// Wait for 30 seconds before next iteration
		time.Sleep(30 * time.Second)
	}
}

func fetchUserFromAPI(baseURL string) (*User, error) {
	// Create a sample user to send to PHP API
	sampleUsers := []UserData{
		{Name: "David Smith", Email: "david@example.com"},
		{Name: "Jane Doe", Email: "jane@example.com"},
		{Name: "David Johnson", Email: "david.johnson@example.com"},
		{Name: "John Smith", Email: "john@example.com"},
	}

	// For demo purposes, we'll cycle through sample users
	// In a real scenario, this would come from some external source

	client := &http.Client{Timeout: 10 * time.Second}

	// Select a user randomly or in rotation
	selectedUser := sampleUsers[int(time.Now().Unix())%len(sampleUsers)]

	jsonData, err := json.Marshal(selectedUser)
	if err != nil {
		return nil, fmt.Errorf("failed to marshal user data: %w", err)
	}

	resp, err := client.Post(baseURL+"/users", "application/json", bytes.NewBuffer(jsonData))
	if err != nil {
		return nil, fmt.Errorf("failed to send request to PHP API: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusCreated && resp.StatusCode != http.StatusOK {
		body, _ := io.ReadAll(resp.Body)
		return nil, fmt.Errorf("unexpected status code %d: %s", resp.StatusCode, string(body))
	}

	var user User
	body, err := io.ReadAll(resp.Body)
	if err != nil {
		return nil, fmt.Errorf("failed to read response body: %w", err)
	}

	err = json.Unmarshal(body, &user)
	if err != nil {
		return nil, fmt.Errorf("failed to unmarshal response: %w", err)
	}

	return &user, nil
}

func storeUserData(user *User) error {
	file, err := os.OpenFile(dataFilePath, os.O_APPEND|os.O_CREATE|os.O_WRONLY, 0644)
	if err != nil {
		return fmt.Errorf("failed to open data file: %w", err)
	}
	defer file.Close()

	// Create a new line with the user data as JSON
	jsonData, err := json.Marshal(user)
	if err != nil {
		return fmt.Errorf("failed to marshal user data: %w", err)
	}

	// Write JSON object to file followed by a newline
	_, err = file.WriteString(string(jsonData) + "\n")
	if err != nil {
		return fmt.Errorf("failed to write user data to file: %w", err)
	}

	return nil
}

func sendToPythonService(user User, baseURL string) error {
	client := &http.Client{Timeout: 10 * time.Second}

	jsonData, err := json.Marshal(user)
	if err != nil {
		return fmt.Errorf("failed to marshal user data: %w", err)
	}

	resp, err := client.Post(baseURL+"/receive_user", "application/json", bytes.NewBuffer(jsonData))
	if err != nil {
		return fmt.Errorf("failed to send request to Python service: %w", err)
	}
	defer resp.Body.Close()

	if resp.StatusCode != http.StatusOK {
		body, _ := io.ReadAll(resp.Body)
		return fmt.Errorf("unexpected status code %d: %s", resp.StatusCode, string(body))
	}

	return nil
}