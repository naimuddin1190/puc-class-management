<?php
session_start();
include 'db.php';

// --- CR login/logout ---
$login_error = '';
if (isset($_POST['cr_login_pin'])) {
    if ($_POST['cr_login_pin'] === "4242") { // CR PIN
        $_SESSION['is_cr'] = true;
        header("Location: resources.php");
        exit;
    } else {
        $login_error = "Wrong PIN.";
    }
}

if (isset($_GET['logout']) && $_GET['logout'] === '1') {
    unset($_SESSION['is_cr']);
    header("Location: resources.php");
    exit;
}

// --- Handle CR subject management / resource / notice ---
if(!empty($_SESSION['is_cr'])) {

    // Add subject
    if(isset($_POST['add_subject']) && !empty($_POST['new_subject'])) {
        $name = trim($_POST['new_subject']);
        $stmt = mysqli_prepare($conn, "INSERT IGNORE INTO subjects (name) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: resources.php");
        exit;
    }

    // Delete subject and its resources/notices
    if(isset($_POST['delete_subject']) && !empty($_POST['del_subject'])) {
        $sub = trim($_POST['del_subject']);
        $res_files = mysqli_query($conn, "SELECT file_path FROM resources WHERE subject='" . mysqli_real_escape_string($conn,$sub) . "'");
        while($r = mysqli_fetch_assoc($res_files)) {
            $abs = __DIR__ . '/' . $r['file_path'];
            if(is_file($abs)) @unlink($abs);
        }
        mysqli_query($conn, "DELETE FROM resources WHERE subject='" . mysqli_real_escape_string($conn,$sub) . "'");
        mysqli_query($conn, "DELETE FROM notices WHERE subject='" . mysqli_real_escape_string($conn,$sub) . "'");
        mysqli_query($conn, "DELETE FROM subjects WHERE name='" . mysqli_real_escape_string($conn,$sub) . "'");
        header("Location: resources.php");
        exit;
    }

    // Handle notice upload
    if(isset($_POST['post_notice']) && !empty($_POST['notice_title']) && !empty($_POST['notice_content'])) {
        $subj = trim($_POST['notice_subject']);
        $title = trim($_POST['notice_title']);
        $content = trim($_POST['notice_content']);
        $stmt = mysqli_prepare($conn, "INSERT INTO notices (subject, title, content) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sss", $subj, $title, $content);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);
        header("Location: resources.php?subject=".urlencode($subj));
        exit;
    }
}

// --- Load subjects & current tab ---
$sub_result = mysqli_query($conn, "SELECT name FROM subjects ORDER BY name ASC");
$SUBJECTS = [];
while($row = mysqli_fetch_assoc($sub_result)) { $SUBJECTS[] = $row['name']; }
$current = isset($_GET['subject']) && in_array($_GET['subject'], $SUBJECTS) ? $_GET['subject'] : ($SUBJECTS[0] ?? '');

// --- Fetch resources ---
$stmt = mysqli_prepare($conn, "SELECT id, title, file_path, uploaded_at FROM resources WHERE subject=? ORDER BY uploaded_at DESC");
mysqli_stmt_bind_param($stmt, "s", $current);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);

