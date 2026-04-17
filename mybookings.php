<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$user_email = $_SESSION['user_email'];

$sql = "SELECT * FROM bookings WHERE user_email = ? ORDER BY id DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - My Bookings</title>
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
  <h1>My Bookings</h1>
  <p>Track your real movie reservations and booking history.</p>
</section>

<section class="bookings-section">
  <div class="bookings-container">
    <h1>Recent Bookings</h1>
    <p class="bookings-subtitle">Here are your latest confirmed movie tickets.</p>

    <?php if (isset($_GET['success'])): ?>
      <p style="text-align:center; background:#16a34a; color:white; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
        Booking confirmed successfully!
      </p>
    <?php endif; ?>

    <?php if (isset($_GET['cancel']) && $_GET['cancel'] === 'success'): ?>
      <p style="text-align:center; background:#dc2626; color:white; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
        Booking cancelled successfully.
      </p>
    <?php endif; ?>

    <?php if (isset($_GET['cancel']) && $_GET['cancel'] === 'error'): ?>
      <p style="text-align:center; background:#b91c1c; color:white; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
        Something went wrong while cancelling the booking.
      </p>
    <?php endif; ?>

    <?php if (isset($_GET['cancel']) && $_GET['cancel'] === 'closed'): ?>
      <p style="text-align:center; background:#f59e0b; color:black; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
        Booking cannot be cancelled within 2 hours of show start time.
      </p>
    <?php endif; ?>

    <?php if (isset($_GET['cancel']) && $_GET['cancel'] === 'already'): ?>
      <p style="text-align:center; background:#475569; color:white; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
        This booking is already cancelled.
      </p>
    <?php endif; ?>

    <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
        <?php
          $showDateTime = strtotime($row['show_date'] . ' ' . $row['show_time']);
          $currentTime = time();
          $cancelDeadline = $showDateTime - (2 * 60 * 60);

          $canCancel = false;
          if ($row['booking_status'] !== 'Cancelled' && $currentTime < $cancelDeadline) {
              $canCancel = true;
          }

          $status = $row['booking_status'];
          $badgeText = $status;
          $statusClass = 'status-confirmed';

          if ($status === 'Cancelled') {
              $statusClass = 'status-cancelled';
          } elseif ($status === 'Pending') {
              $statusClass = 'status-pending';
          } else {
              $statusClass = 'status-confirmed';
          }
        ?>
        <div class="booking-card premium-booking-card">
          <div class="booking-card-left">
            <img src="https://picsum.photos/220/300?random=<?php echo rand(31, 99); ?>" alt="Movie Poster">
          </div>

          <div class="booking-card-right">
            <span class="details-badge"><?php echo htmlspecialchars($badgeText); ?></span>
            <h2><?php echo htmlspecialchars($row['movie_name']); ?></h2>
            <p><strong>Date:</strong> <?php echo htmlspecialchars($row['show_date']); ?></p>
            <p><strong>Show Time:</strong> <?php echo htmlspecialchars($row['show_time']); ?></p>
            <p><strong>Seat(s):</strong> <?php echo htmlspecialchars($row['seats']); ?></p>
            <p><strong>Total Price:</strong> ৳<?php echo htmlspecialchars($row['total_price']); ?></p>
            <p><strong>Booked At:</strong> <?php echo htmlspecialchars($row['created_at']); ?></p>
            <p><strong>Status:</strong> <span class="<?php echo $statusClass; ?>"><?php echo htmlspecialchars($row['booking_status']); ?></span></p>

            <div class="details-actions">
              <a href="ticket.php?id=<?php echo $row['id']; ?>" class="book-now-btn">View Ticket</a>
              <a href="movies.php" class="book-now-btn">Book Again</a>

              <?php if ($canCancel): ?>
                <a href="cancel_booking.php?id=<?php echo $row['id']; ?>"
                   class="book-now-btn outline-btn"
                   onclick="return confirm('Are you sure you want to cancel this booking?');">
                  Cancel Booking
                </a>
              <?php elseif ($row['booking_status'] === 'Cancelled'): ?>
                <span class="book-now-btn outline-btn" style="opacity:0.65; cursor:not-allowed;">
                  Already Cancelled
                </span>
              <?php else: ?>
                <span class="book-now-btn outline-btn" style="opacity:0.65; cursor:not-allowed;">
                  Cancellation Closed
                </span>
              <?php endif; ?>
            </div>
          </div>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p style="text-align:center; color:#fff; font-size:20px; margin-top:30px;">
        No bookings found yet.
      </p>
    <?php endif; ?>
  </div>
</section>

<footer>
  <p>© 2026 CineBook. All rights reserved.</p>
</footer>

</body>
</html>