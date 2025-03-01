<?php
session_start();
include 'db_connect.php';

// Ensure user is logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $feedback_type = $_POST["feedback_type"];
    $message = trim($_POST["message"]);

    if (!empty($message)) {
        $sql = "INSERT INTO feedback (user_id, feedback_type, message) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $feedback_type, $message);
        
        if ($stmt->execute()) {
            $_SESSION["success"] = "✅ Feedback submitted successfully!";
        } else {
            $_SESSION["error"] = "⚠️ Failed to submit feedback. Please try again.";
        }
        $stmt->close();
    } else {
        $_SESSION["error"] = "⚠️ Message cannot be empty!";
    }
}

header("Location: user_dashboard.php");
exit;
?>
