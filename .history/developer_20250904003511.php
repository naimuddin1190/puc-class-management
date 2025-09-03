<?php
session_start();
require "config.php";

// Handle contact form submit
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['contact_submit'])) {
    $name = $conn->real_escape_string($_POST['name']);
    $email = $conn->real_escape_string($_POST['email']);
    $message = $conn->real_escape_string($_POST['message']);

    $sql = "INSERT INTO developer_messages (name, email, message) VALUES ('$name','$email','$message')";
    if ($conn->query($sql) === TRUE) {
        $success = "✅ Your message has been sent successfully!";
    } else {
        $error = "❌ Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Developer Info</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {font-family: 'Poppins', sans-serif; background:#f9f9f9; margin:0; padding:0;}
    .container {max-width: 900px; margin:40px auto; padding:20px;}
    .dev-card {text-align:center; background:#fff; padding:25px; border-radius:15px; box-shadow:0 6px 15px rgba(0,0,0,0.1); margin-bottom:30px;}
    .dev-photo {width:150px; height:150px; border-radius:50%; object-fit:cover; margin-bottom:15px; border:3px solid #0073e6;}
    .dev-socials a {margin:0 10px; font-size:22px; color:#0073e6; transition:color 0.3s;}
    .dev-socials a:hover {color:#ff6600;}
    .contact-form {background:#fff; padding:25px; border-radius:15px; box-shadow:0 6px 15px rgba(0,0,0,0.1); margin-bottom:20px;}
    .contact-form input, .contact-form textarea {width:100%; padding:12px; margin:10px 0; border-radius:8px; border:1px solid #ddd; font-size:14px;}
    .contact-form button {padding:12px 20px; background:#0073e6; color:#fff; border:none; border-radius:8px; cursor:pointer; transition:background 0.3s;}
    .contact-form button:hover {background:#005bb5;}
    .alert {padding:10px; margin-bottom:15px; border-radius:8px; font-size:14px;}
    .success {background:#d4edda; color:#155724;}
    .error {background:#f8d7da; color:#721c24;}
    .admin-btn {display:block; width:200px; margin:20px auto; padding:12px; background:#28a745; color:#fff; text-align:center; border-radius:8px; text-decoration:none; font-weight:600;}
    .admin-btn:hover {background:#218838;}
  </style>
</head>
<body>
<div class="container">

  <!-- Developer Info -->
  <div class="dev-card">
    <img src="images/developer.jpg" alt="Developer Photo" class="dev-photo">
    <h2>Md Naim Uddin Mozumdar</h2>
    <p>Full Stack Web & Android Developer • CSE, Premier University</p>
    <div class="dev-socials">
      <a href="https://facebook.com/yourprofile" target="_blank"><i class="fab fa-facebook"></i></a>
      <a href="https://github.com/yourgithub" target="_blank"><i class="fab fa-github"></i></a>
      <a href="https://linkedin.com/in/yourlinkedin" target="_blank"><i class="fab fa-linkedin"></i></a>
    </div>
  </div>

  <!-- Contact Form -->
  <div class="contact-form">
    <h3>Contact Developer</h3>
    <?php if(isset($success)) echo "<div class='alert success'>$success</div>"; ?>
    <?php if(isset($error)) echo "<div class='alert error'>$error</div>"; ?>
    <form method="POST">
      <input type="text" name="name" placeholder="Your Name" required>
      <input type="email" name="email" placeholder="Your Email" required>
      <textarea name="message" rows="4" placeholder="Your Message..." required></textarea>
      <button type="submit" name="contact_submit">Send Message</button>
    </form>
  </div>

  <!-- Admin Button -->
  <a href="admin.php" class="admin-btn"><i class="fa-solid fa-envelope"></i> View Messages (Admin)</a>

</div>
</body>
</html>
