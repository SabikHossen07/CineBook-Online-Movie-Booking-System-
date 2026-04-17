<?php
session_start();
include 'db.php';

$movie_key = $_GET['movie'] ?? '';

if ($movie_key === '') {
    header("Location: movies.php");
    exit();
}

$sql = "SELECT * FROM movies WHERE movie_key = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $movie_key);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: movies.php");
    exit();
}

$currentMovie = $result->fetch_assoc();
$showtimes = array_map('trim', explode(',', $currentMovie['showtimes']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Movie Details</title>
  <link rel="stylesheet" href="style.css" />
</head>
<body>

<header>
  <nav class="navbar">
    <div class="logo">CineBook</div>
    <ul class="nav-links">
      <li><a href="index.php">Home</a></li>
      <li><a href="movies.php">Movies</a></li>
      <li><a href="mybookings.php">My Bookings</a></li>

      <?php if (isset($_SESSION['user_name'])): ?>
        <li><span style="color:#facc15;">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
        <li><a href="logout.php" class="btn-nav">Logout</a></li>
      <?php else: ?>
        <li><a href="login.php">Login</a></li>
        <li><a href="register.php" class="btn-nav">Register</a></li>
      <?php endif; ?>
    </ul>
  </nav>
</header>

<section class="page-banner movies-banner">
  <h1>Movie Details</h1>
  <p>Explore movie information, check showtimes, and continue to booking.</p>
</section>

<section class="details-section">
  <div class="details-container">
    <div class="details-poster">
      <img src="<?php echo htmlspecialchars($currentMovie['image']); ?>" alt="Movie Poster">
    </div>

    <div class="details-info">
      <span class="details-badge">Now Showing</span>
      <h1><?php echo htmlspecialchars($currentMovie['title']); ?></h1>

      <div class="details-meta-grid">
        <p class="movie-meta"><strong>Genre:</strong> <?php echo htmlspecialchars($currentMovie['genre']); ?></p>
        <p class="movie-meta"><strong>Duration:</strong> <?php echo htmlspecialchars($currentMovie['duration']); ?></p>
        <p class="movie-meta"><strong>Language:</strong> <?php echo htmlspecialchars($currentMovie['language']); ?></p>
        <p class="movie-meta"><strong>Rating:</strong> <?php echo htmlspecialchars($currentMovie['rating']); ?></p>
      </div>

      <p class="movie-description">
        <?php echo htmlspecialchars($currentMovie['description']); ?>
      </p>

      <div class="showtime-section">
        <h3>Available Showtimes</h3>
        <div class="showtime-buttons">
          <?php foreach ($showtimes as $time): ?>
            <a href="booking.php?movie=<?php echo urlencode($currentMovie['movie_key']); ?>&time=<?php echo urlencode($time); ?>" class="book-now-btn">
              <?php echo htmlspecialchars($time); ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="details-actions">
        <a href="booking.php?movie=<?php echo urlencode($currentMovie['movie_key']); ?>" class="book-now-btn">Book Now</a>
        <a href="movies.php" class="book-now-btn outline-btn">Back to Movies</a>
      </div>
    </div>
  </div>
</section>

<footer>
  <p>© 2026 CineBook. All rights reserved.</p>
</footer>

</body>
</html>