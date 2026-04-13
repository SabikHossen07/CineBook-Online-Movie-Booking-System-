<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $genre = trim($_POST['genre']);
    $show_date = trim($_POST['show_date']);
    $showtimes = trim($_POST['showtimes']);
    $hall = trim($_POST['hall']);
    $price = trim($_POST['price']);

    $sql = "INSERT INTO movies (title, genre, show_date, showtimes, hall, price)
            VALUES ('$title', '$genre', '$show_date', '$showtimes', '$hall', '$price')";

    if ($conn->query($sql) === TRUE) {
        header("Location: manager_showtimes.php");
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
  <title>Add Showtime</title>
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
    input {
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
    <h1>Add Showtime</h1>

    <form method="POST">
      <label>Movie Title</label>
      <input type="text" name="title" required>

      <label>Genre</label>
      <input type="text" name="genre" required>

      <label>Date</label>
      <input type="date" name="show_date" required>

      <label>Showtimes</label>
      <input type="text" name="showtimes" placeholder="10:00 AM, 1:30 PM, 4:30 PM" required>

      <label>Hall</label>
      <input type="text" name="hall" required>

      <label>Price</label>
      <input type="number" step="0.01" name="price" required>

      <button type="submit">Save Showtime</button>
      <a href="manager_showtimes.php" class="back-btn">Back</a>
    </form>

    <?php if (!empty($message)): ?>
      <div class="error"><?php echo $message; ?></div>
    <?php endif; ?>
  </div>
</body>
</html>