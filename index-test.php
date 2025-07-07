<?php
// Simple test index to verify deployment
echo "<h1>AG Station - Deployment Test</h1>";
echo "<p>Current time: " . date('Y-m-d H:i:s') . "</p>";
echo "<p>PHP Version: " . PHP_VERSION . "</p>";

// Test environment variables
echo "<h2>Environment Variables:</h2>";
echo "<p>DB_HOST: " . ($_ENV['DB_HOST'] ?? 'Not set') . "</p>";
echo "<p>DB_NAME: " . ($_ENV['DB_NAME'] ?? 'Not set') . "</p>";
echo "<p>DB_TYPE: " . ($_ENV['DB_TYPE'] ?? 'Not set') . "</p>";

// Test database connection
echo "<h2>Database Connection Test:</h2>";
try {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $db = $_ENV['DB_NAME'] ?? 'test';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASSWORD'] ?? '';
    $port = $_ENV['DB_PORT'] ?? '5432';
    $db_type = $_ENV['DB_TYPE'] ?? 'pgsql';
    
    if ($db_type === 'pgsql') {
        $pdo = new PDO("pgsql:host=$host;port=$port;dbname=$db", $user, $pass);
    } else {
        $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db", $user, $pass);
    }
    
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "<p style='color: green;'>✅ Database connection successful!</p>";
    
} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Database connection failed: " . $e->getMessage() . "</p>";
}

echo "<h2>Navigation:</h2>";
echo "<ul>";
echo "<li><a href='/health.php'>Health Check</a></li>";
echo "<li><a href='/frontend/index.php'>Frontend (Original)</a></li>";
echo "<li><a href='/frontend/login.php'>Login Page</a></li>";
echo "<li><a href='/admin/dashboard.php'>Admin Dashboard</a></li>";
echo "</ul>";
?>
