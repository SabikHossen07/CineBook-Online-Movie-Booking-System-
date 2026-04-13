<?php
session_start();

$movie = $_GET['movie'] ?? 'avengers';

$movies = [
    "avengers" => [
        "title" => "Avengers: Endgame",
        "genre" => "Action, Sci-Fi",
        "duration" => "3h 1m",
        "language" => "English",
        "rating" => "8.4/10",
        "description" => "After the devastating events of Infinity War, the Avengers assemble once more to reverse Thanos' actions and restore balance to the universe.",
        "image" => "https://picsum.photos/400/550?random=21",
        "showtimes" => ["10:00 AM", "1:30 PM", "4:30 PM", "8:00 PM"]
    ],
    "interstellar" => [
        "title" => "Interstellar",
        "genre" => "Adventure, Drama",
        "duration" => "2h 49m",
        "language" => "English",
        "rating" => "8.7/10",
        "description" => "A team of explorers travels through a wormhole in space in an attempt to ensure humanity's survival.",
        "image" => "https://picsum.photos/400/550?random=22",
        "showtimes" => ["11:00 AM", "2:00 PM", "6:00 PM", "9:00 PM"]
    ],
    "joker" => [
        "title" => "Joker",
        "genre" => "Crime, Thriller",
        "duration" => "2h 2m",
        "language" => "English",
        "rating" => "8.3/10",
        "description" => "A failed comedian begins a slow descent into madness, transforming into the infamous Joker.",
        "image" => "https://picsum.photos/400/550?random=23",
        "showtimes" => ["12:00 PM", "3:30 PM", "7:00 PM", "10:00 PM"]
    ],
    "inception" => [
        "title" => "Inception",
        "genre" => "Sci-Fi, Thriller",
        "duration" => "2h 28m",
        "language" => "English",
        "rating" => "8.8/10",
        "description" => "A skilled thief enters people's dreams to steal secrets, but is given a chance at redemption through one last impossible mission.",
        "image" => "https://picsum.photos/400/550?random=24",
        "showtimes" => ["9:30 AM", "1:00 PM", "5:00 PM", "8:30 PM"]
    ],
    "batman" => [
        "title" => "The Batman",
        "genre" => "Action, Crime",
        "duration" => "2h 56m",
        "language" => "English",
        "rating" => "7.9/10",
        "description" => "Batman ventures into Gotham City's underworld when a sadistic killer leaves behind a trail of cryptic clues.",
        "image" => "https://picsum.photos/400/550?random=25",
        "showtimes" => ["10:30 AM", "2:30 PM", "6:30 PM", "9:30 PM"]
    ],
    "strange" => [
        "title" => "Doctor Strange",
        "genre" => "Fantasy, Action",
        "duration" => "1h 55m",
        "language" => "English",
        "rating" => "7.5/10",
        "description" => "After a tragic car accident, Doctor Strange explores the world of mystic arts and alternate dimensions.",
        "image" => "https://picsum.photos/400/550?random=26",
        "showtimes" => ["11:30 AM", "3:00 PM", "6:30 PM", "9:45 PM"]
    ]
];

$currentMovie = $movies[$movie] ?? $movies["avengers"];
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
      <img src="<?php echo $currentMovie['image']; ?>" alt="Movie Poster">
    </div>

    <div class="details-info">
      <span class="details-badge">Now Showing</span>
      <h1><?php echo $currentMovie['title']; ?></h1>

      <div class="details-meta-grid">
        <p class="movie-meta"><strong>Genre:</strong> <?php echo $currentMovie['genre']; ?></p>
        <p class="movie-meta"><strong>Duration:</strong> <?php echo $currentMovie['duration']; ?></p>
        <p class="movie-meta"><strong>Language:</strong> <?php echo $currentMovie['language']; ?></p>
        <p class="movie-meta"><strong>Rating:</strong> <?php echo $currentMovie['rating']; ?></p>
      </div>

      <p class="movie-description">
        <?php echo $currentMovie['description']; ?>
      </p>

      <div class="showtime-section">
        <h3>Available Showtimes</h3>
        <div class="showtime-buttons">
          <?php foreach ($currentMovie['showtimes'] as $time): ?>
            <a href="booking.php?movie=<?php echo urlencode($movie); ?>&time=<?php echo urlencode($time); ?>" class="book-now-btn">
              <?php echo $time; ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="details-actions">
        <a href="booking.php?movie=<?php echo urlencode($movie); ?>" class="book-now-btn">Book Now</a>
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