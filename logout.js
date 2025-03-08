// logout.js
function logout() {
    fetch('logout.php') // Call the logout handler
        .then(() => {
            window.location.href = 'login.html'; // Redirect to the login page
        })
        .catch(error => {
            console.error('Error during logout:', error);
            alert('An error occurred during logout.');
        });
}