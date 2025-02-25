<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "user") {
    echo "<p class='no-recommendations'>❗ Unauthorized access.</p>";
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

// Generate recommendations
if (!empty($preferences)) {
    echo "<h3>Based on Your Preferences:</h3>";

    $sql = "SELECT title, link FROM resources WHERE category = ?";
    $stmt = $conn->prepare($sql);

    foreach ($preferences as $category => $value) {
        echo "<div class='category-title'>🔹 $category:</div>";
        $stmt->bind_param("s", $value); // Use value, not category
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<ul class='resource-list'>";
            while ($row = $result->fetch_assoc()) {
                echo "<li>📖 <a href='" . htmlspecialchars($row['link']) . "' target='_blank'>" . htmlspecialchars($row['title']) . "</a></li>";
            }
            echo "</ul>";
        } else {
            echo "<p class='no-recommendations'>No recommendations available for <strong>$value</strong>.</p>";
        }
    }

    $stmt->close();
} else {
    echo "<p class='no-recommendations'>❗ You haven't set any preferences yet.</p>";
}
?>
