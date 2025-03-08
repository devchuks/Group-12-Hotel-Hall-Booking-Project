<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        $booking_id = $_POST['booking_id'];
        $amount = $_POST['amount'];
        $payment_method = $_POST['payment_method'];
        $transaction_id = uniqid(); // Generate a unique transaction ID (fake)

        // Simulate payment processing (always assume success for demo purposes)
        $payment_successful = true;

        if ($payment_successful) {
            // Insert payment details into the database
            $stmt = $pdo->prepare("INSERT INTO payments (booking_id, amount, payment_method, transaction_id) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$booking_id, $amount, $payment_method, $transaction_id])) {
                // Update booking status to 'paid'
                $stmt = $pdo->prepare("UPDATE bookings SET payment_status = 'paid' WHERE id = ?");
                $stmt->execute([$booking_id]);

                echo json_encode(['status' => 'success', 'message' => 'Payment processed successfully.']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to record payment.']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Payment failed.']);
        }
    } catch (Exception $e) {
        // Log the error
        error_log("Payment error: " . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'An error occurred while processing your payment.']);
    }
}
?>