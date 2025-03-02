<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in or not admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Check if feedback ID is set
if (isset($_POST['feedback_id'])) {
    $feedback_id = intval($_POST['feedback_id']);

    // Delete feedback from database
    $sql = "DELETE FROM feedback WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $feedback_id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Feedback deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting feedback.";
    }

    $stmt->close();
}

// Redirect back to view_feedback.php
header("Location: view_feedback.php");
exit;
?>
