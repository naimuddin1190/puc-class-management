<?php
include 'db.php';

// Allowed subjects (চাইলে বাড়াতে/কমাতে পারো)
$SUBJECTS = ['SE', 'CCS', 'PME', 'SEL', 'Other'];

// CR login (simple PIN)
$login_error = '';
if (isset($_POST['cr_login_pin'])) {
  if ($_POST['cr_login_pin'] === ""CR_PIN"") {
    $_SESSION['is_cr'] = true;
    header("Location: resources.php");
    exit;
  } else {
    $login_error = "Wrong PIN.";
  }
}

// CR logout
if (isset($_GET['logout']) && $_GET['logout'] === '1') {
  $_SESSION['is_cr'] = false;
  unset($_SESSION['is_cr']);
  header("Location: resources.php");
  exit;
}

// Current subject filter
$current = isset($_GET['subject']) && in_array($_GET['subject'], $SUBJECTS) ? $_GET['subject'] : 'SE';

// Fetch resources
$stmt = mysqli_prepare($conn, "SELECT id, title, file_path, uploaded_at FROM resources WHERE subject=? ORDER BY uploaded_at DESC");
mysqli_stmt_bind_param($stmt, "s", $current);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
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
    /* ছোট্ট কিছু UI টিউন (তোমার থিম রেখে) */
    .tabs { display:flex; flex-wrap:wrap; gap:8px; margin: 10px 0 20px;}
    .tab {
      padding:8px 12px; border:1px solid #444; border-radius:8px; text-decoration:none; color:var(--text);
      background:#0f1838a8;
    }
    .tab.active { border-color: var(--primary); box-shadow:0 0 0 1px var(--primary) inset;}
    .res-list { display:grid; grid-template-columns: repeat(auto-fit,minmax(260px,1fr)); gap:14px; }
    .res-item { background: var(--card); padding:14px; border-radius:10px; }
    .res-meta { font-size:12px; opacity:.8; margin-top:6px; }
    .res-actions { display:flex; gap:8px; margin-top:10px; }
    .admin-panel { background: #0d1533; padding:14px; border-radius:10px; margin-top:18px;}
    .admin-panel input, .admin-panel select { margin:6px 6px 6px 0; padding:8px; border-radius:8px; border:1px solid #444; background:#0b1020; color:var(--text);}
    .admin-panel .btn { margin-top:8px; }
    .pin-row { display:flex; gap:8px; align-items:center; }
    .notice { color: #aaa; font-size: 13px; margin-top:6px; }
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

    <!-- List -->
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
          <div class="res-meta">
            Uploaded: <?= date('M d, Y h:i A', strtotime($r['uploaded_at'])) ?>
          </div>
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

    <!-- CR login / logout + Upload -->
    <?php if(empty($_SESSION['is_cr'])): ?>
      <div class="admin-panel" style="margin-top:24px;">
        <h3>CR Login</h3>
        <?php if($login_error): ?><div style="color:#ef4444; margin-bottom:6px;"><?= htmlspecialchars($login_error) ?></div><?php endif; ?>
        <form method="post">
          <div class="pin-row">
            <input type="password" name="cr_login_pin" placeholder="Enter CR PIN" required>
            <button class="btn" type="submit">Login</button>
          </div>
          <div class="notice">Only CR can upload or delete resources.</div>
        </form>
      </div>
    <?php else: ?>
      <div class="admin-panel">
        <div style="display:flex; justify-content:space-between; align-items:center;">
          <h3>Upload New Resource</h3>
          <a class="btn" href="resources.php?logout=1">Logout CR</a>
        </div>
        <form method="post" action="resource_upload.php" enctype="multipart/form-data">
          <div>
            <select name="subject" required>
              <?php foreach($SUBJECTS as $s): ?>
                <option value="<?= htmlspecialchars($s) ?>" <?= $current===$s?'selected':'' ?>><?= htmlspecialchars($s) ?></option>
              <?php endforeach; ?>
            </select>
            <input type="text" name="title" placeholder="Title (e.g., CT-1 Syllabus, Lecture 05)" required>
            <input type="file" name="file" accept=".pdf,.ppt,.pptx,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z,.txt" required>
          </div>
          <button class="btn" type="submit" style="margin-top:8px;"><i class="fa-solid fa-cloud-arrow-up"></i> Upload</button>
          <div class="notice">Allowed: pdf, ppt/pptx, doc/docx, xls/xlsx, zip/rar/7z, txt (≤ 20MB)</div>
        </form>
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
