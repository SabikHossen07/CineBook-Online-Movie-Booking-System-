<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

$movie_key = $_GET['movie'] ?? '';
$selected_time = $_GET['time'] ?? '';

if ($movie_key === '') {
    header("Location: movies.php");
    exit();
}

$movie_sql = "SELECT * FROM movies WHERE movie_key = ?";
$movie_stmt = $conn->prepare($movie_sql);
$movie_stmt->bind_param("s", $movie_key);
$movie_stmt->execute();
$movie_result = $movie_stmt->get_result();

if ($movie_result->num_rows === 0) {
    header("Location: movies.php");
    exit();
}

$currentMovie = $movie_result->fetch_assoc();

$message = "";
$movie_name = $currentMovie["title"];
$show_date = $currentMovie["show_date"];
$show_time = $selected_time !== '' ? $selected_time : trim(explode(',', $currentMovie["showtimes"])[0]);
$ticket_price = (float)$currentMovie["price"];
$hall_name = $currentMovie["hall"];
$movie_image = $currentMovie["image"];

$booked_seats = [];
$seat_sql = "SELECT seats FROM bookings WHERE movie_name = ? AND show_date = ? AND show_time = ? AND booking_status = 'Confirmed'";
$seat_stmt = $conn->prepare($seat_sql);
$seat_stmt->bind_param("sss", $movie_name, $show_date, $show_time);
$seat_stmt->execute();
$seat_result = $seat_stmt->get_result();

