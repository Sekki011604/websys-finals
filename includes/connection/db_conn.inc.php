<?php

// Database Connection for 2nd Phone Shop

// Database credentials - PLEASE REPLACE WITH YOUR ACTUAL CREDENTIALS
$db_host = 'localhost';      // Usually 'localhost' or an IP address
$db_user = 'root';           // Your database username
$db_pass = 'root';               // Your database password
$db_name = 'shop';  // The name of your database (e.g., from database_schema.sql)

// Attempt to connect to MySQL database
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    // In a production environment, log this error to a file
    // and display a generic error message to the user.
    error_log("Database Connection Failed: " . $conn->connect_error);
    die("Sorry, we're experiencing technical difficulties. Please try again later.");
}

// Set character set to utf8mb4 for full Unicode support (recommended)
if (!$conn->set_charset("utf8mb4")) {
    error_log("Error loading character set utf8mb4: " . $conn->error);
    // die("Error loading character set utf8mb4: " . $conn->error); // Optional: or handle more gracefully
}

// The $conn variable is now available for database operations.
// Remember to close the connection when it's no longer needed, typically at the end of a script.
// Example: // if (isset($conn)) { $conn->close(); }
?>