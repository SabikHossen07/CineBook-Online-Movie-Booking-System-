<?php
session_start();
include 'db.php';

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'manager') {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['status'])) {
    $id = (int) $_GET['id'];
    $status = trim($_GET['status']);

    $allowed = ['booked', 'available', 'cancelled', 'pending'];

    if (in_array($status, $allowed)) {
        $conn->query("UPDATE bookings SET status='$status' WHERE id=$id");
    }
}

header("Location: manager_seats.php");
exit();
?>