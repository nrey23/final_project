<?php
// Database credentials for Railway MySQL
define('DB_SERVER', 'yamabiko.proxy.rlwy.net'); // Railway MySQL host
define('DB_USERNAME', 'root'); // Railway MySQL username
define('DB_PASSWORD', 'rmSSdlFESZOtsPzGgkGKMgcDgukqVjis'); // Railway MySQL password
define('DB_NAME', 'railway'); // Railway database name
define('DB_PORT', 45657); // Railway MySQL port

// Attempt to connect to MySQL database
$link = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if ($link->connect_error) {
    error_log("Connection failed: " . $link->connect_error); // Log error for debugging
    die("Error: Unable to connect to the database. Please try again later."); // User-friendly error
}

// Optional: Debugging confirmation
echo "✅ Database connection successful!";
?>
