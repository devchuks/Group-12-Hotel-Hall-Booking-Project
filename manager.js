// manager.js
document.addEventListener('DOMContentLoaded', function () {
    // Handle booking approval
    window.approveBooking = function (bookingId) {
        fetch('approve_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(response => response.text())
        .then(message => {
            alert(message);
            location.reload(); // Refresh the page to update the bookings list
        });
    };

    // Handle booking decline
    window.declineBooking = function (bookingId) {
        fetch('decline_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(response => response.text())
        .then(message => {
            alert(message);
            location.reload(); // Refresh the page to update the bookings list
        });
    };
});