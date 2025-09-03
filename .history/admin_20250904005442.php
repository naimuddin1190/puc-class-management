<?php
session_start();
require "config.php";

// Logout
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: admin.php");
    exit();
}

// Handle login
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['pin'])) {
    if ($_POST['pin'] === $ADMIN_PIN) {
        $_SESSION['admin'] = true;
    } else {
        $login_error = "❌ Invalid PIN!";
    }
}

// If logged in → Handle delete
if (isset($_SESSION['admin']) && $_SESSION['admin'] === true) {
    if (isset($_GET['delete'])) {
        $id = intval($_GET['delete']);
        $conn->query("DELETE FROM developer_messages WHERE id=$id");
        header("Location: admin.php");
        exit();
    }
    $result = $conn->query("SELECT * FROM developer_messages ORDER BY created_at DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Admin • Developer Messages</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="style.css">
<style>
  /* Font & Color Fixes */
  body, h1, h2, h3, h4, h5, h6, p, a, li, input, textarea, button {
      font-family: 'Poppins', sans-serif;
      color: #000 !important;
      font-weight: 600 !important;
      opacity: 1 !important;
  }
  
  .nav .menu a:hover { color:#ffcc00 !important; }
  .footer p, .footer .socials a { color:#fff !important; font-weight:600; }
  input, textarea { color:#000 !important; opacity:1; }

  .container {max-width:1000px; margin:40px auto; background:#fff; padding:25px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.1);}
  table {width:100%; border-collapse:collapse; margin-top:20px;}
  th, td {padding:12px; border-bottom:1px solid #ddd; font-size:14px;}
  th {background:#0073e6; color:#fff;}
  tr:hover {background:#f1f1f1;}
  .btn {padding:6px 12px; border:none; border-radius:6px; color:#fff; font-weight:600; cursor:pointer;}
  .btn-delete {background:#e74c3c;}
  .btn-delete:hover {background:#c0392b;}
  .btn-logout {background:#fff; margin-bottom:20px;}
  .btn-logout:hover {background:green;}
  .login-box {text-align:center; margin-top:50px;}
  .login-box input {padding:12px; font-size:14px; border:1px solid #ddd; border-radius:8px; width:200px;}
  .login-box button {padding:12px 20px; background:#0073e6; color:#fff; border:none; border-radius:8px; cursor:pointer; margin-top:10px;}
  .alert {margin:15px auto; padding:10px; border-radius:8px; width:250px; font-size:14px;}
  .error {background:#f8d7da; color:#721c24;}
</style>
</head>
<body>

<!-- NAVBAR -->
<nav class="nav">
  <div class="nav-inner container">
    <div class="brand"><i class="fa-solid fa-graduation-cap"></i> PUC CSE-D</div>
    <div class="menu">
      <a href="index.html#home">Home</a>
      <a href="index.html#routine">Routine</a>
      <a href="index.html#notice">Notice</a>
      <a href="index.html#contact">Contact</a>
      <a href="resources.php">Resources</a>
      <a href="developer.php">Developer</a>
    </div>
  </div>
</nav>

<div class="container">
<?php if (!isset($_SESSION['admin'])): ?>
  <div class="login-box">
    <h2><i class="fa-solid fa-lock"></i> Admin Login</h2>
    <?php if(isset($login_error)) echo "<div class='alert error'>$login_error</div>"; ?>
    <form method="POST">
      <input type="password" name="pin" placeholder="Enter Admin PIN" required><br>
      <button type="submit">Login</button>
    </form>
  </div>
<?php else: ?>
  <h2><i class="fa-solid fa-inbox"></i> Developer Messages</h2>
  <a href="admin.php?logout=1" class="btn btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
  <?php if ($result->num_rows > 0): ?>
  <table>
    <tr><th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Action</th></tr>
    <?php while($row = $result->fetch_assoc()): ?>
    <tr>
      <td><?php echo $row['id']; ?></td>
      <td><?php echo htmlspecialchars($row['name']); ?></td>
      <td><?php echo htmlspecialchars($row['email']); ?></td>
      <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
      <td><?php echo $row['created_at']; ?></td>
      <td>
        <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete" onclick="return confirm('Are you sure?');">
          <i class="fa-solid fa-trash"></i> Delete
        </a>
      </td>
    </tr>
    <?php endwhile; ?>
  </table>
  <?php else: ?>
    <p>No messages found.</p>
  <?php endif; ?>
<?php endif; ?>
</div>

<!-- FOOTER -->
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
