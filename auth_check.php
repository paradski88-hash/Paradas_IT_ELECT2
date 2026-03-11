<?php
// Reusable authentication guard.
// Call this after including connection.php (or anywhere) to ensure only logged-in admins access the page.

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check for admin session (set by index.php on successful login)
if (!isset($_SESSION['a_id']) || empty($_SESSION['a_id'])) {
    // Not logged in — redirect to login page
    header('Location: index.php');
    exit;
}
