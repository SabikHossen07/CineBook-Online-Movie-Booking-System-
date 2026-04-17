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

$booking_id = intval($_GET['id']);
$user_email = $_SESSION['user_email'];

$sql = "SELECT * FROM bookings WHERE id = ? AND user_email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("is", $booking_id, $user_email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: mybookings.php?cancel=error");
    exit();
}

$booking = $result->fetch_assoc();

/* already cancelled */
if ($booking['booking_status'] === 'Cancelled') {
    header("Location: mybookings.php?cancel=already");
    exit();
}

/* show datetime calculate */
$showDateTime = strtotime($booking['show_date'] . ' ' . $booking['show_time']);
$currentTime = time();

/* cancel allowed only before 2 hours of show start */
$cancelDeadline = $showDateTime - (2 * 60 * 60);

if ($currentTime >= $cancelDeadline) {
    header("Location: mybookings.php?cancel=closed");
    exit();
}

/* update status instead of deleting */
$updateSql = "UPDATE bookings SET booking_status = 'Cancelled' WHERE id = ? AND user_email = ?";
$updateStmt = $conn->prepare($updateSql);
$updateStmt->bind_param("is", $booking_id, $user_email);

if ($updateStmt->execute()) {
    header("Location: mybookings.php?cancel=success");
    exit();
} else {
    header("Location: mybookings.php?cancel=error");
    exit();
}
?>