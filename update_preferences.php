<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];
$message = "";

// Fetch current preferences
$sql = "SELECT preference_category, value FROM user_preferences WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$preferences = [];
while ($row = $result->fetch_assoc()) {
    $preferences[$row['preference_category']] = $row['value'];
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST["category"];
    $value = $_POST["value"];

    // Check if preference already exists
    $sql = "SELECT user_id FROM user_preferences WHERE user_id = ? AND preference_category = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $category);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        // Update existing preference
        $sql = "UPDATE user_preferences SET value = ? WHERE user_id = ? AND preference_category = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sis", $value, $user_id, $category);
    } else {
        // Insert new preference
        $sql = "INSERT INTO user_preferences (user_id, preference_category, value) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $category, $value);
    }
    
    if ($stmt->execute()) {
        $message = "Preferences updated successfully!";
        $preferences[$category] = $value; // Update array dynamically
    } else {
        $message = "Error updating preferences.";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Preferences</title>
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
            width: 400px;
            text-align: center;
        }
        h2 {
            color: #333;
        }
        select, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        .btn {
            background-color: #007bff;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .back-btn {
            display: block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
            font-size: 14px;
        }
        .back-btn:hover {
            text-decoration: underline;
        }
        .message {
            color: green;
            font-weight: bold;
            margin-bottom: 10px;
        }
    </style>
    <script>
        function updatePreferenceValue() {
            var category = document.getElementById("category").value;
            var preferences = <?php echo json_encode($preferences); ?>;
            document.getElementById("value").value = preferences[category] || "";
        }
    </script>
</head>
<body>

    <div class="container">
        <h2>Update Preferences</h2>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="category">Preference Category:</label>
            <select name="category" id="category" required onchange="updatePreferenceValue()">
                <option value="Favorite Topics">Favorite Topics</option>
                <option value="Preferred Content Type">Preferred Content Type</option>
            </select>

            <label for="value">Enter Your Preference:</label>
            <textarea name="value" id="value" required></textarea>

            <button type="submit" class="btn">Save Preferences</button>
        </form>

        <a href="user_dashboard.php" class="back-btn">Back to Dashboard</a>
    </div>

    <script>
        // Initialize textarea with the first category's preference
        updatePreferenceValue();
    </script>

</body>
</html>
