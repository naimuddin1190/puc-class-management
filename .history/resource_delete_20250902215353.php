<?php
include 'db.php';
require_cr();

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
  exit("Not found");
}

// Delete DB row
$stmt = mysqli_prepare($conn, "DELETE FROM resources WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Delete file from disk (optional)
$abs = __DIR__ . '/' . $row['file_path'];
if (is_file($abs)) { @unlink($abs); }

// Redirect back to same subject
header("Location: resources.php?subject=" . urlencode($row['subject']));
exit;
