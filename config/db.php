<?php
// Database configuration for Render/Production deployment
$host = $_ENV['DB_HOST'] ?? getenv('DB_HOST') ?? 'localhost';
$db = $_ENV['DB_NAME'] ?? getenv('DB_NAME') ?? 'agstation_db';
$user = $_ENV['DB_USER'] ?? getenv('DB_USER') ?? 'root';
$pass = $_ENV['DB_PASSWORD'] ?? getenv('DB_PASSWORD') ?? 'Halima4103@';
$port = $_ENV['DB_PORT'] ?? getenv('DB_PORT') ?? '5432';

// Determine database type based on port or environment
$db_type = $_ENV['DB_TYPE'] ?? (($port == '5432') ? 'pgsql' : 'mysql');

try {
    if ($db_type === 'pgsql') {
        // PostgreSQL connection (for Render)
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    } else {
        // MySQL connection (for local development)
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>
