<?php
// Database credentials for Railway MySQL
define('DB_SERVER', 'yamabiko.proxy.rlwy.net'); // Railway MySQL host
define('DB_USERNAME', 'root'); // Railway MySQL username
define('DB_PASSWORD', 'rmSSdlFESZOtsPzGgkGKMgcDgukqVjis'); // Railway MySQL password
define('DB_NAME', 'railway'); // Railway database name
define('DB_PORT', 45657); // Railway MySQL port

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME, DB_PORT);

// Check connection
if (!$link) {
    error_log("Connection error: " . mysqli_connect_error()); // Log the error
    die("Unable to connect to the database. Please try again later."); // User-friendly error
}
?>
