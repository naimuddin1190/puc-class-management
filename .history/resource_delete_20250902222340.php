<?php
session_start();
include 'db.php';

// Only CR can delete
if (empty($_SESSION['is_cr'])) {
    exit("Access denied. Only CR can delete.");
}

if (empty($_POST['id'])) {
    exit("Invalid request");
}

$id = (int)$_POST['id'];

// Find file path
$stmt = mysqli_prepare($conn, "SELECT file_path, subject FROM resources WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$row) {
    exit("Resource not found.");
}

// Delete DB row
$stmt = mysqli_prepare($conn, "DELETE FROM resources WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Delete file from disk
$abs = __DIR__ . '/' . $row['file_path'];
if (is_file($abs)) {
    if (!@unlink($abs)) {
        exit("Failed to delete file: " . $abs . ". Check folder permissions.");
    }
}

// Redirect back to same subject
header("Location: resources.php?subject=" . urlencode($row['subject']));
exit;
