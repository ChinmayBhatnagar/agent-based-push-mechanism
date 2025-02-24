<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Check if resource ID is provided
if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$resource_id = $_GET['id'];

// Delete the resource
$sql = "DELETE FROM resources WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resource_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Resource deleted successfully!";
} else {
    $_SESSION['error_message'] = "Failed to delete resource.";
}

$stmt->close();
$conn->close();

// Redirect back to manage_resources.php
header("Location: manage_resources.php");
exit;
