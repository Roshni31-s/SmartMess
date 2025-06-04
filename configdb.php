<?php
// configdb.php

// Check if the constants are already defined to prevent duplicate definitions
if (!defined('DB_SERVER')) {
    define('DB_SERVER', 'sql200.infinityfree.com'); // Your hostname
}

if (!defined('DB_USERNAME')) {
    define('DB_USERNAME', 'if0_39135426');       // Your database username
}

if (!defined('DB_PASSWORD')) {
    define('DB_PASSWORD', 'iesphyJHp4Ri');       // Your database password
}

if (!defined('DB_NAME')) {
    define('DB_NAME', 'if0_39135426_mess_db');   // Your database name
}

// Check if connection already exists
if (!isset($conn)) {
    // Create a new MySQLi connection
    $conn = new mysqli(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);
    
    // Check connection
    if ($conn->connect_error) {
        // Log the error for debugging (don't show to users in production)
        error_log("Database connection failed: " . $conn->connect_error);
        
        // Show user-friendly message
        die("Database connection error. Please try again later.");
    }
    
    // Set charset
    $conn->set_charset("utf8mb4");
}

// Optional: For debugging, you can check connection status
// if (isset($conn) && $conn->ping()) {
//     error_log("Database connection active");
// }
?>