<?php
session_start();
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Check if log_id is provided
if (isset($_GET["log_id"])) {
    $log_id = $_GET["log_id"];

    // Delete log from database
    $sql = "DELETE FROM logs WHERE log_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $log_id);

    if ($stmt->execute()) {
        header("Location: view_logs.php?message=Log deleted successfully");
    } else {
        header("Location: view_logs.php?error=Failed to delete log");
    }

    $stmt->close();
}

$conn->close();
?>