while ($row = $seat_result->fetch_assoc()) {
    $savedSeats = explode(",", $row['seats']);
    foreach ($savedSeats as $s) {
        $booked_seats[] = trim($s);
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_email = $_SESSION['user_email'];
    $seats = trim($_POST['seats'] ?? "");

    if (empty($seats)) {
        $message = "Please select at least one seat.";
    } else {
        $selected_seats = array_map('trim', explode(",", $seats));

        $fresh_booked_seats = [];
        $recheck_sql = "SELECT seats FROM bookings WHERE movie_name = ? AND show_date = ? AND show_time = ? AND booking_status = 'Confirmed'";
        $recheck_stmt = $conn->prepare($recheck_sql);
        $recheck_stmt->bind_param("sss", $movie_name, $show_date, $show_time);
        $recheck_stmt->execute();
        $recheck_result = $recheck_stmt->get_result();

        while ($row = $recheck_result->fetch_assoc()) {
            $savedSeats = explode(",", $row['seats']);
            foreach ($savedSeats as $s) {
                $fresh_booked_seats[] = trim($s);
            }
        }

        $alreadyBooked = false;
        foreach ($selected_seats as $seat) {
            if (in_array($seat, $fresh_booked_seats)) {
                $alreadyBooked = true;
                break;
            }
        }

        if ($alreadyBooked) {
            $message = "One or more selected seats were just booked by another user. Please choose again.";
            $booked_seats = $fresh_booked_seats;
        } else {
            $total_price = $ticket_price * count($selected_seats);
            $booking_status = "Confirmed";

            $insert_sql = "INSERT INTO bookings 
              (user_email, movie_name, show_date, show_time, seats, total_price, booking_status)
              VALUES (?, ?, ?, ?, ?, ?, ?)";

            $insert_stmt = $conn->prepare($insert_sql);
            $insert_stmt->bind_param(
                "sssssis",
                $user_email,
                $movie_name,
                $show_date,
                $show_time,
                $seats,
                $total_price,
                $booking_status
            );

            if ($insert_stmt->execute()) {
                $new_booking_id = $conn->insert_id;
                header("Location: ticket.php?id=" . $new_booking_id);
                exit();
            } else {
                $message = "Database error: " . $insert_stmt->error;
            }
        }
    }
}

function seatClass($seat, $booked_seats) {
    return in_array($seat, $booked_seats) ? "seat booked" : "seat";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>CineBook - Booking</title>
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
  <h1>Book Your Seat</h1>
  <p>Select your seats, review the details, and confirm your booking.</p>
</section>

<section class="booking-section">
  <div class="booking-container">

    <div class="booking-top-info">
      <div class="booking-movie-info">
        <span class="details-badge">Now Showing</span>
        <h2><?php echo htmlspecialchars($movie_name); ?></h2>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($show_date); ?></p>
        <p><strong>Showtime:</strong> <?php echo htmlspecialchars($show_time); ?></p>
        <p><strong>Hall:</strong> <?php echo htmlspecialchars($hall_name); ?></p>
        <p><strong>Ticket Price:</strong> ৳<?php echo htmlspecialchars($ticket_price); ?> per seat</p>
      </div>

      <div class="booking-poster">
        <img src="<?php echo htmlspecialchars($movie_image); ?>" alt="Movie Poster">
      </div>
    </div>

    <?php if (!empty($message)): ?>
      <p style="text-align:center; color:white; background:#dc2626; padding:12px; border-radius:8px; margin-bottom:20px; font-weight:bold;">
        <?php echo htmlspecialchars($message); ?>
      </p>
    <?php endif; ?>

    <div class="screen">SCREEN</div>

    <div class="seat-layout">
      <div class="seat-row">
        <div class="<?php echo seatClass('A1', $booked_seats); ?>">A1</div>
        <div class="<?php echo seatClass('A2', $booked_seats); ?>">A2</div>
        <div class="<?php echo seatClass('A3', $booked_seats); ?>">A3</div>
        <div class="<?php echo seatClass('A4', $booked_seats); ?>">A4</div>
        <div class="<?php echo seatClass('A5', $booked_seats); ?>">A5</div>
        <div class="<?php echo seatClass('A6', $booked_seats); ?>">A6</div>
        <div class="<?php echo seatClass('A7', $booked_seats); ?>">A7</div>
        <div class="<?php echo seatClass('A8', $booked_seats); ?>">A8</div>
      </div>

      <div class="seat-row">
        <div class="<?php echo seatClass('B1', $booked_seats); ?>">B1</div>
        <div class="<?php echo seatClass('B2', $booked_seats); ?>">B2</div>
        <div class="<?php echo seatClass('B3', $booked_seats); ?>">B3</div>
        <div class="<?php echo seatClass('B4', $booked_seats); ?>">B4</div>
        <div class="<?php echo seatClass('B5', $booked_seats); ?>">B5</div>
        <div class="<?php echo seatClass('B6', $booked_seats); ?>">B6</div>
        <div class="<?php echo seatClass('B7', $booked_seats); ?>">B7</div>
        <div class="<?php echo seatClass('B8', $booked_seats); ?>">B8</div>
      </div>

      <div class="seat-row">
        <div class="<?php echo seatClass('C1', $booked_seats); ?>">C1</div>
        <div class="<?php echo seatClass('C2', $booked_seats); ?>">C2</div>
        <div class="<?php echo seatClass('C3', $booked_seats); ?>">C3</div>
        <div class="<?php echo seatClass('C4', $booked_seats); ?>">C4</div>
        <div class="<?php echo seatClass('C5', $booked_seats); ?>">C5</div>
        <div class="<?php echo seatClass('C6', $booked_seats); ?>">C6</div>
        <div class="<?php echo seatClass('C7', $booked_seats); ?>">C7</div>
        <div class="<?php echo seatClass('C8', $booked_seats); ?>">C8</div>
      </div>

      <div class="seat-row">
        <div class="<?php echo seatClass('D1', $booked_seats); ?>">D1</div>
        <div class="<?php echo seatClass('D2', $booked_seats); ?>">D2</div>
        <div class="<?php echo seatClass('D3', $booked_seats); ?>">D3</div>
        <div class="<?php echo seatClass('D4', $booked_seats); ?>">D4</div>
        <div class="<?php echo seatClass('D5', $booked_seats); ?>">D5</div>
        <div class="<?php echo seatClass('D6', $booked_seats); ?>">D6</div>
        <div class="<?php echo seatClass('D7', $booked_seats); ?>">D7</div>
        <div class="<?php echo seatClass('D8', $booked_seats); ?>">D8</div>
      </div>
    </div>

    <div class="seat-info">
      <span><span class="legend available-box"></span> Available</span>
      <span><span class="legend selected-box"></span> Selected</span>
      <span><span class="legend booked-box"></span> Booked</span>
    </div>

    <div class="booking-summary">
      <h3>Booking Summary</h3>
      <p>Selected Seat: <span id="selected-seats">None</span></p>
      <p>Total Price: ৳<span id="total-price">0</span></p>

      <form method="POST" class="auth-form" style="margin-top: 20px;">
        <input type="hidden" id="seat-hidden" name="seats">

        <label for="seat-display" style="text-align:left;">Selected Seat(s)</label>
        <input
          type="text"
          id="seat-display"
          placeholder="Select seat(s) above"
          readonly
        >

        <div class="details-actions" style="justify-content:center; margin-top:15px;">
          <button type="submit" id="confirm-booking" class="book-now-btn">Confirm Booking</button>
          <a href="details.php?movie=<?php echo urlencode($movie_key); ?>" class="book-now-btn outline-btn">Back to Details</a>
        </div>
      </form>
    </div>

  </div>
</section>

<footer>
  <p>© 2026 CineBook. All rights reserved.</p>
</footer>

<script>
document.addEventListener("DOMContentLoaded", function () {
  const seatPrice = <?php echo (int)$ticket_price; ?>;
  const movieKey = <?php echo json_encode($movie_key); ?>;
  const showTime = <?php echo json_encode($show_time); ?>;

  const selectedSeatsText = document.getElementById("selected-seats");
  const totalPriceText = document.getElementById("total-price");
  const seatHidden = document.getElementById("seat-hidden");
  const seatDisplay = document.getElementById("seat-display");
  const confirmButton = document.getElementById("confirm-booking");

  let selectedSeats = [];

  function updateBookingSummary() {
    if (selectedSeats.length > 0) {
      const joinedDisplay = selectedSeats.join(", ");
      const joinedSubmit = selectedSeats.join(",");

      selectedSeatsText.textContent = joinedDisplay;
      totalPriceText.textContent = selectedSeats.length * seatPrice;
      seatHidden.value = joinedSubmit;
      seatDisplay.value = joinedDisplay;
    } else {
      selectedSeatsText.textContent = "None";
      totalPriceText.textContent = "0";
      seatHidden.value = "";
      seatDisplay.value = "";
    }
  }

  function bindSeatClicks() {
    document.querySelectorAll(".seat").forEach((seat) => {
      if (seat.classList.contains("booked")) return;

      seat.onclick = function () {
        const seatName = this.textContent.trim();

        if (this.classList.contains("selected")) {
          this.classList.remove("selected");
          selectedSeats = selectedSeats.filter((s) => s !== seatName);
        } else {
          this.classList.add("selected");
          selectedSeats.push(seatName);
        }

        updateBookingSummary();
      };
    });
  }

  function applyBookedSeats(bookedSeats) {
    document.querySelectorAll(".seat").forEach((seat) => {
      const seatName = seat.textContent.trim();

      if (bookedSeats.includes(seatName)) {
        seat.classList.remove("selected");
        seat.classList.add("booked");
        selectedSeats = selectedSeats.filter((s) => s !== seatName);
      } else if (!seat.classList.contains("selected")) {
        seat.classList.remove("booked");
      }
    });

    updateBookingSummary();
    bindSeatClicks();
  }

  function fetchBookedSeats() {
    fetch(`get_booked_seats.php?movie=${encodeURIComponent(movieKey)}&time=${encodeURIComponent(showTime)}`)
      .then(response => response.json())
      .then(data => {
        applyBookedSeats(data);
      })
      .catch(error => {
        console.error("Seat fetch error:", error);
      });
  }

  if (confirmButton) {
    confirmButton.addEventListener("click", function (e) {
      if (selectedSeats.length === 0) {
        e.preventDefault();
        alert("Please select at least one seat.");
      }
    });
  }

  bindSeatClicks();
  updateBookingSummary();
  fetchBookedSeats();
  setInterval(fetchBookedSeats, 3000);
});
</script>
</body>
</html>