<?php
// Database configuration
$host = '127.0.0.1'; // Use 127.0.0.1 instead of localhost for better compatibility
$port = '3306'; // Default MySQL port
$db = 'boys_clothing';
$user = 'root'; // XAMPP default user
$pass = ''; // XAMPP default password (empty)

try {
    // Try connecting with port specified
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // More user-friendly error message
    $errorMessage = $e->getMessage();
    error_log("Database connection error: " . $errorMessage, 3, "errors.log");
    
    // Check if it's a connection refused error
    if (strpos($errorMessage, '2002') !== false || strpos($errorMessage, 'actively refused') !== false) {
        die("
        <div style='padding: 20px; font-family: Arial; max-width: 600px; margin: 50px auto; border: 2px solid #dc3545; border-radius: 5px; background: #f8d7da;'>
            <h2 style='color: #721c24;'>⚠️ Database Connection Error</h2>
            <p style='color: #721c24;'><strong>MySQL server is not running!</strong></p>
            <p style='color: #721c24;'>Please follow these steps:</p>
            <ol style='color: #721c24;'>
                <li>Open <strong>XAMPP Control Panel</strong></li>
                <li>Click <strong>Start</strong> next to <strong>MySQL</strong></li>
                <li>Wait until MySQL status shows as <strong>Running</strong> (green)</li>
                <li>Refresh this page</li>
            </ol>
            <p style='color: #721c24; margin-top: 20px;'><small>If MySQL still won't start, check the XAMPP Control Panel for error messages.</small></p>
        </div>
        ");
    } else {
        // Other database errors
        die("
        <div style='padding: 20px; font-family: Arial; max-width: 600px; margin: 50px auto; border: 2px solid #dc3545; border-radius: 5px; background: #f8d7da;'>
            <h2 style='color: #721c24;'>⚠️ Database Connection Error</h2>
            <p style='color: #721c24;'><strong>Error:</strong> " . htmlspecialchars($errorMessage) . "</p>
            <p style='color: #721c24;'>Please check your database configuration in <code>includes/config.php</code></p>
        </div>
        ");
    }
}
?>