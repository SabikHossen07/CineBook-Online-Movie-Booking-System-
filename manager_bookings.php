<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$result = $conn->query("SELECT * FROM bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
  <title>Manager - Bookings</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>

<h1 style="text-align:center; color:yellow;">All Bookings</h1>

<table style="width:90%; margin:auto; color:white;">
<tr>
  <th>ID</th>
  <th>User</th>
  <th>Movie</th>
  <th>Date</th>
  <th>Time</th>
  <th>Seats</th>
  <th>Status</th>
</tr>

<?php while($row = $result->fetch_assoc()): ?>
<tr>
  <td><?php echo $row['id']; ?></td>
  <td><?php echo $row['user_email']; ?></td>
  <td><?php echo $row['movie_name']; ?></td>
  <td><?php echo $row['show_date']; ?></td>
  <td><?php echo $row['show_time']; ?></td>
  <td><?php echo $row['seats']; ?></td>
  <td><?php echo $row['booking_status']; ?></td>
</tr>
<?php endwhile; ?>

</table>

</body>
</html>