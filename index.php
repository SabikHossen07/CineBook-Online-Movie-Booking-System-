<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Home</title>
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

<section class="hero">
  <div class="hero-overlay"></div>
  <div class="hero-content">
    <h1 class="hero-title"><span id="typing-text"></span></h1>
    <p class="hero-subtitle fade-in-up">
      Seamless booking, immersive journeys and unforgettable movie nights —
      all in one place with CineBook.
    </p>
    <a href="movies.php" class="hero-btn fade-in-up delay-1">Browse Movies</a>
  </div>
</section>

<section class="home-tabs-section">
  <div class="home-tabs">
    <button class="tab-item active-tab" onclick="filterMovies('all', this)">Now Showing</button>
    <button class="tab-item" onclick="filterMovies('coming', this)">Coming Soon</button>
    <button class="tab-item" onclick="filterMovies('top', this)">Top Rated</button>
    <button class="tab-item" onclick="window.location.href='movies.php'">Show Times</button>
  </div>

  <div class="home-search-box">
    <input type="text" id="movieSearch" placeholder="Search featured movies..." onkeyup="searchMovies()">
  </div>
</section>

<section class="featured">
  <div class="section-header">
    <h2>Featured Movies</h2>
    <a href="movies.php" class="view-all-link">View All Movies</a>
  </div>

  <div class="movie-grid" id="movieGrid">
    <div class="movie-card" data-category="top">
      <img src="https://picsum.photos/250/350?random=1" alt="Avengers Endgame">
      <h3>Avengers: Endgame</h3>
      <p>Action | Sci-Fi</p>
      <div class="card-actions">
        <a href="details.php?movie=avengers" class="card-btn secondary-btn">Details</a>
        <a href="booking.php?movie=avengers" class="card-btn primary-btn">Book Now</a>
      </div>
    </div>

    <div class="movie-card" data-category="top">
      <img src="https://picsum.photos/250/350?random=2" alt="Interstellar">
      <h3>Interstellar</h3>
      <p>Adventure | Drama</p>
      <div class="card-actions">
        <a href="details.php?movie=interstellar" class="card-btn secondary-btn">Details</a>
        <a href="booking.php?movie=interstellar" class="card-btn primary-btn">Book Now</a>
      </div>
    </div>

    <div class="movie-card" data-category="now">
      <img src="https://picsum.photos/250/350?random=3" alt="Joker">
      <h3>Joker</h3>
      <p>Crime | Thriller</p>
      <div class="card-actions">
        <a href="details.php?movie=joker" class="card-btn secondary-btn">Details</a>
        <a href="booking.php?movie=joker" class="card-btn primary-btn">Book Now</a>
      </div>
    </div>

    <div class="movie-card" data-category="coming">
      <img src="https://picsum.photos/250/350?random=4" alt="Inception">
      <h3>Inception</h3>
      <p>Sci-Fi | Thriller</p>
      <div class="card-actions">
        <a href="details.php?movie=inception" class="card-btn secondary-btn">Details</a>
        <a href="booking.php?movie=inception" class="card-btn primary-btn">Book Now</a>
      </div>
    </div>
  </div>

  <div class="no-results" id="noResults" style="display:none;">
    No movies found.
  </div>

  <div class="center-button">
    <a href="movies.php" class="outline-btn">View All Movies</a>
  </div>
</section>

<section class="highlight-section">
  <div class="highlight-box">
    <h2>Why Choose CineBook?</h2>
    <div class="highlight-grid">
      <div class="highlight-card">
        <h3>Easy Booking</h3>
        <p>Select your movie, choose seats, and confirm in just a few clicks.</p>
      </div>
      <div class="highlight-card">
        <h3>Real-Time Seats</h3>
        <p>See available and booked seats instantly before payment.</p>
      </div>
      <div class="highlight-card">
        <h3>Fast Confirmation</h3>
        <p>Get your ticket details immediately after booking confirmation.</p>
      </div>
    </div>
  </div>
</section>

<footer>
  <p>© 2026 CineBook. All rights reserved.</p>
</footer>

<script src="script.js"></script>
</body>
</html>