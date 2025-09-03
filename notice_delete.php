<?php
session_start();
include 'db.php';

// Only CR
if(empty($_SESSION['is_cr'])) {
    exit("Access denied.");
}

if(empty($_POST['id'])) exit("Invalid request.");

$id = (int)$_POST['id'];

// Find subject of notice
$stmt = mysqli_prepare($conn, "SELECT subject FROM notices WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if(!$row) exit("Notice not found.");

// Delete notice
$stmt = mysqli_prepare($conn, "DELETE FROM notices WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
mysqli_stmt_close($stmt);

// Redirect back to subject tab
header("Location: resources.php?subject=" . urlencode($row['subject']));
exit;
