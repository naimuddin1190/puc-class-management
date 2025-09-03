<?php
session_start();
include 'db.php';

// Only CR
if (empty($_SESSION['is_cr'])) {
    exit("Access denied. Only CR can post notices.");
}

if (!isset($_POST['subject'], $_POST['title'], $_POST['content'])) {
    exit("Invalid request");
}

$subject = trim($_POST['subject']);
$title = trim($_POST['title']);
$content = trim($_POST['content']);

// Allowed subjects from DB
$sub_result = mysqli_query($conn, "SELECT name FROM subjects");
$allowedSubjects = [];
while($row = mysqli_fetch_assoc($sub_result)) {
    $allowedSubjects[] = $row['name'];
}

if (!in_array($subject, $allowedSubjects)) {
    exit("Invalid subject");
}

// Insert into notices table
$stmt = mysqli_prepare($conn, "INSERT INTO notices (subject, title, content) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $subject, $title, $content);
if (!mysqli_stmt_execute($stmt)) {
    exit("DB error: " . mysqli_error($conn));
}
mysqli_stmt_close($stmt);

// Redirect back to resources page
header("Location: resources.php?subject=" . urlencode($subject));
exit;
