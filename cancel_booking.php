<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $data = json_decode(file_get_contents('php://input'), true);
        $booking_id = $data['booking_id'];

        // Fetch the booking details
        $stmt = $pdo->prepare("SELECT * FROM bookings WHERE id = ? AND user_id = ?");
        $stmt->execute([$booking_id, $_SESSION['user_id']]);
        $booking = $stmt->fetch();

        if ($booking) {
            // Fetch the payment details
            $stmt = $pdo->prepare("SELECT * FROM payments WHERE booking_id = ?");
            $stmt->execute([$booking_id]);
            $payment = $stmt->fetch();

            if ($payment) {
                // Calculate 50% refund
                $refund_amount = $payment['amount'] * 0.5;

                // Update payment status to 'refunded'
                $stmt = $pdo->prepare("UPDATE payments SET payment_status = 'refunded' WHERE id = ?");
                $stmt->execute([$payment['id']]);

                // Update booking status to 'cancelled'
                $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = ?");
                $stmt->execute([$booking_id]);

                // Log the refund in the refunds table
                $stmt = $pdo->prepare("INSERT INTO refunds (payment_id, refund_amount) VALUES (?, ?)");
                $stmt->execute([$payment['id'], $refund_amount]);

                echo json_encode(['status' => 'success', 'message' => "Booking cancelled successfully. You will receive a 50% refund of $" . $refund_amount . "."]);
            } else {
                echo json_encode(['status' => 'error', 'message' => "Payment details not found."]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => "Booking not found or you do not have permission to cancel this booking."]);
        }
    } catch (Exception $e) {
        // Log the error
        error_log("Cancel booking error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing your request.']);
    }
}
?>