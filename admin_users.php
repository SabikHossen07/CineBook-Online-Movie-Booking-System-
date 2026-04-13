<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$message_color = "#16a34a";

/* DELETE USER */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // নিজের account delete prevent
    if ($delete_id == $_SESSION['user_id'] ?? -1) {
        $message = "You cannot delete your own admin account.";
        $message_color = "#dc2626";
    } else {
        $delete_sql = "DELETE FROM users WHERE id = ?";
        $delete_stmt = $conn->prepare($delete_sql);
        $delete_stmt->bind_param("i", $delete_id);

        if ($delete_stmt->execute()) {
            header("Location: admin_users.php?deleted=1");
            exit();
        } else {
            $message = "Delete failed.";
            $message_color = "#dc2626";
        }
    }
}

$users_result = $conn->query("SELECT id, full_name, email, role, created_at FROM users ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Admin Users</title>
  <link rel="stylesheet" href="style.css" />
  <style>
    .admin-wrap {
      max-width: 1200px;
      margin: 40px auto;
      padding: 0 20px;
    }

    .admin-block {
      background: linear-gradient(135deg, #111827, #1e293b);
      border-radius: 22px;
      padding: 28px;
      margin-bottom: 28px;
      box-shadow: 0 14px 30px rgba(0,0,0,0.22);
      border: 1px solid rgba(250, 204, 21, 0.12);
    }

    .admin-block h2 {
      color: #facc15;
      font-size: 32px;
      margin-bottom: 20px;
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
      vertical-align: top;
    }

    .admin-table th {
      color: #facc15;
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

    .small-delete-btn {
      display: inline-block;
      padding: 8px 14px;
      border-radius: 10px;
      background: #dc2626;
      color: white;
      text-decoration: none;
      font-weight: 700;
    }

    .small-delete-btn:hover {
      background: #b91c1c;
    }
  </style>
</head>
<body>

<header>
  <nav class="navbar">
    <div class="logo">CineBook Admin</div>
    <ul class="nav-links">
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="admin_movies.php">Manage Movies</a></li>
      <li><a href="admin_users.php">Manage Users</a></li>
      <li><span style="color:#facc15;">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="page-banner movies-banner">
  <h1>Manage Users</h1>
  <p>View registered users and manage their accounts.</p>
</section>

<section class="admin-wrap">
  <div class="admin-block">
    <h2>All Users</h2>

    <?php if (!empty($message)): ?>
      <p style="text-align:center; color:white; background:<?php echo $message_color; ?>; padding:12px; border-radius:8px; margin-bottom:18px; font-weight:bold;">
        <?php echo htmlspecialchars($message); ?>
      </p>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <p style="text-align:center; color:white; background:#dc2626; padding:12px; border-radius:8px; margin-bottom:18px; font-weight:bold;">
        User deleted successfully.
      </p>
    <?php endif; ?>

    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Full Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Created At</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($users_result && $users_result->num_rows > 0): ?>
            <?php while ($user = $users_result->fetch_assoc()): ?>
              <?php
                $roleClass = 'role-customer';
                if ($user['role'] === 'admin') $roleClass = 'role-admin';
                if ($user['role'] === 'manager') $roleClass = 'role-manager';
              ?>
              <tr>
                <td><?php echo htmlspecialchars($user['id']); ?></td>
                <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                <td><?php echo htmlspecialchars($user['email']); ?></td>
                <td>
                  <span class="role-badge <?php echo $roleClass; ?>">
                    <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                  </span>
                </td>
                <td><?php echo htmlspecialchars($user['created_at']); ?></td>
                <td>
                  <?php if ($user['email'] !== $_SESSION['user_email']): ?>
                    <a 
                      href="admin_users.php?delete=<?php echo $user['id']; ?>" 
                      class="small-delete-btn"
                      onclick="return confirm('Are you sure you want to delete this user?');"
                    >
                      Delete
                    </a>
                  <?php else: ?>
                    <span style="color:#94a3b8;">Current Admin</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">No users found.</td>
            </tr>
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