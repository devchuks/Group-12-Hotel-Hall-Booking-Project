<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.html');
    exit();
}

$user_id = $_SESSION['user_id'];
$current_date = date('Y-m-d');

// Initialize variables
$upcoming_bookings = [];
$past_bookings = [];

// Fetch upcoming bookings (excluding cancelled bookings)
try {
    $stmt = $pdo->prepare("SELECT bookings.*, halls.name AS hall_name 
                           FROM bookings 
                           JOIN halls ON bookings.hall_id = halls.id 
                           WHERE user_id = ? AND date >= ? AND status != 'cancelled'
                           ORDER BY date ASC");
    $stmt->execute([$user_id, $current_date]);
    $upcoming_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching upcoming bookings: " . $e->getMessage());
}

// Fetch past or cancelled bookings
try {
    $stmt = $pdo->prepare("SELECT bookings.*, halls.name AS hall_name, payments.amount AS original_price, refunds.refund_amount 
                           FROM bookings 
                           JOIN halls ON bookings.hall_id = halls.id 
                           LEFT JOIN payments ON bookings.id = payments.booking_id
                           LEFT JOIN refunds ON payments.id = refunds.payment_id
                           WHERE user_id = ? AND (date < ? OR status = 'cancelled')
                           ORDER BY date DESC");
    $stmt->execute([$user_id, $current_date]);
    $past_bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching past or cancelled bookings: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="user-dashboard-header">
        <h2>User Dashboard</h2>
            <button onclick="logout()">Logout</button>      
    </header>

    <!-- Booking Form -->
    <h3 class="book-a-hall">Book a Hall</h3>
    <div class="booking-form-container">
        <form id="bookingForm" class="booking-form">
            <label for="hall">Select Hall:</label>
            <select id="hall" name="hall" required>
                <option value="1">Hall A</option>
                <option value="2">Hall B</option>
                <option value="3">Hall C</option>
            </select>
    
            <label for="date">Select Date:</label>
            <input type="date" id="date" name="date" required>
    
            <label for="session">Select Session:</label>
            <select id="session" name="session" required>
                <option value="morning">Morning</option>
                <option value="afternoon">Afternoon</option>
                <option value="both">Both</option>
            </select>
    
            <button type="submit">Book Now</button>
        </form>
    </div>

   <!-- Payment Form -->
   <div class="payment-section-container" id="paymentSection" style="display: none;">
    <h3>Make a Payment</h3>
    <div class="payment-form-container">
        <form id="paymentForm">
            <label for="booking_id">Booking ID:</label>
            <input type="number" id="booking_id" name="booking_id" required readonly>
    
            <label for="amount">Amount:</label>
            <input type="number" id="amount" name="amount" required>
    
            <label for="payment_method">Payment Method:</label>
            <select id="payment_method" name="payment_method" required>
                <option value="paypal">PayPal</option>
                <option value="stripe">Stripe</option>
            </select>
    
            <button type="submit">Pay Now</button>
        </form>
    </div>
  </div>

    <!-- Upcoming Bookings -->
<div class="bookings-container">
 <h3>Upcoming Bookings</h3>
 <div class="upcoming-bookings-container">
     <?php if (!empty($upcoming_bookings)): ?>
         <table id="upcomingBookings">
             <thead>
                 <tr>
                     <th>Hall</th>
                     <th>Date</th>
                     <th>Session</th>
                     <th>Status</th>
                     <th>Action</th>
                 </tr>
             </thead>
             <tbody>
                 <?php foreach ($upcoming_bookings as $booking): ?>
                     <tr>
                         <td><?= htmlspecialchars($booking['hall_name']) ?></td>
                         <td><?= htmlspecialchars($booking['date']) ?></td>
                         <td><?= htmlspecialchars($booking['session']) ?></td>
                         <td><?= htmlspecialchars($booking['status']) ?></td>
                         <td>
                             <?php if ($booking['status'] === 'approved'): ?>
                                 <button onclick="cancelBooking(<?= $booking['id'] ?>)">Cancel</button>
                             <?php endif; ?>
                         </td>
                     </tr>
                 <?php endforeach; ?>
             </tbody>
         </table>
     <?php else: ?>
         <p>No upcoming bookings found.</p>
     <?php endif; ?>
 </div>
    
        <!-- Past or Cancelled Bookings -->
    
    <h3>Past or Cancelled Bookings</h3>
    <div class="past-bookings-container">
        <?php if (!empty($past_bookings)): ?>
            <table id="pastBookings">
                <thead>
                    <tr>
                        <th>Hall</th>
                        <th>Date</th>
                        <th>Session</th>
                        <th>Status</th>
                        <th>Original Price</th>
                        <th>Refunded Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($past_bookings as $booking): ?>
                        <tr>
                            <td><?= htmlspecialchars($booking['hall_name']) ?></td>
                            <td><?= htmlspecialchars($booking['date']) ?></td>
                            <td><?= htmlspecialchars($booking['session']) ?></td>
                            <td><?= htmlspecialchars($booking['status']) ?></td>
                            <td>$<?= htmlspecialchars($booking['original_price'] ?? '0.00') ?></td>
                            <td>
                                <?php if ($booking['status'] === 'cancelled'): ?>
                                    $<?= htmlspecialchars($booking['refund_amount'] ?? '0.00') ?>
                                <?php else: ?>
                                    N/A
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No past or cancelled bookings found.</p>
        <?php endif; ?>
    </div>
</div>

    <!-- Pass user_id to JavaScript -->
    <script>
        const userId = <?= json_encode($_SESSION['user_id']) ?>;
    </script>
    <script src="logout.js"></script>
    <script src="user_bookings.js"></script>
</body>
</html>