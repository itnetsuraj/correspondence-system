<?php
declare(strict_types=1);

/**
 * SECURITY FIX: Use environment variables instead of hardcoded credentials
 * 
 * This file should NOT be committed to version control.
 * Add 'config/db.php' to .gitignore
 * 
 * Create a .env file with your actual credentials:
 * DB_HOST=localhost
 * DB_USER=root
 * DB_PASS=your_password
 * DB_NAME=correspondence
 */

// Check if environment variables are set, fallback to defaults for development
$host = getenv('DB_HOST') ?: 'localhost';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') ?: '';
$database = getenv('DB_NAME') ?: 'correspondence';

$conn = new mysqli(
    $host,
    $username,
    $password,
    $database
);

if ($conn->connect_error) {

    die(
        'Connection failed: '
        . htmlspecialchars($conn->connect_error, ENT_QUOTES, 'UTF-8')
    );
}

$conn->set_charset('utf8mb4');

// Enable error reporting for development (disable in production)
if (getenv('APP_ENV') !== 'production') {
    mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
}
