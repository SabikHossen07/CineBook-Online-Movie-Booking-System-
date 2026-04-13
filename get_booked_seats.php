<?php
include 'db.php';

$movie_key = $_GET['movie'] ?? '';
$show_time = $_GET['time'] ?? '';

if ($movie_key === '' || $show_time === '') {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$movie_sql = "SELECT title, show_date FROM movies WHERE movie_key = ?";
$movie_stmt = $conn->prepare($movie_sql);
$movie_stmt->bind_param("s", $movie_key);
$movie_stmt->execute();
$movie_result = $movie_stmt->get_result();

if ($movie_result->num_rows === 0) {
    header('Content-Type: application/json');
    echo json_encode([]);
    exit();
}

$movie = $movie_result->fetch_assoc();
$movie_name = $movie['title'];
$show_date = $movie['show_date'];

$booked_seats = [];

$sql = "SELECT seats FROM bookings WHERE movie_name = ? AND show_date = ? AND show_time = ? AND booking_status = 'Confirmed'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $movie_name, $show_date, $show_time);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $seats = array_map('trim', explode(',', $row['seats']));
    foreach ($seats as $seat) {
        if ($seat !== '') {
            $booked_seats[] = $seat;
        }
    }
}

header('Content-Type: application/json');
echo json_encode(array_values(array_unique($booked_seats)));