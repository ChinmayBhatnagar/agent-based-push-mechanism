<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $feedback_id = $_POST['feedback_id'];
    $user_id = $_POST['user_id'];

    // Update feedback status to "resolved"
    $sql = "UPDATE feedback SET status = 'resolved' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $feedback_id);

    if ($stmt->execute()) {
        // Insert notification for the user
        $notif_sql = "INSERT INTO notifications (user_id, message) VALUES (?, 'Your course request has been fulfilled by the admin.')";
        $notif_stmt = $conn->prepare($notif_sql);
        $notif_stmt->bind_param("i", $user_id);
        $notif_stmt->execute();

        header("Location: view_feedback.php?success=Feedback marked as resolved");
        exit;
    } else {
        echo "Error updating feedback status.";
    }
}
?>
