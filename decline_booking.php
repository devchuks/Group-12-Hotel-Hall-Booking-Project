<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = $data['booking_id'];

    $stmt = $pdo->prepare("UPDATE bookings SET status = 'declined' WHERE id = ?");
    if ($stmt->execute([$booking_id])) {
        echo "Booking declined successfully.";
    } else {
        echo "Failed to decline booking.";
    }
}
?>