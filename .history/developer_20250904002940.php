<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "puc_portal_db"; // 🔹 তোমার database নাম দাও

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle contact form submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
  <!-- Font Awesome -->
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      background: #f9f9f9;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 900px;
      margin: 40px auto;
      padding: 20px;
    }
    .dev-card {
      text-align: center;
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
      margin-bottom: 30px;
    }
    .dev-photo {
      width: 150px;
      height: 150px;
      border-radius: 50%;
      object-fit: cover;
      margin-bottom: 15px;
      border: 3px solid #0073e6;
    }
    .dev-card h2 {
      margin: 10px 0;
    }
    .dev-socials a {
      margin: 0 10px;
      font-size: 22px;
      color: #0073e6;
      transition: color 0.3s;
    }
    .dev-socials a:hover {
      color: #ff6600;
    }
    .contact-form {
      background: #fff;
      padding: 25px;
      border-radius: 15px;
      box-shadow: 0 6px 15px rgba(0,0,0,0.1);
    }
    .contact-form h3 {
      margin-bottom: 15px;
    }
    .contact-form input, .contact-form textarea {
      width: 100%;
      padding: 12px;
      margin: 10px 0;
      border-radius: 8px;
      border: 1px solid #ddd;
      font-size: 14px;
    }
    .contact-form button {
      padding: 12px 20px;
      background: #0073e6;
      color: #fff;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: background 0.3s;
    }
    .contact-form button:hover {
      background: #005bb5;
    }
    .alert {
      padding: 10px;
      margin-bottom: 15px;
      border-radius: 8px;
      font-size: 14px;
    }
    .success {
      background: #d4edda;
      color: #155724;
    }
    .error {
      background: #f8d7da;
      color: #721c24;
    }
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
      <form method="POST" action="">
        <input type="text" name="name" placeholder="Your Name" required>
        <input type="email" name="email" placeholder="Your Email" required>
        <textarea name="message" rows="4" placeholder="Your Message..." required></textarea>
        <button type="submit">Send Message</button>
      </form>
    </div>

  </div>
</body>
</html>
