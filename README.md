# Full-Stack Multi-Service Application

This project demonstrates a full-stack application with multiple microservices orchestrated using Docker Compose. The application includes:

- A Laravel PHP API for user management
- A Go scheduler service
- A Python service
- A React frontend

## Architecture

The application consists of the following services:

1. **MySQL Database**: Stores user data
2. **PHP API (Laravel)**: Provides REST endpoints for user management
3. **Go Scheduler**: Fetches data from PHP API and forwards specific records to Python service
4. **Python Service**: Receives and stores data from Go scheduler
5. **React Frontend**: Web interface for user interaction

## Prerequisites

- Docker
- Docker Compose
- Git

## Installation

1. Clone the repository:
   ```bash
   git clone <your-repo-url>
   cd <repo-directory>
   ```

2. Copy environment files:
   ```bash
   cp .env.example .env
   ```

3. Build and start all services:
   ```bash
   docker-compose up --build -d
   ```

4. Access the services:
   - Frontend: http://localhost:4135
   - PHP API: http://localhost:8080/users
   - Python Service: http://localhost:5000

## Running the Application

### Start Services
```bash
docker-compose up -d
```

### Stop Services
```bash
docker-compose down
```

### View Logs
```bash
docker-compose logs -f <service-name>
```

## Services Details

### PHP API (`/php-api`)
A Laravel-based REST API with the following structure:
- **Controllers**: `app/Http/Controllers/UserController.php`
- **Services**: `app/Services/UserService.php`
- **Repositories**: `app/Repositories/EloquentUserRepository.php`, `UserRepositoryInterface.php`
- **Models**: `app/Models/User.php`
- **Routes**: `routes/api.php`, `routes/web.php`

**API Endpoints:**
- `GET /users` - List all users
- `POST /users` - Create a new user
- `GET /users/{id}` - Get specific user
- `PUT /users/{id}` - Update user
- `DELETE /users/{id}` - Delete user

### Go Scheduler (`/go-scheduler-service`)
- **Main**: `main.go` - Main Go application
- **State**: `state.json` - Tracks processed user IDs
- **Data Storage**: `data/` and `users/` directories
- Periodically fetches data from PHP API
- Checks if user name starts with "David" and forwards to Python service

### Python Service (`/python-service`)
- **Main**: `app.py` - Flask application
- Receives user data from Go scheduler
- Stores received data in `users/` directory
- Provides endpoints for data retrieval

### React Frontend (`/frontend`)
- **Components**: `UserForm.js`, `UserTable.js` in `src/components/`
- **Main**: `App.js`, `index.js` in `src/`
- **Public**: `index.html` template
- Simple form for user creation and table display

## Environment Variables

### Root `.env`
- Database connection settings
- Service URLs configuration

### PHP API `.env`
- Database: `DB_CONNECTION=mysql`, `DB_HOST=mysql`, `DB_PORT=3306`
- `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`

### Go Scheduler
- `PHP_API_URL`: Base URL for PHP API
- `PYTHON_API_URL`: Base URL for Python service

## Project Structure

```
.
├── .env.example                    # Root environment template
├── .gitignore                       # Git ignore rules
├── docker-compose.yml               # Docker Compose configuration
├── package-lock.json               # npm lock file
├── README.md                        # This file
│
├── php-api/                         # Laravel PHP API service
│   ├── app/                         # Application code
│   │   ├── Http/
│   │   │   ├── Controllers/
│   │   │   │   └── UserController.php
│   │   │   └── Kernel.php
│   │   ├── Models/
│   │   │   └── User.php
│   │   ├── Providers/
│   │   │   ├── AppServiceProvider.php
│   │   │   └── UserServiceProvider.php
│   │   ├── Repositories/
│   │   │   ├── EloquentUserRepository.php
│   │   │   └── UserRepositoryInterface.php
│   │   └── Services/
│   │       └── UserService.php
│   ├── bootstrap/
│   │   ├── app.php
│   │   └── providers.php
│   ├── config/
│   │   ├── app.php
│   │   ├── database.php
│   │   └── ...
│   ├── database/
│   │   ├── factories/
│   │   ├── migrations/
│   │   └── seeders/
│   ├── public/
│   │   └── index.php
│   ├── routes/
│   │   ├── api.php
│   │   ├── web.php
│   │   └── console.php
│   ├── resources/
│   │   ├── views/
│   │   ├── js/
│   │   └── css/
│   ├── storage/
│   │   ├── app/
│   │   ├── framework/
│   │   └── logs/
│   ├── tests/
│   │   ├── Feature/
│   │   └── Unit/
│   ├── .env.example
│   ├── composer.json
│   ├── artisan
│   ├── Dockerfile
│   └── ...
│
├── go-scheduler-service/            # Go scheduler service
│   ├── main.go                      # Main Go application
│   ├── go.mod                       # Go module definition
│   ├── go.sum                       # Go dependencies
│   ├── Dockerfile                   # Docker configuration
│   ├── state.json.example           # State file template
│   ├── data/                        # Data storage directory
│   └── users/                       # Users storage directory
│
├── python-service/                  # Python Flask service
│   ├── app.py                       # Flask application
│   ├── requirements.txt            # Python dependencies
│   ├── Dockerfile                   # Docker configuration
│   └── users/                       # Users storage directory
│
└── frontend/                        # React frontend
    ├── src/
    │   ├── index.js                # React entry point
    │   ├── App.js                  # Main App component
    │   ├── App.css                 # App styles
    │   └── components/
    │       ├── UserForm.js         # User creation form
    │       └── UserTable.js        # User table display
    ├── public/
    │   └── index.html              # HTML template
    ├── package.json                # Node dependencies
    ├── Dockerfile                  # Docker configuration
    └── ...
```

## Development

To run individual services for development:

### PHP API (Laravel)
```bash
cd php-api
composer install
cp .env.example .env
php artisan migrate
php artisan serve
```

### Go Scheduler
```bash
cd go-scheduler-service
go run main.go
```

### Python Service
```bash
cd python-service
pip install -r requirements.txt
python app.py
```

### React Frontend
```bash
cd frontend
npm install
npm start
```

## API Endpoints Reference

### PHP API (Laravel)
| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/users` | Get all users |
| POST | `/users` | Create new user |
| GET | `/users/{id}` | Get user by ID |
| PUT | `/users/{id}` | Update user |
| DELETE | `/users/{id}` | Delete user |

### Python Service
| Method | Endpoint | Description |
|--------|----------|-------------|
| POST | `/receive_user` | Receive user from Go scheduler |
| GET | `/users` | Get all received users |
| GET | `/` | Health check |

## Troubleshooting

- **Containers fail to start**: Check logs with `docker-compose logs <service-name>`
- **Port conflicts**: Ensure ports 4135, 5000, 8080, and 3306 are available
- **Database connection issues**: Verify `.env` database settings
- **Permission errors**: Run `chmod -R 775 storage bootstrap/cache` in php-api directory