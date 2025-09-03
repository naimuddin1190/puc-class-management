<?php
session_start();
include 'db.php';

// Only CR can upload
if (empty($_SESSION['is_cr'])) {
    exit("Access denied. Only CR can upload.");
}

if (!isset($_POST['subject'], $_POST['title']) || !isset($_FILES['file'])) {
    exit("Invalid request");
}

$subject = trim($_POST['subject']);
$title   = trim($_POST['title']);

// --- Allowed subjects from DB ---
$sub_result = mysqli_query($conn, "SELECT name FROM subjects");
$allowedSubjects = [];
while($row = mysqli_fetch_assoc($sub_result)) {
    $allowedSubjects[] = $row['name'];
}

if (!in_array($subject, $allowedSubjects)) {
    exit("Invalid subject. Make sure the subject exists in the database.");
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
if (!is_dir($baseDir)) {
    if (!mkdir($baseDir, 0775, true)) {
        exit("Failed to create uploads folder. Check permissions.");
    }
}

// Subject subfolder
$subDirName = preg_replace('/[^a-z0-9_-]+/i','_', $subject);
$subDir = $baseDir . '/' . $subDirName;
if (!is_dir($subDir)) {
    if (!mkdir($subDir, 0775, true)) {
        exit("Failed to create subject folder. Check permissions.");
    }
}

// Safe filename
$basename = time() . '_' . preg_replace('/[^a-z0-9._-]+/i','_', $_FILES['file']['name']);
$targetAbs = $subDir . '/' . $basename;
$targetRel = 'uploads/' . $subDirName . '/' . $basename;

// Move uploaded file
if (!move_uploaded_file($_FILES['file']['tmp_name'], $targetAbs)) {
    exit("Upload failed. Check folder permissions.");
}

// Insert into DB
$stmt = mysqli_prepare($conn, "INSERT INTO resources (subject, title, file_path) VALUES (?, ?, ?)");
mysqli_stmt_bind_param($stmt, "sss", $subject, $title, $targetRel);
if (!mysqli_stmt_execute($stmt)) {
    @unlink($targetAbs);
    exit("DB error: " . mysqli_error($conn));
}
mysqli_stmt_close($stmt);

// Redirect back to resources page
header("Location: resources.php?subject=" . urlencode($subject));
exit;
