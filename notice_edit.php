 <?php
session_start();
include 'db.php';

if(empty($_SESSION['is_cr'])){
    header("Location: resources.php");
    exit;
}

// Get notice id
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch notice
$stmt = mysqli_prepare($conn, "SELECT id, subject, title, content FROM notices WHERE id=?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$notice = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if(!$notice){
    header("Location: resources.php");
    exit;
}

// Handle update
if(isset($_POST['update_notice'])){
    $title = trim($_POST['notice_title']);
    $content = trim($_POST['notice_content']);
    $stmt = mysqli_prepare($conn, "UPDATE notices SET title=?, content=? WHERE id=?");
    mysqli_stmt_bind_param($stmt, "ssi", $title, $content, $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    header("Location: resources.php?subject=".urlencode($notice['subject']));
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Edit Notice</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<style>
body{font-family:'Poppins',sans-serif; background:#0b1020; color:#fff; padding:20px;}
input, textarea, button{padding:8px; margin:6px 0; border-radius:8px; border:1px solid #444; background:#0d1533; color:#fff; width:100%;}
button{cursor:pointer;}
</style>
</head>
<body>
<h2>Edit Notice</h2>
<form method="post">
<input type="text" name="notice_title" value="<?= htmlspecialchars($notice['title']) ?>" required>
<textarea name="notice_content" rows="6" required><?= htmlspecialchars($notice['content']) ?></textarea>
<button type="submit" name="update_notice"><i class="fa-solid fa-floppy-disk"></i> Update Notice</button>
</form>
<a href="resources.php?subject=<?= urlencode($notice['subject']) ?>" style="color:#60a5fa; text-decoration:none; margin-top:10px; display:inline-block;">← Back to Resources</a>
</body>
</html>
