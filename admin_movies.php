<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email']) || !isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit();
}

$message = "";
$message_color = "#16a34a";

/* ADD MOVIE */
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['add_movie'])) {
    $movie_key = trim($_POST['movie_key']);
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

    if (
        empty($movie_key) || empty($title) || empty($genre) || empty($duration) ||
        empty($language) || empty($rating) || empty($description) || empty($image) ||
        empty($showtimes) || empty($show_date) || empty($hall) || empty($price)
    ) {
        $message = "All fields are required.";
        $message_color = "#dc2626";
    } else {
        $check_sql = "SELECT id FROM movies WHERE movie_key = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("s", $movie_key);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();

        if ($check_result->num_rows > 0) {
            $message = "Movie key already exists.";
            $message_color = "#dc2626";
        } else {
            $insert_sql = "INSERT INTO movies 
            (movie_key, title, genre, duration, language, rating, description, image, showtimes, show_date, hall, price)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param(
                "sssssssssssd",
                $movie_key,
                $title,
                $genre,
                $duration,
                $language,
                $rating,
                $description,
                $image,
                $showtimes,
                $show_date,
                $hall,
                $price
            );

            if ($insert_stmt->execute()) {
                $message = "Movie added successfully!";
                $message_color = "#16a34a";
            } else {
                $message = "Insert failed: " . $insert_stmt->error;
                $message_color = "#dc2626";
            }
        }
    }
}

/* DELETE MOVIE */
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    $delete_sql = "DELETE FROM movies WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $delete_id);

    if ($delete_stmt->execute()) {
        header("Location: admin_movies.php?deleted=1");
        exit();
    } else {
        $message = "Delete failed.";
        $message_color = "#dc2626";
    }
}

$movies_result = $conn->query("SELECT * FROM movies ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Admin Movies</title>
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

    .movie-form-grid {
      display: grid;
      grid-template-columns: repeat(2, 1fr);
      gap: 16px;
    }

    .movie-form-grid .full-width {
      grid-column: 1 / -1;
    }

    .movie-form-grid input,
    .movie-form-grid textarea {
      width: 100%;
      padding: 14px 16px;
      background: #0f172a;
      border: 1px solid rgba(255,255,255,0.08);
      border-radius: 12px;
      color: #fff;
      font-size: 16px;
      outline: none;
      box-sizing: border-box;
    }

    .movie-form-grid textarea {
      min-height: 120px;
      resize: vertical;
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

    @media (max-width: 800px) {
      .movie-form-grid {
        grid-template-columns: 1fr;
      }

      .admin-block h2 {
        font-size: 26px;
      }
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
      <li><span style="color:#facc15;">Hi, <?php echo htmlspecialchars($_SESSION['user_name']); ?></span></li>
      <li><a href="logout.php" class="btn-nav">Logout</a></li>
    </ul>
  </nav>
</header>

<section class="page-banner movies-banner">
  <h1>Manage Movies</h1>
  <p>Add and remove movie data from the admin panel.</p>
</section>

<section class="admin-wrap">

  <div class="admin-block">
    <h2>Add New Movie</h2>

    <?php if (!empty($message)): ?>
      <p style="text-align:center; color:white; background:<?php echo $message_color; ?>; padding:12px; border-radius:8px; margin-bottom:18px; font-weight:bold;">
        <?php echo htmlspecialchars($message); ?>
      </p>
    <?php endif; ?>

    <?php if (isset($_GET['deleted'])): ?>
      <p style="text-align:center; color:white; background:#dc2626; padding:12px; border-radius:8px; margin-bottom:18px; font-weight:bold;">
        Movie deleted successfully.
      </p>
    <?php endif; ?>

    <form method="POST">
      <div class="movie-form-grid">
        <input type="text" name="movie_key" placeholder="Movie Key (example: batman2)" required>
        <input type="text" name="title" placeholder="Movie Title" required>

        <input type="text" name="genre" placeholder="Genre" required>
        <input type="text" name="duration" placeholder="Duration (example: 2h 15m)" required>

        <input type="text" name="language" placeholder="Language" required>
        <input type="text" name="rating" placeholder="Rating (example: 8.2/10)" required>

        <input type="text" name="image" placeholder="Image URL" class="full-width" required>
        <input type="text" name="showtimes" placeholder="Showtimes comma separated (example: 10:00 AM,1:30 PM,4:30 PM)" class="full-width" required>

        <input type="text" name="show_date" placeholder="Show Date (example: 2026-04-25)" required>
        <input type="text" name="hall" placeholder="Hall Name" required>

        <input type="number" step="0.01" name="price" placeholder="Ticket Price" required>
        <div></div>

        <textarea name="description" placeholder="Movie Description" class="full-width" required></textarea>
      </div>

      <div style="margin-top:18px;">
        <button type="submit" name="add_movie" class="book-now-btn">Add Movie</button>
      </div>
    </form>
  </div>

  <div class="admin-block">
    <h2>All Movies</h2>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>ID</th>
            <th>Key</th>
            <th>Title</th>
            <th>Genre</th>
            <th>Date</th>
            <th>Hall</th>
            <th>Price</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php if ($movies_result && $movies_result->num_rows > 0): ?>
            <?php while ($movie = $movies_result->fetch_assoc()): ?>
              <tr>
                <td><?php echo htmlspecialchars($movie['id']); ?></td>
                <td><?php echo htmlspecialchars($movie['movie_key']); ?></td>
                <td><?php echo htmlspecialchars($movie['title']); ?></td>
                <td><?php echo htmlspecialchars($movie['genre']); ?></td>
                <td><?php echo htmlspecialchars($movie['show_date']); ?></td>
                <td><?php echo htmlspecialchars($movie['hall']); ?></td>
                <td>৳<?php echo htmlspecialchars($movie['price']); ?></td>
                <td>
                  <a 
                    href="admin_movies.php?delete=<?php echo $movie['id']; ?>" 
                    class="small-delete-btn"
                    onclick="return confirm('Are you sure you want to delete this movie?');"
                  >
                    Delete
                  </a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="8">No movies found.</td>
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