<?php
session_start();

// Check if user is logged in and is a target user
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Target Dashboard</title>
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
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }
        h1, h2, h3 {
            color: #333;
        }
        textarea, input {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 10px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        #suggestions {
            margin-top: 20px;
            padding: 10px;
            background: #e9ecef;
            border-radius: 5px;
        }
        .logout-btn {
            background-color: #dc3545;
        }
        .logout-btn:hover {
            background-color: #a71d2a;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h1>
        <h2>Agent-Based Information Push Mechanism</h2>
        
        <!-- User Activity - Text Box for Typing -->
        <div>
            <h3>Start Typing Here:</h3>
            <textarea id="user-activity" placeholder="Type something..."></textarea>
        </div>

        <!-- Search Box for User Activity -->
        <div>
            <h3>Search Here:</h3>
            <input type="text" id="search-box" placeholder="Search for something...">
        </div>

        <!-- Area to Display Suggestions -->
        <div id="suggestions">
            <h3>Suggestions:</h3>
            <p id="suggestions-content">No suggestions yet...</p>
        </div>

        <br>

        <!-- Button to go to User Dashboard -->
        <a href="user_dashboard.php">
            <button class="btn">Go to User Dashboard</button>
        </a>

        <!-- Logout Button -->
        <br><br>
        <a href="logout.php">
            <button class="btn logout-btn">Logout</button>
        </a>
    </div>

    <script src="js/script.js"></script>
</body>
</html>
