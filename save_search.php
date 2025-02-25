<?php
session_start();

// Database connection
$servername = "localhost";
$username = "root";  // default XAMPP MySQL username
$password = "";  // default XAMPP MySQL password
$dbname = "information_push_db";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

// Get search query from POST request
$search_query = $_POST['search_query'] ?? '';
$user_id = $_SESSION['user_id'] ?? 1; // Use session user_id if available, default to 1

if (!empty($search_query)) {
    $stmt = $conn->prepare("INSERT INTO search_history (user_id, search_query) VALUES (?, ?)");
    $stmt->bind_param("is", $user_id, $search_query);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
