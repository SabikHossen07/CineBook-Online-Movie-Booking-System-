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
    $duration = trim($_POST['duration']);
    $language = trim($_POST['language']);
    $rating = trim($_POST['rating']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);
    $showtimes = trim($_POST['showtimes']);
    $show_date = trim($_POST['show_date']);
    $hall = trim($_POST['hall']);
    $price = trim($_POST['price']);

    $movie_key = 'MOV' . rand(10000, 99999);

    $sql = "INSERT INTO movies (movie_key, title, genre, duration, language, rating, description, image, showtimes, show_date, hall, price)
            VALUES ('$movie_key', '$title', '$genre', '$duration', '$language', '$rating', '$description', '$image', '$showtimes', '$show_date', '$hall', '$price')";

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
      max-width: 760px;
      margin: 40px auto;
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
    input, textarea {
      width: 100%;
      padding: 12px;
      border-radius: 10px;
      border: none;
      outline: none;
      font-size: 15px;
    }
    textarea {
      min-height: 110px;
      resize: vertical;
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

      <label>Duration</label>
      <input type="text" name="duration" placeholder="2h 49m" required>

      <label>Language</label>
      <input type="text" name="language" placeholder="English" required>

      <label>Rating</label>
      <input type="text" name="rating" placeholder="PG-13" required>

      <label>Description</label>
      <textarea name="description" required></textarea>

      <label>Image Path / URL</label>
      <input type="text" name="image" placeholder="images/movie.jpg">

      <label>Showtimes</label>
      <input type="text" name="showtimes" placeholder="10:00 AM, 1:30 PM, 4:30 PM" required>

      <label>Show Date</label>
      <input type="text" name="show_date" placeholder="2026-04-20" required>

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