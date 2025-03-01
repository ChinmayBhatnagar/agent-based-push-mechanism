<?php
session_start();
include 'db_connect.php';

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
    $preferences[$row['preference_category']] = explode(',', $row['value']); // Store as array
}
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST["category"];
    $values = isset($_POST["values"]) ? implode(',', $_POST["values"]) : '';

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
        $stmt->bind_param("sis", $values, $user_id, $category);
    } else {
        // Insert new preference
        $sql = "INSERT INTO user_preferences (user_id, preference_category, value) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iss", $user_id, $category, $values);
    }

    if ($stmt->execute()) {
        $message = "Preferences updated successfully!";
        $preferences[$category] = explode(',', $values); // Update dynamically
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
        select {
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
    </style>
</head>
<body>

    <div class="container">
        <h2>Update Preferences</h2>

        <?php if ($message): ?>
            <p class="message"><?php echo $message; ?></p>
        <?php endif; ?>

        <form method="post">
            <label for="category">Preference Category:</label>
            <select name="category" id="category" required>
                <option value="Favorite Topics">Favorite Topics</option>
                <option value="Preferred Content Type">Preferred Content Type</option>
            </select>

            <label for="values">Select Your Preferences:</label>
            <select name="values[]" id="values" multiple required>
                <option value="Technology">Technology</option>
                <option value="Science">Science</option>
                <option value="Business">Business</option>
                <option value="Entertainment">Entertainment</option>
                <option value="Sports">Sports</option>
                <option value="Articles">Articles</option>
                <option value="Videos">Videos</option>
                <option value="Podcasts">Podcasts</option>
            </select>

            <button type="submit" class="btn">Save Preferences</button>
        </form>

        <a href="user_dashboard.php" class="btn">Back to Dashboard</a>
    </div>

</body>
</html>
