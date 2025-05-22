<?php
// Database credentials from environment variables
define('DB_SERVER', getenv('DB_SERVER') ?: 'localhost');
define('DB_USERNAME', getenv('DB_USERNAME') ?: 'root');
define('DB_PASSWORD', getenv('DB_PASSWORD') ?: '');
define('DB_NAME', getenv('DB_NAME') ?: 'final_project');

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if (mysqli_connect_errno()) {
    error_log("Connection error: " . mysqli_connect_error()); // Log the error
    die("Unable to connect to the database. Please try again later."); // User-friendly error
}
?>
