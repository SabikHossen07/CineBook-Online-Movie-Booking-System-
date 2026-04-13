<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'db.php';

$message = "";
$message_color = "#16a34a";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if (empty($full_name) || empty($email) || empty($password) || empty($confirm_password)) {
        $message = "All fields are required.";
        $message_color = "#dc2626";
    } elseif ($password !== $confirm_password) {
        $message = "Passwords do not match.";
        $message_color = "#dc2626";
    } else {
        $check_sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($check_sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $message = "Email already registered.";
            $message_color = "#dc2626";
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $role = "customer";

            $sql = "INSERT INTO users (full_name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if (!$stmt) {
                die("Prepare failed: " . $conn->error);
            }

            $stmt->bind_param("ssss", $full_name, $email, $hashed_password, $role);

            if ($stmt->execute()) {
                $message = "Registration successful!";
                $message_color = "#16a34a";
            } else {
                $message = "Insert failed: " . $stmt->error;
                $message_color = "#dc2626";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Register</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

  <header>
    <nav class="navbar">
      <div class="logo">CineBook</div>
      <ul class="nav-links">
        <li><a href="index.php">Home</a></li>
        <li><a href="movies.php">Movies</a></li>
        <li><a href="mybookings.php">My Bookings</a></li>

        <?php if (isset($_SESSION['user_name'])): ?>
          <li><span style="color:#facc15;">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
          <li><a href="logout.php" class="btn-nav">Logout</a></li>
        <?php else: ?>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php" class="btn-nav">Register</a></li>
        <?php endif; ?>
      </ul>
    </nav>
  </header>

  <section class="page-banner movies-banner">
    <h1>Register</h1>
    <p>Create your CineBook account and start booking tickets with ease.</p>
  </section>

  <section class="auth-section">
    <div class="auth-card premium-auth-card">
      <h1>Create Account</h1>
      <p class="auth-subtitle">Join CineBook and enjoy a smooth movie ticket booking experience.</p>

      <?php if (!empty($message)): ?>
        <p style="text-align:center; color:white; background:<?php echo $message_color; ?>; padding:12px; border-radius:8px; margin-bottom:15px; font-weight:bold;">
          <?php echo htmlspecialchars($message); ?>
        </p>
      <?php endif; ?>

      <form class="auth-form" method="POST" action="register.php">
        <label for="full_name">Full Name</label>
        <input type="text" id="full_name" name="full_name" placeholder="Enter your full name" required />

        <label for="email">Email</label>
        <input type="email" id="email" name="email" placeholder="Enter your email" required />

        <label for="password">Password</label>
        <input type="password" id="password" name="password" placeholder="Create a password" required />

        <label for="confirm_password">Confirm Password</label>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" required />

        <button type="submit" class="auth-btn">Create Account</button>
      </form>

      <p class="auth-link-text">
        Already have an account?
        <a href="login.php">Login here</a>
      </p>
    </div>
  </section>

  <footer>
    <p>© 2026 CineBook. All rights reserved.</p>
  </footer>

</body>
</html>