<?php
/*
 * Database Configuration
 * radji e-shopping website
 */

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'radji_eshopping');

// Create connection (non-fatal in case MySQL is not running)
mysqli_report(MYSQLI_REPORT_OFF);
try {
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
} catch (Throwable $e) {
    $conn = null;
}

if ($conn instanceof mysqli && !$conn->connect_error) {
    $conn->set_charset("utf8");
    define('DB_CONNECTED', true);
} else {
    $conn = null;
    define('DB_CONNECTED', false);
}

// Define base URL for different local serving modes
$script_name = $_SERVER['SCRIPT_NAME'] ?? '';
if (strpos($script_name, '/radji/') !== false) {
    $app_url = '/radji/';
} else {
    $app_url = '/';
}
define('APP_URL', $app_url);
define('PUBLIC_URL', APP_URL . 'public/');
define('BASE_URL', PUBLIC_URL);

// Define upload directory
define('UPLOAD_DIR', '../uploads/');

// Error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
