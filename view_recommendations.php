<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION["user_id"];

// Fetch user preferences
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
</head>
<body>

    <div class="container">
        <h2>📌 Recommended Resources</h2>

        <div class="recommendations">
        <?php
        if (!empty($preferences)) {
            echo "<h3>Based on Your Preferences:</h3>";

            // Prepare SQL query to fetch matching resources
            $sql = "SELECT title, link FROM resources WHERE category = ?";
            $stmt = $conn->prepare($sql);

            foreach ($preferences as $category => $value) {
                echo "<div class='category-title'>🔹 $category:</div>";
                $stmt->bind_param("s", $category);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    echo "<ul class='resource-list'>";
                    while ($row = $result->fetch_assoc()) {
                        echo "<li>📖 <a href='" . htmlspecialchars($row['link']) . "' target='_blank'>" . htmlspecialchars($row['title']) . "</a></li>";
                    }
                    echo "</ul>";
                } else {
                    echo "<p class='no-recommendations'>No recommendations available for <strong>$category</strong>.</p>";
                }
            }

            $stmt->close();
        } else {
            echo "<p class='no-recommendations'>❗ You haven't set any preferences yet.</p>";
            echo "<a href='update_preferences.php' class='btn'>⚙️ Set Preferences</a>";
        }
        ?>
        </div>

        <a href="user_dashboard.php" class="btn">🔙 Back to Dashboard</a>
    </div>

</body>
</html>
