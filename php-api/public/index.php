<?php

use Slim\Factory\AppFactory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

require_once __DIR__ . '/../vendor/autoload.php';

$app = AppFactory::create();

// Add CORS middleware
$app->add(function (Request $request, $handler) {
    $response = $handler->handle($request);
    return $response
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Methods', 'GET, POST, OPTIONS')
        ->withHeader('Access-Control-Allow-Headers', 'Content-Type');
});

// Handle OPTIONS preflight requests
$app->options('/{routes:.*}', function (Request $request, Response $response) {
    return $response;
});

// Database configuration
$host = getenv('DB_HOST') ?: 'mariadb';
$dbname = getenv('DB_NAME') ?: 'userdb';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'password';

try {
    $dsn = "mysql:host=$host;dbname=$dbname";
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Create table if not exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL UNIQUE,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )");
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Routes

// GET / - Health check
$app->get('/', function (Request $request, Response $response) {
    $response->getBody()->write(json_encode(['status' => 'PHP API is running']));
    return $response->withHeader('Content-Type', 'application/json');
});

// GET /users - List all users
$app->get('/users', function (Request $request, Response $response) use ($pdo) {
    try {
        $stmt = $pdo->query("SELECT id, name, email FROM users ORDER BY created_at DESC");
        $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($users));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        $response->getBody()->write(json_encode(['error' => 'Failed to fetch users: ' . $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

// POST /users - Create a new user
$app->post('/users', function (Request $request, Response $response) use ($pdo) {
    try {
        $data = json_decode($request->getBody()->getContents(), true);

        if (!isset($data['name']) || !isset($data['email'])) {
            $response->getBody()->write(json_encode(['error' => 'Name and email are required']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $name = trim($data['name']);
        $email = trim($data['email']);

        if (empty($name) || empty($email)) {
            $response->getBody()->write(json_encode(['error' => 'Name and email cannot be empty']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $response->getBody()->write(json_encode(['error' => 'Invalid email format']));
            return $response->withStatus(400)->withHeader('Content-Type', 'application/json');
        }

        $stmt = $pdo->prepare("INSERT INTO users (name, email) VALUES (?, ?)");
        $stmt->execute([$name, $email]);

        $user_id = $pdo->lastInsertId();

        $result = [
            'id' => (int)$user_id,
            'name' => $name,
            'email' => $email
        ];

        $response->getBody()->write(json_encode($result));
        return $response->withStatus(201)->withHeader('Content-Type', 'application/json');

    } catch (PDOException $e) {
        if ($e->getCode() == 23000) { // Duplicate entry
            $response->getBody()->write(json_encode(['error' => 'Email already exists']));
            return $response->withStatus(409)->withHeader('Content-Type', 'application/json');
        }
        $response->getBody()->write(json_encode(['error' => 'Failed to create user: ' . $e->getMessage()]));
        return $response->withStatus(500)->withHeader('Content-Type', 'application/json');
    }
});

$app->run();