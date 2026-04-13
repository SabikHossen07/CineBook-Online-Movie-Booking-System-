<?php
session_start();
include 'db.php';

$search = trim($_GET['search'] ?? '');

if ($search !== '') {
    $sql = "SELECT * FROM movies 
            WHERE title LIKE ? OR genre LIKE ? OR language LIKE ?
            ORDER BY id DESC";
    $stmt = $conn->prepare($sql);
    $like = "%" . $search . "%";
    $stmt->bind_param("sss", $like, $like, $like);
    $stmt->execute();
    $movies_result = $stmt->get_result();
} else {
    $movies_result = $conn->query("SELECT * FROM movies ORDER BY id DESC");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Movies</title>
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
  <h1>Now Showing</h1>
  <p>Explore the latest movies and book your seats instantly with CineBook.</p>
</section>

<section class="search-section">
  <div class="search-box">
    <form method="GET" action="movies.php" style="display:flex; gap:12px; flex-wrap:wrap; width:100%;">
      <input 
        type="text" 
        name="search" 
        placeholder="Search by title, genre, or language..." 
        value="<?php echo htmlspecialchars($search); ?>"
        style="flex:1; min-width:260px;"
      />
      <button type="submit">Search</button>
      <a href="movies.php" class="book-now-btn outline-btn" style="text-decoration:none;">Reset</a>
    </form>
  </div>
</section>

<section class="featured movies-page">
  <h2>Popular Movies</h2>

  <div class="movie-grid">
    <?php if ($movies_result && $movies_result->num_rows > 0): ?>
      <?php while ($movie = $movies_result->fetch_assoc()): ?>
        <div class="movie-card">
          <img src="<?php echo htmlspecialchars($movie['image']); ?>" alt="<?php echo htmlspecialchars($movie['title']); ?>">
          <h3><?php echo htmlspecialchars($movie['title']); ?></h3>
          <p><?php echo htmlspecialchars($movie['genre']); ?></p>
          <div class="card-actions">
            <a href="details.php?movie=<?php echo urlencode($movie['movie_key']); ?>" class="card-btn">View Details</a>
            <a href="booking.php?movie=<?php echo urlencode($movie['movie_key']); ?>" class="card-btn secondary-btn">Book Now</a>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="color:white; font-size:20px; text-align:center; width:100%;">No movies found.</p>
    <?php endif; ?>
  </div>
</section>

<footer>
  <p>© 2026 CineBook. All rights reserved.</p>
</footer>

</body>
</html>