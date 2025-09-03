<?php
// --- DB connect (তোমার নিজের ক্রেডেনশিয়াল বসাও) ---
$host = "localhost";
$user = "root";
$pass = "";
$dbname = ";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
  die("Database connection failed: " . mysqli_connect_error());
}
mysqli_set_charset($conn, 'utf8mb4');

// --- Sessions + CR PIN ---
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}
define('CR_PIN', '4242'); // চাইলে বদলে নাও

// শুধু CR allowed এমন জায়গায় এই ফাংশন কল করবে
function require_cr() {
  if (empty($_SESSION['is_cr'])) {
    http_response_code(403);
    exit("Forbidden: CR only");
  }
}
