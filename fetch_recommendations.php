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
    $preferences[$row['preference_category']] = explode(',', $row['value']);
}
$stmt->close();

if (!empty($preferences)) {
    echo "<h3>Based on Your Preferences:</h3>";

    $sql = "SELECT title, link FROM resources WHERE ";
    $conditions = [];
    $params = [];
    $types = "";

    foreach ($preferences as $category => $values) {
        foreach ($values as $value) {
            $conditions[] = "(LOWER(category) = LOWER(?) OR LOWER(title) LIKE LOWER(CONCAT('%', ?, '%')))";
            $params[] = $value;
            $params[] = $value;
            $types .= "ss";
        }
    }

    $sql .= implode(" OR ", $conditions);
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<ul class='resource-list'>";
        while ($row = $result->fetch_assoc()) {
            echo "<li>📖 <a href='" . htmlspecialchars($row['link'], ENT_QUOTES, 'UTF-8') . "' target='_blank'>" . htmlspecialchars($row['title'], ENT_QUOTES, 'UTF-8') . "</a></li>";
        }
        echo "</ul>";
    } else {
        echo "<p class='no-recommendations'>No recommendations available.</p>";
    }

    $stmt->close();
} else {
    echo "<p class='no-recommendations'>❗ You haven't set any preferences yet.</p>";
}
?>
