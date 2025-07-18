<?php
session_start();

// Remove all session variables
session_unset();

// Destroy the session
session_destroy();

// Delete the remember_me cookie if exists
if (isset($_COOKIE['remember_user'])) {
    setcookie('remember_user', '', time() - 3600, "/"); // expire the cookie
}

// Redirect to login page
header('location: index.php');
exit;
?>
