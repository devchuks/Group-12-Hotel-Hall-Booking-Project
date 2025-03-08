<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $hall_id = $_POST['hall'];
    $date = $_POST['date'];
    $session = $_POST['session'];
    $user_id = $_SESSION['user_id'];

    // Check for double booking
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE hall_id = ? AND date = ? AND session = ?");
    $stmt->execute([$hall_id, $date, $session]);
    if ($stmt->rowCount() > 0) {
        echo json_encode(['status' => 'error', 'message' => 'Hall already booked for this session on the selected date.']);
    } else {
        // Insert booking
        $stmt = $pdo->prepare("INSERT INTO bookings (user_id, hall_id, date, session, status) VALUES (?, ?, ?, ?, 'pending')");
        if ($stmt->execute([$user_id, $hall_id, $date, $session])) {
            $booking_id = $pdo->lastInsertId(); // Get the ID of the newly created booking
            // Reduce hall capacity by 1
        $stmt = $pdo->prepare("UPDATE halls SET capacity = capacity - 1 WHERE id = ?");
        $stmt->execute([$hall_id]);
            echo json_encode(['status' => 'success', 'message' => 'Booking successful!', 'booking_id' => $booking_id]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to book the hall.']);
        }

        

        
    }
}
?>