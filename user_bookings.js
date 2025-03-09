document.addEventListener('DOMContentLoaded', function () {
    const bookingForm = document.getElementById('bookingForm');
    const paymentSection = document.getElementById('paymentSection');
    const paymentForm = document.getElementById('paymentForm');

    // Handle booking form submission
    bookingForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting the traditional way

        // Get form data
        const formData = new FormData(bookingForm);
        formData.append('user_id', userId); // Add user_id to the form data

        // Send booking data to the server
        fetch('booking.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert('Booking successful!');
                // Show the payment form and pre-fill the booking_id
                paymentSection.style.display = 'block';
                document.getElementById('booking_id').value = data.booking_id;
            } else {
                alert('Booking failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your booking.');
        });
    });

    // Handle payment form submission
    paymentForm.addEventListener('submit', function (e) {
        e.preventDefault(); // Prevent the form from submitting the traditional way

        // Get form data
        const formData = new FormData(paymentForm);

        // Send payment data to the server
        fetch('payment.php', {
            method: 'POST',
            body: formData
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                alert('Payment successful!');
                location.reload(); // Refresh the page to update the bookings list
            } else {
                alert('Payment failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your payment. Check the console for details.');
        });
    });

// Handle booking cancellation

window.cancelBooking = function (bookingId) {
    if (confirm('Are you sure you want to cancel this booking? You will receive a 50% refund.')) {
        fetch('cancel_booking.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ booking_id: bookingId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok: ' + response.statusText);
            }
            return response.json();
        })
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload(); // Refresh the page to update the bookings list
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while processing your request. Check the console for details.');
        });
    }
};
});