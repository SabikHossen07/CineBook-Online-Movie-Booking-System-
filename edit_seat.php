<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: manager_seats.php");
    exit();
}

$id = (int) $_GET['id'];
$result = $conn->query("SELECT * FROM bookings WHERE id = $id");

if (!$result || $result->num_rows === 0) {
    header("Location: manager_seats.php");
    exit();
}

$row = $result->fetch_assoc();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = trim($_POST['user_email']);
    $movie_name = trim($_POST['movie_name']);
    $show_date = trim($_POST['show_date']);
    $show_time = trim($_POST['show_time']);
    $seats = trim($_POST['seats']);
    $total_price = trim($_POST['total_price']);
    $booking_status = trim($_POST['booking_status']);

    $sql = "UPDATE bookings 
            SET user_email='$user_email',
                movie_name='$movie_name',
                show_date='$show_date',
                show_time='$show_time',
                seats='$seats',
                total_price='$total_price',
                booking_status='$booking_status'
            WHERE id=$id";

    if ($conn->query($sql) === TRUE) {
        header("Location: manager_seats.php");
        exit();
    } else {
        $message = "Error: " . $conn->error;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Seat Booking</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(90deg, #00143c, #071a40, #00143c);
      color: white;
      margin: 0;
      padding: 0;
    }
    .container {
      max-width: 700px;
      margin: 60px auto;
      background: linear-gradient(135deg, #111827, #1e293b);
      padding: 30px;
      border-radius: 20px;
      box-shadow: 0 12px 24px rgba(0,0,0,0.25);
    }
    h1 {
      color: #facc15;
      margin-bottom: 20px;
      text-align: center;
    }
    label {
      display: block;
      margin: 14px 0 8px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: none;
      outline: none;
      font-size: 15px;
    }
    button, .back-btn {
      margin-top: 20px;
      padding: 12px 18px;
      border: none;
      border-radius: 10px;
      font-weight: bold;
      cursor: pointer;
      text-decoration: none;
      display: inline-block;
    }
    button {
      background: #22c55e;
      color: white;
    }
    .back-btn {
      background: #facc15;
      color: #111827;
      margin-left: 10px;
    }
    .error {
      margin-top: 15px;
      color: #f87171;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="container">
    <h1>Edit Seat Booking</h1>

    <form method="POST">
      <label>User Email</label>
      <input type="email" name="user_email" value="<?php echo htmlspecialchars($row['user_email']); ?>" required>

      <label>Movie Name</label>
      <input type="text" name="movie_name" value="<?php echo htmlspecialchars($row['movie_name']); ?>" required>

      <label>Date</label>
      <input type="text" name="show_date" value="<?php echo htmlspecialchars($row['show_date']); ?>" required>

      <label>Show Time</label>
      <input type="text" name="show_time" value="<?php echo htmlspecialchars($row['show_time']); ?>" required>

      <label>Seat(s)</label>
      <input type="text" name="seats" value="<?php echo htmlspecialchars($row['seats']); ?>" required>

      <label>Total Price</label>
      <input type="number" step="0.01" name="total_price" value="<?php echo htmlspecialchars($row['total_price']); ?>" required>

      <label>Status</label>
      <select name="booking_status" required>
        <option value="Confirmed" <?php if ($row['booking_status'] == 'Confirmed') echo 'selected'; ?>>Confirmed</option>
        <option value="Pending" <?php if ($row['booking_status'] == 'Pending') echo 'selected'; ?>>Pending</option>
        <option value="Cancelled" <?php if ($row['booking_status'] == 'Cancelled') echo 'selected'; ?>>Cancelled</option>
      </select>

      <button type="submit">Update Booking</button>
      <a href="manager_seats.php" class="back-btn">Back</a>
    </form>

    <?php if (!empty($message)): ?>
      <div class="error"><?php echo $message; ?></div>
    <?php endif; ?>
  </div>
</body>
</html>