<?php
session_start();
include 'db.php';

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    if (empty($email) || empty($password)) {
        $message = "Email and password are required.";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);

        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_email'] = $user['email'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                if ($user['role'] === 'admin') {
                    header("Location: admin_dashboard.php");
                    exit();
                } elseif ($user['role'] === 'manager') {
                    header("Location: manager_dashboard.php");
                    exit();
                } else {
                    header("Location: index.php");
                    exit();
                }
            } else {
                $message = "Incorrect password.";
            }
        } else {
            $message = "Email not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Login</title>
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
  <h1>Login</h1>
  <p>Access your CineBook account and manage your bookings.</p>
</section>

<section class="auth-section">
  <div class="auth-card premium-auth-card">
    <h1>Welcome Back</h1>

    <?php if (!empty($message)): ?>
      <p style="text-align:center; color:white; background:#dc2626; padding:12px; border-radius:8px; margin-bottom:15px; font-weight:bold;">
        <?php echo htmlspecialchars($message); ?>
      </p>
    <?php endif; ?>

    <form class="auth-form" method="POST" action="login.php">
      <label for="email">Email</label>
      <input type="email" id="email" name="email" placeholder="Enter your email" required />

      <label for="password">Password</label>
      <input type="password" id="password" name="password" placeholder="Enter your password" required />

      <button type="submit" class="auth-btn">Login</button>
    </form>

    <p class="auth-link-text">
      Don’t have an account?
      <a href="register.php">Register here</a>
    </p>
  </div>
</section>

<footer>
  <p>© 2026 CineBook. All rights reserved.</p>
</footer>

</body>
</html>