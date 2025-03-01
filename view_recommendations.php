<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in or incorrect role
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Recommendations</title>
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
            width: 450px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .category-title {
            font-weight: bold;
            margin-top: 15px;
            color: #007bff;
        }
        .resource-list {
            list-style-type: none;
            padding: 0;
        }
        .resource-list li {
            margin-bottom: 10px;
        }
        .resource-list a {
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        .resource-list a:hover {
            text-decoration: underline;
        }
        .no-recommendations {
            color: #777;
            margin-top: 10px;
        }
        .btn {
            display: inline-block;
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: 0.3s;
            margin-top: 10px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
    </style>
    <script>
        function fetchRecommendations() {
            fetch('fetch_recommendations.php')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    document.getElementById("recommendations").innerHTML = data || "<p class='no-recommendations'>No recommendations available.</p>";
                })
                .catch(error => {
                    console.error("Error fetching recommendations:", error);
                    document.getElementById("recommendations").innerHTML = "<p class='no-recommendations'>Error fetching recommendations.</p>";
                });
        }

        document.addEventListener("DOMContentLoaded", fetchRecommendations);
    </script>
</head>
<body>

    <div class="container">
        <h2>📌 Recommended Resources</h2>
        <div id="recommendations">
            <p>Loading recommendations...</p>
        </div>
        <a href="update_preferences.php" class="btn">⚙️ Update Preferences</a>
        <a href="user_dashboard.php" class="btn">🔙 Back to Dashboard</a>
    </div>

</body>
</html>
