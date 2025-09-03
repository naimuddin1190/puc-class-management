<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "puc_portal_db"; 

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Admin PIN (🔒 তোমার মতো করে সেট করো)
$ADMIN_PIN = "1234";
?>
