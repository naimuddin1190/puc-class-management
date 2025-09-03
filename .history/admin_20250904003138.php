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
  <title>Admin - Developer Messages</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {font-family: 'Poppins', sans-serif; background:#f5f6fa; margin:0; padding:0;}
    .container {max-width: 900px; margin: 60px auto; background:#fff; padding:25px; border-radius:12px; box-shadow:0 6px 15px rgba(0,0,0,0.1);}
    h2 {margin-bottom:20px;}
    table {width:100%; border-collapse:collapse;}
    th, td {padding:12px; border-bottom:1px solid #ddd; text-align:left; font-size:14px;}
    th {background:#0073e6; color:#fff;}
    tr:hover {background:#f1f1f1;}
    .btn {padding:6px 12px; border:none; border-radius:6px; cursor:pointer; font-size:13px; text-decoration:none;}
    .btn-delete {background:#e74c3c; color:#fff;}
    .btn-delete:hover {background:#c0392b;}
    .btn-logout {background:#555; color:#fff;}
    .btn-logout:hover {background:#333;}
    .login-box {text-align:center;}
    .login-box input {padding:12px; font-size:14px; border:1px solid #ddd; border-radius:8px; width:200px;}
    .login-box button {padding:12px 20px; background:#0073e6; color:#fff; border:none; border-radius:8px; cursor:pointer; margin-top:10px;}
    .alert {margin:15px auto; padding:10px; border-radius:8px; width:250px; font-size:14px;}
    .error {background:#f8d7da; color:#721c24;}
  </style>
</head>
<body>
  <div class="container">
    <?php if (!isset($_SESSION['admin'])): ?>
      <!-- Login Form -->
      <div class="login-box">
        <h2><i class="fa-solid fa-lock"></i> Admin Login</h2>
        <?php if(isset($login_error)) echo "<div class='alert error'>$login_error</div>"; ?>
        <form method="POST">
          <input type="password" name="pin" placeholder="Enter Admin PIN" required><br>
          <button type="submit">Login</button>
        </form>
      </div>
    <?php else: ?>
      <!-- Messages Table -->
      <h2><i class="fa-solid fa-inbox"></i> Developer Messages</h2>
      <p><a href="admin.php?logout=1" class="btn btn-logout"><i class="fa-solid fa-right-from-bracket"></i> Logout</a></p>
      <?php if ($result->num_rows > 0): ?>
        <table>
          <tr>
            <th>ID</th><th>Name</th><th>Email</th><th>Message</th><th>Date</th><th>Action</th>
          </tr>
          <?php while($row = $result->fetch_assoc()): ?>
          <tr>
            <td><?php echo $row['id']; ?></td>
            <td><?php echo htmlspecialchars($row['name']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
            <td><?php echo $row['created_at']; ?></td>
            <td>
              <a href="admin.php?delete=<?php echo $row['id']; ?>" class="btn btn-delete"
                 onclick="return confirm('Are you sure you want to delete this message?');">
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
</body>
</html>
