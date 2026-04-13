<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<header>
  <nav class="navbar">
    <div class="logo">CineBook Manager</div>
    <ul class="nav-links">
      <li><span style="color:#facc15;">Hi, <?php echo $_SESSION['user_name']; ?></span></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="page-banner movies-banner">
  <h1>Manager Dashboard</h1>
  <p>Manage shows, seats, and bookings</p>
</section>

<section style="padding:40px; text-align:center; color:white;">
    <h2>Manager Controls</h2>
    <br>

   <a href="manager_showtimes.php" class="book-now-btn">Manage Showtimes</a>
   <a href="manager_seats.php" class="book-now-btn">Manage Seats</a>
   <a href="manager_bookings.php" class="book-now-btn">View Bookings</a>
</section>

</body>
</html>