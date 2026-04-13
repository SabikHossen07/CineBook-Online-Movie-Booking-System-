<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$sql = "SELECT id, user_email, movie_name, show_date, show_time, seats, total_price, booking_status, created_at 
        FROM bookings 
        ORDER BY id DESC";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Manage Seats</title>
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

    .page-wrap {
      max-width: 1380px;
      margin: 0 auto;
      padding: 40px 20px 30px;
    }

    .hero {
      text-align: center;
      padding: 40px 10px 20px;
    }

    .hero h1 {
      font-size: 62px;
      color: #facc15;
      margin-bottom: 16px;
      font-weight: 800;
    }

    .hero p {
      color: #d1d5db;
      font-size: 18px;
    }

    .panel {
      margin-top: 26px;
      background: linear-gradient(135deg, #111827, #1e293b);
      border-radius: 26px;
      padding: 32px;
      box-shadow: 0 14px 30px rgba(0,0,0,0.22);
      border: 1px solid rgba(250, 204, 21, 0.12);
      overflow-x: auto;
    }

    .panel h2 {
      font-size: 30px;
      color: #facc15;
      margin-bottom: 24px;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      min-width: 1200px;
    }

    thead th {
      text-align: left;
      color: #facc15;
      font-size: 17px;
      padding: 16px 12px;
      border-bottom: 1px solid rgba(255,255,255,0.12);
    }

    tbody td {
      padding: 18px 12px;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      color: #f9fafb;
      font-size: 16px;
      vertical-align: middle;
    }

    tbody tr:hover {
      background: rgba(255,255,255,0.03);
    }

    .status {
      font-weight: 700;
      text-transform: capitalize;
    }

    .status.confirmed { color: #22c55e; }
    .status.cancelled { color: #ef4444; }
    .status.pending { color: #facc15; }

    .action-group {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
    }

    .btn {
      text-decoration: none;
      padding: 8px 14px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 14px;
      display: inline-block;
      transition: 0.3s ease;
      white-space: nowrap;
    }

    .btn:hover {
      transform: translateY(-2px);
      opacity: 0.92;
    }

    .btn-edit {
      background: #facc15;
      color: #111827;
    }

    .btn-confirm {
      background: #22c55e;
      color: white;
    }

    .btn-pending {
      background: #0ea5e9;
      color: white;
    }

    .btn-cancel {
      background: #ef4444;
      color: white;
    }

    .empty-state {
      background: rgba(15, 23, 42, 0.9);
      border: 1px solid rgba(250, 204, 21, 0.15);
      border-radius: 20px;
      padding: 24px;
      text-align: center;
      color: #d1d5db;
      font-size: 18px;
    }

    .footer {
      text-align: center;
      padding: 26px 10px 34px;
      color: #e5e7eb;
      font-size: 15px;
    }

    @media (max-width: 768px) {
      .navbar {
        flex-direction: column;
        gap: 16px;
        padding: 20px;
      }

      .nav-links {
        flex-wrap: wrap;
        justify-content: center;
        gap: 16px;
      }

      .hero h1 {
        font-size: 42px;
      }
    }
  </style>
</head>
<body>

  <nav class="navbar">
    <a href="manager_dashboard.php" class="logo">CineBook Manager</a>
    <div class="nav-links">
      <a href="manager_dashboard.php">Dashboard</a>
      <a href="manager_showtimes.php">Showtimes</a>
      <a href="manager_seats.php" class="active">Seats</a>
      <a href="manager_bookings.php">Bookings</a>
      <a href="#">Hi, Manager</a>
      <a href="logout.php" class="logout-btn">Logout</a>
    </div>
  </nav>

  <div class="page-wrap">
    <div class="hero">
      <h1>Manage Seats</h1>
      <p>Track booked seats and update reservation status from one place.</p>
    </div>

    <div class="panel">
      <h2>Seat Status</h2>

      <?php if ($result && $result->num_rows > 0): ?>
        <table>
          <thead>
            <tr>
              <th>User</th>
              <th>Movie</th>
              <th>Date</th>
              <th>Show Time</th>
              <th>Seat(s)</th>
              <th>Total Price</th>
              <th>Status</th>
              <th>Booked At</th>
              <th>Action</th>
            </tr>
          </thead>
          <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
              <?php
                $statusClass = strtolower(trim($row['booking_status']));
                if (!in_array($statusClass, ['confirmed', 'cancelled', 'pending'])) {
                    $statusClass = 'pending';
                }
              ?>
              <tr>
                <td><?php echo htmlspecialchars($row['user_email']); ?></td>
                <td><?php echo htmlspecialchars($row['movie_name']); ?></td>
                <td><?php echo htmlspecialchars($row['show_date']); ?></td>
                <td><?php echo htmlspecialchars($row['show_time']); ?></td>
                <td><?php echo htmlspecialchars($row['seats']); ?></td>
                <td>৳<?php echo number_format((float)$row['total_price'], 2); ?></td>
                <td>
                  <span class="status <?php echo $statusClass; ?>">
                    <?php echo htmlspecialchars($row['booking_status']); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                <td>
                  <div class="action-group">
                    <a href="edit_seat.php?id=<?php echo $row['id']; ?>" class="btn btn-edit">Edit</a>
                    <a href="update_seat_status.php?id=<?php echo $row['id']; ?>&status=Confirmed" class="btn btn-confirm">Confirm</a>
                    <a href="update_seat_status.php?id=<?php echo $row['id']; ?>&status=Pending" class="btn btn-pending">Pending</a>
                    <a href="update_seat_status.php?id=<?php echo $row['id']; ?>&status=Cancelled" class="btn btn-cancel" onclick="return confirm('Are you sure you want to cancel this booking?')">Cancel</a>
                  </div>
                </td>
              </tr>
            <?php endwhile; ?>
          </tbody>
        </table>
      <?php else: ?>
        <div class="empty-state">
          No seat bookings found yet.
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="footer">
    © 2026 CineBook. All rights reserved.
  </div>

</body>
</html>