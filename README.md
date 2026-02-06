# Full-Stack Multi-Service Application

This project demonstrates a full-stack application with multiple microservices orchestrated using Docker Compose. The application includes:

- A PHP API for user management
- A Go scheduler service
- A Python service
- A React frontend

## Architecture

The application consists of the following services:

1. **MySQL Database**: Stores user data
2. **PHP API**: Provides REST endpoints for user management
3. **Go Scheduler**: Fetches data from PHP API and forwards specific records to Python service
4. **Python Service**: Receives and stores data from Go scheduler
5. **React Frontend**: Web interface for user interaction

## Prerequisites

- Docker
- Docker Compose

## Getting Started

### Running the Application

1. Clone the repository:
   ```bash
   git clone <your-repo-url>
   cd <repo-directory>
   ```

2. Build and start all services:
   ```bash
   docker-compose up --build
   ```

3. Access the services:
   - Frontend: http://localhost:4135
   - PHP API: http://localhost:8080/users
   - Python Service: http://localhost:5000

### Stopping the Application

```bash
docker-compose down
```

## Services Details

### PHP API (`/php-api`)
- Handles user creation via POST `/users` endpoint
- Stores user data in MySQL database
- Validates input data (name, email)
- Returns created user data with assigned ID

### Go Scheduler (`/go-scheduler-service`)
- Periodically fetches data from PHP API
- Stores received data in a local file
- Checks if user name starts with "David" and forwards to Python service
- Configurable via environment variables

### Python Service (`/python-service`)
- Receives user data from Go scheduler
- Stores received data in a local JSON file
- Provides endpoints for data retrieval

### React Frontend (`/frontend`)
- Simple form for user creation
- Submits data to PHP API
- Displays created user information
- Styled with CSS

## Environment Variables

The application uses environment variables for configuration:

- `PHP_API_URL`: Base URL for PHP API (default: http://php-api)
- `PYTHON_API_URL`: Base URL for Python service (default: http://python-service:5000)
- Database connection settings for PHP API

## Project Structure

```
.
├── php-api/                  # PHP API service
│   ├── index.php             # Main API file
│   └── Dockerfile            # Docker configuration
├── go-scheduler-service/     # Go scheduler service
│   ├── main.go               # Main Go application
│   ├── go.mod                # Go module definition
│   └── Dockerfile            # Docker configuration
├── python-service/           # Python service
│   ├── app.py                # Flask application
│   ├── requirements.txt      # Python dependencies
│   └── Dockerfile            # Docker configuration
├── frontend/                 # React frontend
│   ├── src/
│   │   ├── App.js            # Main React component
│   │   └── App.css           # Styles
│   ├── public/
│   │   └── index.html        # HTML template
│   ├── package.json          # Node dependencies
│   └── Dockerfile            # Docker configuration
├── mysql-data/               # MySQL data volume
├── docker-compose.yml        # Docker Compose configuration
└── README.md                 # This file
```

## Development

To run individual services for development:

1. Navigate to the respective service directory
2. Follow the service-specific development instructions

## API Endpoints

### PHP API
- `POST /users` - Create a new user
  - Request body: `{"name": "John Doe", "email": "john@example.com"}`
  - Response: `{"id": 1, "name": "John Doe", "email": "john@example.com"}`

### Python Service
- `POST /receive_user` - Receive user from Go scheduler
- `GET /users` - Get all received users
- `GET /` - Health check

## Troubleshooting

- If containers fail to start, check logs with `docker-compose logs <service-name>`
- Make sure ports 4135, 5000, and 8080 are available
- Ensure Docker has enough resources allocated

## Bonus Features Implemented

- Error handling and input validation in PHP API
- Styled UI using CSS in React frontend
- Environment variables for API URLs in all services
- Proper dependency management for each service
- Comprehensive Docker Compose orchestration