<?php
// Get Railway-assigned PORT (default to 8080 if not set)
$port = getenv('PORT') ?: 8080;

// Start built-in PHP server
echo "🚀 Server running on port $port...\n";
exec("php -S 0.0.0.0:$port -t .");
?>
