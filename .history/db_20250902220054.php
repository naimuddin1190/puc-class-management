<?php
// Database Configuration
$servername = "localhost";
$username   = "root";   // XAMPP/MAMP এর জন্য সাধারণত root
$password   = "";       // Default password খালি থাকে
$dbname     = "puc_portal_db";

// Database Connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check Connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

/*
|--------------------------------------------------------------------------
| Tables Needed:
|--------------------------------------------------------------------------
| 1. subjects (id, name)
| 2. notes (id, subject_id, file_name, file_path, uploaded_at)
| 3. slides (id, subject_id, file_name, file_path, uploaded_at)
| 4. pdfs (id, subject_id, file_name, file_path, uploaded_at)
|--------------------------------------------------------------------------
|
| তুমি phpMyAdmin এ গিয়ে নিচের SQL রান করলে টেবিলগুলো তৈরি হয়ে যাবে।
|
| CREATE TABLE subjects (
|   id INT AUTO_INCREMENT PRIMARY KEY,
|   name VARCHAR(255) NOT NULL
| );
|
| CREATE TABLE notes (
|   id INT AUTO_INCREMENT PRIMARY KEY,
|   subject_id INT,
|   file_name VARCHAR(255),
|   file_path VARCHAR(255),
|   uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
|   FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
| );
|
| CREATE TABLE slides (
|   id INT AUTO_INCREMENT PRIMARY KEY,
|   subject_id INT,
|   file_name VARCHAR(255),
|   file_path VARCHAR(255),
|   uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
|   FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
| );
|
| CREATE TABLE pdfs (
|   id INT AUTO_INCREMENT PRIMARY KEY,
|   subject_id INT,
|   file_name VARCHAR(255),
|   file_path VARCHAR(255),
|   uploaded_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
|   FOREIGN KEY (subject_id) REFERENCES subjects(id) ON DELETE CASCADE
| );
|
*/
?>
