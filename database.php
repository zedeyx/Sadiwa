<?php
// Database configuration
define('DB_HOST', 'sql100.infinityfree.com');
define('DB_USER', 'if0_41515292');
define('DB_PASS', '1zEbqfWmWt');
define('DB_NAME', 'if0_41515292_sadiwa');

// Create connection
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    return $conn;
}
?>