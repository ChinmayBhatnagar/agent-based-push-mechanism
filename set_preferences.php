<?php
session_start();
include 'db_connect.php';

// Check if user is logged in
if (!isset($_SESSION["user_id"])) {
    echo "<div class='message error'>Access denied! Redirecting to login...</div>";
    header("refresh:2; url=login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION["user_id"];
    $preferences = isset($_POST["preferences"]) ? $_POST["preferences"] : []; // Handle case when no preference is selected

    // Delete old preferences
    $delete_sql = "DELETE FROM user_preferences WHERE user_id = ?";
    $stmt = $conn->prepare($delete_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    // Insert new preferences
    if (!empty($preferences)) {
        $insert_sql = "INSERT INTO user_preferences (user_id, preference_category) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);

        foreach ($preferences as $preference) {
            $stmt->bind_param("is", $user_id, $preference);
            $stmt->execute();
        }

        $stmt->close();
    }

    $conn->close();

    echo "<div class='message success'>Preferences updated! Redirecting...</div>";
    header("refresh:2; url=user_dashboard.php");
    exit;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Preferences</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to external CSS -->
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
            width: 400px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .error {
            background-color: #ffcccc;
            color: #cc0000;
        }
        .success {
            background-color: #ccffcc;
            color: #008000;
        }
        .preferences {
            text-align: left;
            margin-bottom: 20px;
        }
        .preferences label {
            display: block;
            font-size: 16px;
            margin-bottom: 10px;
        }
        input[type="checkbox"] {
            margin-right: 10px;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #218838;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Select Your Preferences</h2>
        <form method="post" action="">
            <div class="preferences">
                <label><input type="checkbox" name="preferences[]" value="Programming"> Programming</label>
                <label><input type="checkbox" name="preferences[]" value="Database"> Database</label>
                <label><input type="checkbox" name="preferences[]" value="Web Development"> Web Development</label>
            </div>
            <button type="submit" class="btn">Save Preferences</button>
        </form>
        <a href="user_dashboard.php" class="back-link">Back to Dashboard</a>
    </div>
</body>
</html>
