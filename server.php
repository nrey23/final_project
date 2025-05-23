<?php
// Get Railway's assigned port or default to 8080
$port = getenv('PORT') ?: 8080;

echo "🚀 Starting PHP server on port $port...\n";
// Start the built-in PHP server
passthru("php -S 0.0.0.0:$port -t .");
?>
