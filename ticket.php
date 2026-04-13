<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    header("Location: mybookings.php");
    exit();
}

$ticket_id = intval($_GET['id']);
$user_email = $_SESSION['user_email'];

$sql = "SELECT * FROM bookings WHERE id = ? AND user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $ticket_id, $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: mybookings.php");
    exit();
}

$ticket = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>CineBook - Ticket</title>
<style>
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  font-family: Arial, sans-serif;
  background: linear-gradient(90deg, #00143c, #071a40, #00143c);
  color: white;
}

.ticket-page-wrap {
  max-width: 980px;
  margin: 30px auto;
  padding: 0 18px;
}

.ticket-main-card {
  display: grid;
  grid-template-columns: 1.35fr 0.8fr;
  gap: 24px;
  background: linear-gradient(135deg, #111827, #1e293b);
  border-radius: 24px;
  padding: 28px;
  box-shadow: 0 18px 40px rgba(0,0,0,0.35);
  border: 1px solid rgba(250, 204, 21, 0.18);
}

.ticket-title {
  text-align: center;
  font-size: 52px;
  font-weight: 800;
  background: linear-gradient(90deg, #facc15, #fde68a);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  margin-bottom: 6px;
  letter-spacing: 1px;
}

.ticket-subtitle {
  text-align: center;
  font-size: 16px;
  color: #cbd5e1;
  margin-bottom: 20px;
}

.ticket-info-box {
  background: #0f172a;
  padding: 24px;
  border-radius: 18px;
  border: 2px dashed #facc15;
  position: relative;
  overflow: hidden;
}

.ticket-info-box::before {
  content: "";
  position: absolute;
  top: 0;
  right: -60px;
  width: 170px;
  height: 170px;
  background: radial-gradient(circle, rgba(250,204,21,0.16), transparent 70%);
}

.ticket-info-box p {
  font-size: 18px;
  margin-bottom: 12px;
  color: #ffffff;
  position: relative;
  z-index: 1;
}

.ticket-info-box strong {
  color: #fde68a;
}

.ticket-right {
  display: flex;
  justify-content: center;
  align-items: center;
}

.cinema-badge {
  width: 100%;
  max-width: 260px;
  min-height: 330px;
  background: linear-gradient(135deg, #fff7cc, #facc15, #f4d03f);
  border-radius: 24px;
  position: relative;
  overflow: hidden;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  color: #5b2100;
  border: 3px solid rgba(120, 53, 15, 0.18);
  box-shadow: 0 18px 34px rgba(250, 204, 21, 0.32);
}

.cinema-badge::before,
.cinema-badge::after {
  content: "";
  position: absolute;
  width: 36px;
  height: 36px;
  background: #1e293b;
  border-radius: 50%;
  left: -18px;
  box-shadow: 0 82px 0 #1e293b, 0 164px 0 #1e293b;
}

.cinema-badge::after {
  left: auto;
  right: -18px;
}

.badge-shine {
  position: absolute;
  top: -40px;
  left: -25px;
  width: 85px;
  height: 200%;
  background: rgba(255,255,255,0.28);
  transform: rotate(18deg);
  filter: blur(4px);
}

.badge-icon {
  font-size: 42px;
  margin-bottom: 12px;
  position: relative;
  z-index: 1;
}

.badge-title {
  font-size: 28px;
  text-align: center;
  font-weight: 800;
  line-height: 1.1;
  letter-spacing: 1px;
  position: relative;
  z-index: 1;
}

.badge-sub {
  margin-top: 12px;
  font-size: 15px;
  font-weight: 700;
  letter-spacing: 3px;
  opacity: 0.9;
  position: relative;
  z-index: 1;
}

.badge-code {
  margin-top: 22px;
  background: rgba(91, 33, 0, 0.09);
  padding: 8px 16px;
  border-radius: 999px;
  font-size: 16px;
  font-weight: 700;
  position: relative;
  z-index: 1;
}

.ticket-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  margin-top: 20px;
  flex-wrap: wrap;
}

.btn {
  padding: 12px 20px;
  border-radius: 10px;
  background: #facc15;
  color: #111827;
  font-weight: bold;
  text-decoration: none;
  border: none;
  cursor: pointer;
  transition: 0.25s ease;
}

.btn:hover {
  opacity: 0.92;
  transform: translateY(-1px);
}

.outline {
  background: transparent;
  border: 2px solid #facc15;
  color: #facc15;
}

.ticket-note {
  margin-top: 14px;
  text-align: center;
  color: #cbd5e1;
  font-size: 14px;
}

@media (max-width: 900px) {
  .ticket-main-card {
    grid-template-columns: 1fr;
  }

  .ticket-title {
    font-size: 38px;
  }

  .cinema-badge {
    max-width: 230px;
    min-height: 260px;
  }
}

/* PRINT / PDF STYLE */
@media print {
  @page {
    size: A4;
    margin: 12mm;
  }

  html, body {
    background: #eef2f7 !important;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }

  body {
    color: #111827 !important;
  }

  .ticket-page-wrap {
    max-width: 100%;
    margin: 0 auto;
    padding: 0;
  }

  .ticket-main-card {
    grid-template-columns: 1.25fr 0.75fr;
    gap: 20px;
    background: linear-gradient(135deg, #0f172a, #1e293b) !important;
    border: 2px solid #1f2937 !important;
    border-radius: 22px;
    box-shadow: none !important;
    padding: 22px;
    page-break-inside: avoid;
  }

  .ticket-title {
    background: none !important;
    -webkit-text-fill-color: #f59e0b !important;
    color: #f59e0b !important;
    font-size: 38px;
    margin-bottom: 6px;
  }

  .ticket-subtitle {
    color: #dbeafe !important;
    margin-bottom: 16px;
  }

  .ticket-info-box {
    background: #111827 !important;
    border: 2px dashed #facc15 !important;
  }

  .ticket-info-box p {
    color: #ffffff !important;
    font-size: 16px;
  }

  .ticket-info-box strong {
    color: #fde68a !important;
  }

  .ticket-right {
    justify-content: center;
    align-items: center;
  }

  .cinema-badge {
    background: linear-gradient(135deg, #fff4b8, #facc15, #f4d03f) !important;
    border: 2px solid rgba(120, 53, 15, 0.25) !important;
    box-shadow: none !important;
  }

  .badge-title,
  .badge-sub,
  .badge-code,
  .badge-icon {
    color: #5b2100 !important;
  }

  .ticket-actions,
  .ticket-note {
    display: none !important;
  }
}
</style>
</head>
<body>

<div class="ticket-page-wrap">
  <div class="ticket-main-card">

    <div>
      <h1 class="ticket-title">🎟️ E-Ticket</h1>
      <p class="ticket-subtitle">Your booking has been confirmed successfully</p>

      <div class="ticket-info-box">
        <p><strong>Ticket ID:</strong> #<?php echo htmlspecialchars($ticket['id']); ?></p>
        <p><strong>Movie:</strong> <?php echo htmlspecialchars($ticket['movie_name']); ?></p>
        <p><strong>Date:</strong> <?php echo htmlspecialchars($ticket['show_date']); ?></p>
        <p><strong>Show Time:</strong> <?php echo htmlspecialchars($ticket['show_time']); ?></p>
        <p><strong>Seat(s):</strong> <?php echo htmlspecialchars($ticket['seats']); ?></p>
        <p><strong>Total Price:</strong> ৳<?php echo number_format((float)$ticket['total_price'], 2); ?></p>
        <p><strong>Status:</strong> <?php echo htmlspecialchars($ticket['booking_status']); ?></p>
        <p><strong>Booked By:</strong> <?php echo htmlspecialchars($ticket['user_email']); ?></p>
        <p style="margin-bottom:0;"><strong>Booked At:</strong> <?php echo htmlspecialchars($ticket['created_at']); ?></p>
      </div>

      <div class="ticket-actions">
        <button onclick="window.print()" class="btn">Print Ticket</button>
        <a href="mybookings.php" class="btn">My Bookings</a>
        <a href="movies.php" class="btn outline">Book More</a>
      </div>

      <p class="ticket-note">Use Print Ticket, then choose Save as PDF for a colorful ticket copy.</p>
    </div>

    <div class="ticket-right">
      <div class="cinema-badge">
        <div class="badge-shine"></div>
        <div class="badge-icon">🎟️</div>
        <div class="badge-title">CINEMA<br>TICKET</div>
        <div class="badge-sub">CINEBOOK</div>
        <div class="badge-code">ID #<?php echo htmlspecialchars($ticket['id']); ?></div>
      </div>
    </div>

  </div>
</div>

</body>
</html>