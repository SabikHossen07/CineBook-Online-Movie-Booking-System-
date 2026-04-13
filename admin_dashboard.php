<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* COUNTS */
$total_users = 0;
$total_bookings = 0;
$total_customers = 0;
$total_admins = 0;
$total_managers = 0;

$result = $conn->query("SELECT COUNT(*) AS total FROM users");
if ($result) {
    $row = $result->fetch_assoc();
    $total_users = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM bookings");
if ($result) {
    $row = $result->fetch_assoc();
    $total_bookings = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'customer'");
if ($result) {
    $row = $result->fetch_assoc();
    $total_customers = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'admin'");
if ($result) {
    $row = $result->fetch_assoc();
    $total_admins = $row['total'];
}

$result = $conn->query("SELECT COUNT(*) AS total FROM users WHERE role = 'manager'");
if ($result) {
    $row = $result->fetch_assoc();
    $total_managers = $row['total'];
}

$recent_bookings = $conn->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 5");
$recent_users = $conn->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY id DESC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Admin Dashboard</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .dashboard-wrap {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(5, 1fr);
      gap: 18px;
      margin-bottom: 34px;
    }
    .stat-card {
      background: linear-gradient(135deg, #111827, #1e293b);
      border-radius: 20px;
      padding: 24px 18px;
      text-align: center;
      box-shadow: 0 14px 30px rgba(0,0,0,0.22);
      border: 1px solid rgba(250, 204, 21, 0.12);
    }
    .stat-card h3 {
      color: #cbd5e1;
      font-size: 18px;
      margin-bottom: 12px;
    }
    .stat-card p {
      color: #facc15;
      font-size: 34px;
      font-weight: 800;
      margin: 0;
    }
    .dashboard-section {
      background: linear-gradient(135deg, #111827, #1e293b);
      border-radius: 22px;
      padding: 26px;
      margin-bottom: 28px;
      box-shadow: 0 14px 30px rgba(0,0,0,0.22);
      border: 1px solid rgba(250, 204, 21, 0.12);
    }
    .dashboard-section h2 {
      color: #facc15;
      margin-bottom: 18px;
      font-size: 32px;
    }
    .admin-table-wrap {
      overflow-x: auto;
    }
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      color: #fff;
    }
    .admin-table th,
    .admin-table td {
      padding: 14px 12px;
      text-align: left;
      border-bottom: 1px solid rgba(255,255,255,0.08);
      white-space: nowrap;
    }
    .admin-table th {
      color: #facc15;
      font-size: 16px;
    }
    .role-badge {
      display: inline-block;
      padding: 6px 12px;
      border-radius: 999px;
      font-size: 13px;
      font-weight: 700;
    }
    .role-customer {
      background: rgba(59, 130, 246, 0.18);
      color: #93c5fd;
    }
    .role-admin {
      background: rgba(250, 204, 21, 0.18);
      color: #fde68a;
    }
    .role-manager {
      background: rgba(16, 185, 129, 0.18);
      color: #86efac;
    }
    .admin-actions {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 18px;
    }
    .admin-action-card {
      background: #0f172a;
      border: 1px solid rgba(250, 204, 21, 0.12);
      border-radius: 18px;
      padding: 22px;
      color: #fff;
    }
    .admin-action-card h3 {
      color: #facc15;
      margin-bottom: 10px;
      font-size: 22px;
    }
    .admin-action-card p {
      color: #cbd5e1;
      margin: 0;
      line-height: 1.6;
    }
    @media (max-width: 1100px) {
      .stats-grid {
        grid-template-columns: repeat(2, 1fr);
      }
      .admin-actions {
        grid-template-columns: 1fr;
      }
    }
    @media (max-width: 640px) {
      .stats-grid {
        grid-template-columns: 1fr;
      }
      .dashboard-section h2 {
        font-size: 26px;
      }
    }
  </style>
</head>
<body>

<header>
  <nav class="navbar">
    <div class="logo">CineBook Admin</div>
    <ul class="nav-links">
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><span style="color:#facc15;">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="page-banner movies-banner">
  <h1>Admin Dashboard</h1>
  <p>Monitor users, bookings, and platform activity from one place.</p>
</section>

<section class="dashboard-wrap">

  <div class="stats-grid">
    <div class="stat-card"><h3>Total Users</h3><p><?php echo $total_users; ?></p></div>
    <div class="stat-card"><h3>Total Bookings</h3><p><?php echo $total_bookings; ?></p></div>
    <div class="stat-card"><h3>Customers</h3><p><?php echo $total_customers; ?></p></div>
    <div class="stat-card"><h3>Admins</h3><p><?php echo $total_admins; ?></p></div>
    <div class="stat-card"><h3>Managers</h3><p><?php echo $total_managers; ?></p></div>
  </div>

  <div class="dashboard-section">
    <h2>Admin Controls</h2>
    <div class="admin-actions">
  <div class="admin-action-card">
    <h3><a href="admin_movies.php" style="color:#facc15; text-decoration:none;">Manage Movies</a></h3>
    <p>Add, update, or remove movies from the platform.</p>
  </div>
    <div class="admin-action-card">
      <h3><a href="admin_users.php" style="color:#facc15; text-decoration:none;">Manage Users</a></h3>
      <p>Track registered users and monitor their roles.</p>
 </div>
  </div>

  <div class="admin-action-card">
  <h3><a href="admin_bookings.php" style="color:#facc15; text-decoration:none;">Manage Booking Data</a></h3>
  <p>See booking activity and monitor confirmed reservations.</p>
  </div>
  </div>
  </div>

  <div class="dashboard-section">
    <h2>Recent Bookings</h2>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>User Email</th>
            <th>Movie</th>
            <th>Date</th>
            <th>Show Time</th>
            <th>Seat(s)</th>
            <th>Total Price</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($recent_bookings && $recent_bookings->num_rows > 0): ?>
            <?php while ($booking = $recent_bookings->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($booking['id']); ?></td>
                <td><?php echo htmlspecialchars($booking['user_email']); ?></td>
                <td><?php echo htmlspecialchars($booking['movie_name']); ?></td>
                <td><?php echo htmlspecialchars($booking['show_date']); ?></td>
                <td><?php echo htmlspecialchars($booking['show_time']); ?></td>
                <td><?php echo htmlspecialchars($booking['seats']); ?></td>
                <td>৳<?php echo htmlspecialchars($booking['total_price']); ?></td>
                <td><?php echo htmlspecialchars($booking['booking_status']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="8">No bookings found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div class="dashboard-section">
    <h2>Recent Users</h2>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($recent_users && $recent_users->num_rows > 0): ?>
            <?php while ($user = $recent_users->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                  <?php
                    $role = $user['role'];
                    $roleClass = 'role-customer';
                    if ($role === 'admin') $roleClass = 'role-admin';
                    if ($role === 'manager') $roleClass = 'role-manager';
                  ?>
                  <span class="role-badge <?php echo $roleClass; ?>">
                    <?php echo htmlspecialchars(ucfirst($role)); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="5">No users found.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</section>

<footer>
  <p>© 2026 CineBook. All rights reserved.</p>
</footer>

</body>
</html>