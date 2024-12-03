<?php
session_start(); // Start the session
session_unset(); // Unset all session variables
session_destroy(); // Destroy the session

// Redirect to home page or login page
header("Location: " . $_SERVER['HTTP_REFERER']);
exit();
?>
