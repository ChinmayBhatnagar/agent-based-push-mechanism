<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Fetch all users (targets)
$sql = "SELECT user_id, username, email FROM users WHERE role = 'user'";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .container {
            width: 80%;
            margin: auto;
            overflow: hidden;
        }
        .header {
            background: #333;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .menu {
            background: #444;
            padding: 15px;
            text-align: center;
        }
        .menu a {
            color: white;
            text-decoration: none;
            padding: 10px 15px;
            display: inline-block;
        }
        .menu a:hover {
            background: #555;
            border-radius: 5px;
        }
        .content {
            background: white;
            padding: 20px;
            margin-top: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .user-list {
            list-style: none;
            padding: 0;
        }
        .user-list li {
            background: #e2e2e2;
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .user-list a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .logout {
            display: inline-block;
            padding: 10px 15px;
            background: red;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-top: 20px;
        }
        .logout:hover {
            background: darkred;
        }
    </style>
</head>
<body>
    <div class="header">
        <h2>Welcome, Admin <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>
    </div>
    
    <div class="menu">
        <a href="manage_users.php">Manage Users</a>
        <a href="view_logs.php">View System Logs</a>
        <a href="manage_rules.php">Manage Push Rules</a>
        <a href="manage_resources.php">Manage Resources</a>
        <a href="view_feedback.php" class="btn">📩 View User Feedback</a>

    </div>

    <div class="container">
        <div class="content">
            <h3>All Targets (Users)</h3>
            <ul class="user-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <a href="view_target.php?user_id=<?php echo $row['user_id']; ?>">
                            <?php echo htmlspecialchars($row['username']) . " (" . htmlspecialchars($row['email']) . ")"; ?>
                        </a>
                    </li>
                <?php endwhile; ?>
            </ul>
        </div>
    </div>

    <div class="container" style="text-align: center;">
        <a href="logout.php" class="logout">Logout</a>
    </div>
</body>
</html>
