<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "blood_donation_db";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to handle Arabic and special characters
$conn->set_charset("utf8mb4");

// Function to sanitize input data
if (!function_exists('sanitize_input')) {
    function sanitize_input($data, $conn) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $conn->real_escape_string($data);
    }
}
?>
