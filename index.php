<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    header("Location: login.php"); // Redirect to login page if not logged in
    exit;
}

// Redirect based on user role
if ($_SESSION["role"] === "admin") {
    header("Location: admin_dashboard.php"); // Redirect admin to admin dashboard
    exit;
} else {
    header("Location: target_dashboard.php"); // Redirect target users to their dashboard
    exit;
}
?>
