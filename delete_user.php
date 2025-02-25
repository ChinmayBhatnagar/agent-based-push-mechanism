<?php
session_start();
include 'db_connect.php';

// Redirect if not admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['user_id'])) {
    echo "Invalid user ID.";
    exit;
}

$user_id = intval($_GET['user_id']);

// Delete user preferences
$sql = "DELETE FROM user_preferences WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Delete user search history
$sql = "DELETE FROM search_history WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

// Delete the user
$sql = "DELETE FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->close();

$conn->close();

header("Location: manage_users.php");
exit;
?>
