<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM movies ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Manage Showtimes</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(90deg, #00143c, #071a40, #00143c);
      color: white;
      min-height: 100vh;
    }

    .navbar {
      background: #0f172a;
      padding: 22px 45px;
      display: flex;
      justify-content: space-between;
      align-items: center;
      box-shadow: 0 6px 20px rgba(0,0,0,0.25);
    }

    .logo {
      font-size: 30px;
      font-weight: bold;
      color: #facc15;
      text-decoration: none;
    }

    .nav-links {
      display: flex;
      align-items: center;
      gap: 28px;
    }

    .nav-links a {
      text-decoration: none;
      color: white;
      font-size: 17px;
      transition: 0.3s ease;
    }

    .nav-links a:hover,
    .nav-links .active {
      color: #facc15;
    }

    .logout-btn {
      background: #facc15;
      color: #111827 !important;
      padding: 11px 20px;
      border-radius: 10px;
      font-weight: bold;
    }

    .manager-wrap {
      max-width: 1280px;
      margin: 40px auto;
      padding: 0 20px 40px;
    }

    .page-head {
      background: linear-gradient(135deg, #111827, #1e293b);
      border-radius: 24px;
      padding: 32px;
      box-shadow: 0 14px 30px rgba(0,0,0,0.22);
      border: 1px solid rgba(250, 204, 21, 0.12);
    }

    .head-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      gap: 16px;
      flex-wrap: wrap;
    }

    .page-title {
      font-size: 56px;
      font-weight: 800;
      color: #facc15;
      margin-bottom: 8px;
    }

    .page-subtitle {
      font-size: 18px;
      color: #d1d5db;
    }

    .btn-add {
      background: #22c55e;
      color: white;
      text-decoration: none;
      padding: 14px 22px;
      border-radius: 12px;
      font-weight: 700;
      display: inline-block;
      transition: 0.3s ease;
    }

    .btn-add:hover {
      transform: translateY(-2px);
      opacity: 0.92;
    }

    .showtime-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(390px, 1fr));
      gap: 22px;
      margin-top: 28px;
    }

    .showtime-card {
      background: rgba(8, 23, 56, 0.95);
      border-radius: 24px;
      padding: 28px;
      border: 1px solid rgba(250, 204, 21, 0.15);
      box-shadow: 0 12px 28px rgba(0,0,0,0.18);
      transition: 0.3s ease;
    }

    .showtime-card:hover {
      transform: translateY(-4px);
    }

    .movie-title {
      font-size: 28px;
      font-weight: 800;
      color: #facc15;
      margin-bottom: 18px;
    }

    .showtime-info p {
      margin: 12px 0;
      font-size: 17px;
      line-height: 1.6;
      color: #f9fafb;
    }

    .showtime-info strong {
      color: #ffffff;
    }

    .showtime-actions {
      margin-top: 22px;
      display: flex;
      gap: 12px;
      flex-wrap: wrap;
    }

    .btn-edit,
    .btn-delete {
      text-decoration: none;
      padding: 11px 18px;
      border-radius: 10px;
      font-weight: 700;
      display: inline-block;
      transition: 0.3s ease;
    }

    .btn-edit {
      background: #facc15;
      color: #111827;
    }

    .btn-delete {
      background: #ef4444;
      color: white;
    }

    .footer {
      text-align: center;
      padding: 26px 10px 34px;
      color: #e5e7eb;
      font-size: 15px;
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <a href="manager_dashboard.php" class="logo">CineBook Manager</a>
    <div class="nav-links">
      <a href="manager_dashboard.php">Dashboard</a>
      <a href="manager_showtimes.php" class="active">Showtimes</a>
      <a href="manager_seats.php">Seats</a>
      <a href="manager_bookings.php">Bookings</a>
      <a href="#">Hi, Manager</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <div class="manager-wrap">
    <div class="page-head">
      <div class="head-row">
        <div>
          <h1 class="page-title">Movie Showtimes</h1>
          <p class="page-subtitle">Manage movie schedules, halls, dates, and ticket pricing.</p>
        </div>
        <a href="add_showtime.php" class="btn-add">+ Add Showtime</a>
      </div>

      <div class="showtime-grid">
        <?php while ($row = $result->fetch_assoc()): ?>
          <div class="showtime-card">
            <h2 class="movie-title"><?php echo htmlspecialchars($row['title']); ?></h2>

            <div class="showtime-info">
              <p><strong>Genre:</strong> <?php echo htmlspecialchars($row['genre']); ?></p>
              <p><strong>Duration:</strong> <?php echo htmlspecialchars($row['duration']); ?></p>
              <p><strong>Language:</strong> <?php echo htmlspecialchars($row['language']); ?></p>
              <p><strong>Rating:</strong> <?php echo htmlspecialchars($row['rating']); ?></p>
              <p><strong>Date:</strong> <?php echo htmlspecialchars($row['show_date']); ?></p>
              <p><strong>Showtimes:</strong> <?php echo htmlspecialchars($row['showtimes']); ?></p>
              <p><strong>Hall:</strong> <?php echo htmlspecialchars($row['hall']); ?></p>
              <p><strong>Price:</strong> ৳<?php echo number_format((float)$row['price'], 2); ?></p>
            </div>

            <div class="showtime-actions">
              <a href="edit_showtime.php?id=<?php echo $row['id']; ?>" class="btn-edit">Edit</a>
              <a href="delete_showtime.php?id=<?php echo $row['id']; ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this showtime?')">Delete</a>
            </div>
          </div>
        <?php endwhile; ?>
      </div>
    </div>
  </div>

  <div class="footer">
    © 2026 CineBook. All rights reserved.
  </div>

</body>
</html>