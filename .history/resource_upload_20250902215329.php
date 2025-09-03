<?php
include 'db.php';
require_cr();

if (!isset($_POST['subject'], $_POST['title']) || !isset($_FILES['file'])) {
  exit("Invalid request");
}

$subject = trim($_POST['subject']);
$title   = trim($_POST['title']);

// Allowed subjects (resources.php এর সাথে মিলিয়ে রাখো)
$allowedSubjects = ['SE','CCS','PME','SEL','Other'];
if (!in_array($subject, $allowedSubjects)) {
  exit("Invalid subject");
}

// Size limit: 20MB
if ($_FILES['file']['size'] > 20 * 1024 * 1024) {
  exit("File too large (max 20MB).");
}

// Allowed extensions
$allowedExt = ['pdf','ppt','pptx','doc','docx','xls','xlsx','zip','rar','7z','txt'];
$ext = strtolower(pathinfo($_FILES['file']['name'], PATHINFO_EXTENSION));
if (!in_array($ext, $allowedExt)) {
  exit("File type not allowed.");
}

// Create uploads folder if not exists
$baseDir = __DIR__ . '/uploads';
if (!is_dir($baseDir)) { mkdir($baseDir, 0775, true); }

// Subject subfolder
$subDir = $baseDir . '/' . preg_replace('/[^a-z0-9_-]+/i','_', $subject);
if (!is_dir($subDir)) { mkdir($subDir, 0775, true); }

// Safe filename
$basename = time() . '_' . preg_replace('/[^a-z0-9._-]+/i','_', $_FILES['file']['name']);
$targetAbs = $subDir . '/' . $basename;
$targetRel = 'uploads/' . basename($subDir) . '/' . $basename;

// Move file
if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetAbs)) {
  exit("Upload failed.");
}

// Insert DB
$stmt = mysqli_prepare($conn, "INSERT INTO resources (subject, title, file_path) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $subject, $title, $targetRel);
if (!mysqli_stmt_execute($stmt)) {
  @unlink($targetAbs);
  exit("DB error.");
}
mysqli_stmt_close($stmt);

// Back to resources page (stay on the subject tab)
header("Location: resources.php?subject=" . urlencode($subject));
exit;