// --- Fetch notices ---
$notice_stmt = mysqli_prepare($conn, "SELECT id, title, content, uploaded_at FROM notices WHERE subject=? ORDER BY uploaded_at DESC");
mysqli_stmt_bind_param($notice_stmt, "s", $current);
mysqli_stmt_execute($notice_stmt);
$notice_res = mysqli_stmt_get_result($notice_stmt);
$notices = mysqli_fetch_all($notice_res, MYSQLI_ASSOC);
mysqli_stmt_close($notice_stmt);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Resources • PUC Section-D</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
.tabs { display:flex; flex-wrap:wrap; gap:8px; margin: 10px 0 20px;}
.tab { padding:8px 12px; border:1px solid #444; border-radius:8px; text-decoration:none; color:var(--text); background:#0f1838a8;}
.tab.active { border-color: var(--primary); box-shadow:0 0 0 1px var(--primary) inset;}
.res-list { display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:14px; }
.res-item { background: var(--card); padding:14px; border-radius:10px; }
.res-meta { font-size:12px; opacity:.8; margin-top:6px; }
.res-actions { display:flex; gap:8px; margin-top:10px; }
.admin-panel { background: #0d1533; padding:14px; border-radius:10px; margin-top:18px;}
.admin-panel input, .admin-panel select, .admin-panel textarea { margin:6px 6px 6px 0; padding:8px; border-radius:8px; border:1px solid #444; background:#0b1020; color:var(--text);}
.admin-panel .btn { margin-top:8px; }
.pin-row { display:flex; gap:8px; align-items:center; }
.notice { color: #aaa; font-size: 13px; margin-top:6px; }
.edit-btn { background:#1d4ed8; color:#fff; border:none; cursor:pointer; padding:6px 10px; border-radius:8px;}
</style>
</head>
<body>
<nav class="nav">
<div class="nav-inner container">
<div class="brand"><i class="fa-solid fa-graduation-cap"></i> PUC CSE-D</div>
<div class="menu">
<a href="index.html#home">Home</a>
<a href="index.html#routine">Routine</a>
<a href="index.html#notice">Notice</a>
<a href="index.html#contact">Contact</a>
<a href="resources.php" class="active">Resources</a>
<a href="de.php" class="active">Developer</a>
</div>
</div>
</nav>

<section class="section container">
<h2>📚 Subject Resources</h2>

<!-- Subject tabs -->
<div class="tabs">
<?php foreach($SUBJECTS as $s): ?>
  <a class="tab <?= $current===$s?'active':'' ?>" href="?subject=<?= urlencode($s) ?>"><?= htmlspecialchars($s) ?></a>
<?php endforeach; ?>
</div>

<!-- Resource list -->
<div class="res-list">
<?php if(empty($rows)): ?>
  <div class="res-item">No resources yet for <b><?= htmlspecialchars($current) ?></b>.</div>
<?php else: foreach($rows as $r): ?>
  <div class="res-item">
    <div class="res-title">
      <i class="fa-regular fa-file-lines"></i>
      <a href="<?= htmlspecialchars($r['file_path']) ?>" target="_blank" style="color:var(--text); text-decoration:none;">
        <?= htmlspecialchars($r['title']) ?>
      </a>
    </div>
    <div class="res-meta">Uploaded: <?= date('M d, Y h:i A', strtotime($r['uploaded_at'])) ?></div>
    <?php if(!empty($_SESSION['is_cr'])): ?>
      <form class="res-actions" method="post" action="resource_delete.php" onsubmit="return confirm('Delete this file?');">
        <input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
        <button class="delete-btn" type="submit" style="padding:6px 10px; border-radius:8px;">
          <i class="fa-solid fa-trash"></i> Delete
        </button>
      </form>
    <?php endif; ?>
  </div>
<?php endforeach; endif; ?>
</div>

<!-- Notices list -->
<h3>📌 Notices</h3>
<div class="res-list">
<?php if(empty($notices)): ?>
  <div class="res-item">No notices yet for <b><?= htmlspecialchars($current) ?></b>.</div>
<?php else: foreach($notices as $n): ?>
  <div class="res-item">
    <div class="res-title"><i class="fa-solid fa-bullhorn"></i> <?= htmlspecialchars($n['title']) ?></div>
    <div class="res-meta"><?= nl2br(htmlspecialchars($n['content'])) ?></div>
    <div class="res-meta">Posted: <?= date('M d, Y h:i A', strtotime($n['uploaded_at'])) ?></div>
    <?php if(!empty($_SESSION['is_cr'])): ?>
      <div class="res-actions">
        <form method="post" action="notice_delete.php" onsubmit="return confirm('Delete this notice?');" style="display:inline-block;">
          <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
          <button class="delete-btn" type="submit" style="padding:6px 10px; border-radius:8px;">
            <i class="fa-solid fa-trash"></i> Delete
          </button>
        </form>
        <form method="get" action="notice_edit.php" style="display:inline-block;">
          <input type="hidden" name="id" value="<?= (int)$n['id'] ?>">
          <button class="edit-btn" type="submit">
            <i class="fa-solid fa-pen-to-square"></i> Edit
          </button>
        </form>
      </div>
    <?php endif; ?>
  </div>
<?php endforeach; endif; ?>
</div>

<!-- CR login / panel -->
<?php if(empty($_SESSION['is_cr'])): ?>
<div class="admin-panel" style="margin-top:24px;">
<h3>CR Login</h3>
<?php if($login_error): ?><div style="color:#ef4444; margin-bottom:6px;"><?= htmlspecialchars($login_error) ?></div><?php endif; ?>
<form method="post">
  <div class="pin-row">
    <input type="password" name="cr_login_pin" placeholder="Enter CR PIN" required>
    <button class="btn" type="submit">Login</button>
  </div>
  <div class="notice">Only CR can upload or delete resources/notices.</div>
</form>
</div>
<?php else: ?>
<div class="admin-panel">
<div style="display:flex; justify-content:space-between; align-items:center;">
<h3>Upload New Resource / Post Notice</h3>
<a class="btn" href="resources.php?logout=1">Logout CR</a>
</div>

<!-- File Upload -->
<form method="post" action="resource_upload.php" enctype="multipart/form-data">
<select name="subject" required>
<?php foreach($SUBJECTS as $s): ?>
<option value="<?= htmlspecialchars($s) ?>" <?= $current===$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option>
<?php endforeach; ?>
</select>
<input type="text" name="title" placeholder="File Title" required>
<input type="file" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z,.txt" required>
<button class="btn" type="submit"><i class="fa-solid fa-cloud-arrow-up"></i> Upload File</button>
<div class="notice">Allowed: pdf, ppt/pptx, doc/docx, xls/xlsx, zip/rar/7z, txt (≤20MB)</div>
</form>

<!-- Notice Post -->
<form method="post">
<select name="notice_subject" required>
<?php foreach($SUBJECTS as $s): ?>
<option value="<?= htmlspecialchars($s) ?>" <?= $current===$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option>
<?php endforeach; ?>
</select>
<input type="text" name="notice_title" placeholder="Notice Title" required>
<textarea name="notice_content" rows="4" placeholder="Type your notice here..." required></textarea>
<button class="btn" type="submit" name="post_notice">Post Notice</button>
</form>

<!-- Subject Management -->
<div style="margin-top:20px;">
<h3>Manage Subjects</h3>
<form method="post" style="margin-bottom:12px;">
<input type="text" name="new_subject" placeholder="New Subject" required>
<button type="submit" name="add_subject">Add</button>
</form>

<form method="post">
<select name="del_subject" required>
<option value="">Select Subject to Delete</option>
<?php foreach($SUBJECTS as $s): ?>
<option value="<?= htmlspecialchars($s) ?>"><?= htmlspecialchars($s) ?></option>
<?php endforeach; ?>
</select>
<button type="submit" name="delete_subject" onclick="return confirm('Delete this subject and all its resources/notices?');">Delete</button>
</form>
</div>
</div>
<?php endif; ?>
</section>

<footer class="footer">
<p>© 2025 Premier University Chittagong • CSE 42nd Batch</p>
<div class="socials">
<a href="https://facebook.com"><i class="fab fa-facebook"></i></a>
<a href="https://instagram.com"><i class="fab fa-instagram"></i></a>
<a href="https://linkedin.com"><i class="fab fa-linkedin"></i></a>
<a href="https://chat.whatsapp.com/your-group-link"><i class="fab fa-whatsapp"></i></a>
</div>
</footer>
</body>
</html>
