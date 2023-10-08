<?php
session_start(); // Start the session (if not already started)

function logout() {
    // Unset all session variables
    $_SESSION = array();

    // Destroy the session
    session_destroy();

    // Redirect to the login page or any other desired page
    header("Location: login.php"); // Replace "login.php" with your login page URL
    exit; // Terminate the script after redirection
}

// Call the logout function when a logout action is triggered
if (isset($_GET['logout'])) {
    logout();
}
?>
