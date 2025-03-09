<?php
session_start();
require 'db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'manager') {
    header('Location: login.html');
    exit();
}

// Fetch all bookings
try {
    $stmt = $pdo->prepare("SELECT bookings.*, users.name AS user_name, halls.name AS hall_name 
                           FROM bookings 
                           JOIN users ON bookings.user_id = users.id 
                           JOIN halls ON bookings.hall_id = halls.id 
                           ORDER BY date ASC");
    $stmt->execute();
    $bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Error fetching bookings: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h2>Welcome, Manager!</h2>
    <button onclick="logout()">Logout</button>

    <!-- Hall Capacities -->
<h3>Hall Capacities</h3>
<table id="hallCapacities">
    <thead>
        <tr>
            <th>Hall</th>
            <th>Capacity</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $stmt = $pdo->prepare("SELECT * FROM halls");
        $stmt->execute();
        $halls = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($halls as $hall): ?>
            <tr>
                <td><?= htmlspecialchars($hall['name']) ?></td>
                <td><?= htmlspecialchars($hall['capacity']) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

    <!-- All Bookings -->
    <h3>All Bookings</h3>
    <table id="bookingsTable">
        <thead>
            <tr>
                <th>User</th>
                <th>Hall</th>
                <th>Date</th>
                <th>Session</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= htmlspecialchars($booking['user_name']) ?></td>
                    <td><?= htmlspecialchars($booking['hall_name']) ?></td>
                    <td><?= htmlspecialchars($booking['date']) ?></td>
                    <td><?= htmlspecialchars($booking['session']) ?></td>
                    <td><?= htmlspecialchars($booking['status']) ?></td>
                    <td>
                        <?php if ($booking['status'] === 'pending'): ?>
                            <button onclick="approveBooking(<?= $booking['id'] ?>)">Approve</button>
                            <button onclick="declineBooking(<?= $booking['id'] ?>)">Decline</button>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <script src="logout.js"></script>
    <script src="manager.js"></script>
</body>
</html>