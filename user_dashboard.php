<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Generate CSRF token for feedback form security
if (!isset($_SESSION["csrf_token"])) {
    $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
}

// Fetch resolved resource requests for notification
$query = "SELECT message FROM feedback WHERE user_id = ? AND feedback_type = 'Request' AND status = 'resolved'";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$fulfilled_requests = [];
while ($row = $result->fetch_assoc()) {
    $fulfilled_requests[] = $row['message'];
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/style.css">
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
            padding: 100px;
            border-radius: 10px;
            padding-bottom: 60px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 500px;
            text-align: center;
        }
        .notification-box {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            display: block;
        }
        .notification-box button {
            background: none;
            border: none;
            color: #155724;
            font-size: 16px;
            float: right;
            cursor: pointer;
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
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <h2>Welcome, <?php echo isset($_SESSION["username"]) ? htmlspecialchars($_SESSION["username"]) : "User"; ?>!</h2>

        <!-- Notifications for resolved resource requests -->
        <?php if (!empty($fulfilled_requests)) : ?>
            <div class="notification-box" id="notification-box">
                <strong>🎉 Your Requested Resources Are Available!</strong>
                <button onclick="hideNotification()">✖</button>
                <ul>
                    <?php foreach ($fulfilled_requests as $request) : ?>
                        <li><?php echo htmlspecialchars($request); ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <h3>Your Activity</h3>
        <ul class="options">
            <li><a href="view_recommendations.php" class="btn">📌 View Recommendations</a></li>
            <li><a href="update_preferences.php" class="btn">⚙️ Update Preferences</a></li>
        </ul>

        <!-- Feedback Form -->
        <div class="feedback-form">
            <h3>📝 Submit Feedback</h3>
            <form action="submit_feedback.php" method="post">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

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

            <?php if (isset($_SESSION["success"])) : ?>
                <p class="message success"><?php echo $_SESSION["success"]; ?></p>
                <?php unset($_SESSION["success"]); ?>
            <?php endif; ?>

            <?php if (isset($_SESSION["error"])) : ?>
                <p class="message error"><?php echo $_SESSION["error"]; ?></p>
                <?php unset($_SESSION["error"]); ?>
            <?php endif; ?>
        </div>

        <a href="logout.php" class="btn logout">🚪 Logout</a>
    </div>

    <script>
        function hideNotification() {
            document.getElementById("notification-box").style.display = "none";
        }
    </script>
</body>
</html>
