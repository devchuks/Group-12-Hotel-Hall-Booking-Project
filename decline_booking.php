<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $booking_id = $data['booking_id'];

    // Fetch the booking details
    $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ?");
    $stmt->execute([$booking_id]);
    $booking = $stmt->fetch();

    if ($booking) {
        // Update booking status to 'declined'
        $stmt = $pdo->prepare("UPDATE bookings SET status = 'declined' WHERE id = ?");
        $stmt->execute([$booking_id]);

        // Increase hall capacity by 1
        $stmt = $pdo->prepare("UPDATE halls SET capacity = capacity + 1 WHERE id = ?");
        $stmt->execute([$booking['hall_id']]);

        echo "Booking declined successfully.";
    } else {
        echo "Failed to decline booking.";
    }
}
?>