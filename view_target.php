<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Get target user ID from URL
if (!isset($_GET['user_id'])) {
    echo "Invalid request.";
    exit;
}

$user_id = $_GET['user_id'];

// Fetch target user details
$sql = "SELECT username, email FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user = $user_result->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch user preferences
$sql = "SELECT preference_category, value FROM user_preferences WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$preferences_result = $stmt->get_result();
$stmt->close();

// Fetch user search history
$sql = "SELECT search_query, searched_at FROM search_history WHERE user_id = ? ORDER BY searched_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$history_result = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Target</title>
    <link rel="stylesheet" href="css/style.css"> <!-- External CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
        }
        h2, h3 {
            color: #333;
            text-align: center;
        }
        .info {
            background: #e9ecef;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
        }
        .info p {
            margin: 5px 0;
            font-weight: bold;
        }
        .list {
            list-style: none;
            padding: 0;
        }
        .list li {
            background: #ffffff;
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
            box-shadow: 0px 0px 5px rgba(0, 0, 0, 0.1);
        }
        .btn {
            display: block;
            text-align: center;
            padding: 10px;
            margin-top: 15px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
        }
        .btn:hover {
            background: #0056b3;
        }
        .no-data {
            text-align: center;
            color: #777;
            font-style: italic;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>👤 Target User Details</h2>
        <div class="info">
            <p>📛 <strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
            <p>📧 <strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
        </div>
        
        <h3>📌 Preferences</h3>
        <ul class="list">
            <?php if ($preferences_result->num_rows > 0): ?>
                <?php while ($row = $preferences_result->fetch_assoc()): ?>
                    <li>🔹 <strong><?php echo htmlspecialchars($row['preference_category']); ?>:</strong> <?php echo htmlspecialchars($row['value']); ?></li>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No preferences set.</p>
            <?php endif; ?>
        </ul>
        
        <h3>📖 Search History</h3>
        <ul class="list">
            <?php if ($history_result->num_rows > 0): ?>
                <?php while ($row = $history_result->fetch_assoc()): ?>
                    <li>🔍 <strong><?php echo htmlspecialchars($row['search_query']); ?></strong> (📅 <?php echo $row['searched_at']; ?>)</li>
                <?php endwhile; ?>
            <?php else: ?>
                <p class="no-data">No search history found.</p>
            <?php endif; ?>
        </ul>
        
       
