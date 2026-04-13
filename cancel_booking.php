<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id'])) {
    $booking_id = intval($_GET['id']);
    $user_email = $_SESSION['user_email'];

    $sql = "DELETE FROM bookings WHERE id = ? AND user_email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $booking_id, $user_email);

    if ($stmt->execute()) {
        header("Location: mybookings.php?cancel=success");
        exit();
    } else {
        header("Location: mybookings.php?cancel=error");
        exit();
    }
} else {
    header("Location: mybookings.php");
    exit();
}
?>