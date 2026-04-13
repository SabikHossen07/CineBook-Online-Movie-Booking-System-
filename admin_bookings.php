<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

/* CANCEL BOOKING */
if (isset($_GET['cancel'])) {
    $id = intval($_GET['cancel']);

    $sql = "UPDATE bookings SET booking_status='cancelled' WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin_bookings.php?cancelled=1");
    exit();
}

/* DELETE BOOKING */
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);

    $sql = "DELETE FROM bookings WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();

    header("Location: admin_bookings.php?deleted=1");
    exit();
}

$result = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Admin Bookings</title>
<link rel="stylesheet" href="style.css">
</head>

<body>

<header>
  <nav class="navbar">
    <div class="logo">CineBook Admin</div>
    <ul class="nav-links">
      <li><a href="admin_dashboard.php">Dashboard</a></li>
      <li><a href="admin_movies.php">Movies</a></li>
      <li><a href="admin_users.php">Users</a></li>
      <li><a href="admin_bookings.php">Bookings</a></li>
      <li><span style="color:#facc15;">Hi, <?php echo $_SESSION['user_name']; ?></span></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </nav>
</header>

<section style="padding:40px;">
<h1 style="color:#facc15;">All Bookings</h1>

<?php if(isset($_GET['cancelled'])): ?>
<p style="color:lightgreen;">Booking cancelled!</p>
<?php endif; ?>

<?php if(isset($_GET['deleted'])): ?>
<p style="color:red;">Booking deleted!</p>
<?php endif; ?>

<table style="width:100%; color:white; border-collapse:collapse;">
<tr style="color:#facc15;">
<th>ID</th>
<th>Email</th>
<th>Movie</th>
<th>Date</th>
<th>Time</th>
<th>Seats</th>
<th>Price</th>
<th>Status</th>
<th>Action</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr style="border-bottom:1px solid #333;">
<td><?php echo $row['id']; ?></td>
<td><?php echo $row['user_email']; ?></td>
<td><?php echo $row['movie_name']; ?></td>
<td><?php echo $row['show_date']; ?></td>
<td><?php echo $row['show_time']; ?></td>
<td><?php echo $row['seats']; ?></td>
<td>৳<?php echo $row['total_price']; ?></td>
<td><?php echo $row['booking_status']; ?></td>

<td>
<a href="?cancel=<?php echo $row['id']; ?>" style="color:orange;">Cancel</a> |
<a href="?delete=<?php echo $row['id']; ?>" style="color:red;">Delete</a>
</td>
</tr>
<?php endwhile; ?>

</table>

</section>

</body>
</html>