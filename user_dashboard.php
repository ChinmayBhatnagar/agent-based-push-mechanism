<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
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
    <title>User Dashboard</title>
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
        .dashboard-container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 15px;
        }
        .options {
            list-style-type: none;
            padding: 0;
        }
        .options li {
            margin: 15px 0;
        }
        .btn {
            display: block;
            padding: 12px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 16px;
            transition: 0.3s;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .logout {
            margin-top: 20px;
            background: red;
        }
        .logout:hover {
            background: darkred;
        }
        .feedback-form {
            margin-top: 20px;
            text-align: left;
        }
        .feedback-form label {
            font-weight: bold;
            display: block;
            margin-top: 10px;
        }
        .feedback-form select,
        .feedback-form textarea {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        .submit-btn {
            margin-top: 15px;
            background: green;
        }
        .submit-btn:hover {
            background: darkgreen;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo htmlspecialchars($_SESSION["username"]); ?>!</h2>

        <h3>Your Activity</h3>
        <ul class="options">
            <li><a href="view_recommendations.php" class="btn">📌 View Recommendations</a></li>
            <li><a href="update_preferences.php" class="btn">⚙️ Update Preferences</a></li>
        </ul>

        <!-- Feedback Form -->
        <div class="feedback-form">
            <h3>📝 Submit Feedback</h3>
            <form action="submit_feedback.php" method="post">
                <label for="feedback_type">Feedback Type:</label>
                <select name="feedback_type" id="feedback_type" required>
                    <option value="Appreciation">Appreciation</option>
                    <option value="Problem">Problem</option>
                    <option value="Request">Request for Resources</option>
                </select>

                <label for="message">Your Feedback:</label>
                <textarea name="message" id="message" rows="4" required></textarea>

                <button type="submit" class="btn submit-btn">📩 Submit Feedback</button>
            </form>

            <?php
            if (isset($_SESSION["success"])) {
                echo "<p style='color: green;'>" . $_SESSION["success"] . "</p>";
                unset($_SESSION["success"]);
            }
            if (isset($_SESSION["error"])) {
                echo "<p style='color: red;'>" . $_SESSION["error"] . "</p>";
                unset($_SESSION["error"]);
            }
            ?>
        </div>

        <a href="logout.php" class="btn logout">🚪 Logout</a>
    </div>
</body>
</html>
