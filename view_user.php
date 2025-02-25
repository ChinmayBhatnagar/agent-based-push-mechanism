<?php
session_start();
include 'db_connect.php';

// Redirect if not admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Validate and get user_id
if (!isset($_GET['user_id']) || !is_numeric($_GET['user_id'])) {
    echo "Invalid user ID.";
    exit;
}

$user_id = intval($_GET['user_id']);

// Fetch user details (removed created_at)
$sql = "SELECT user_id, username, email, role FROM users WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    echo "User not found.";
    exit;
}

// Fetch user preferences
$sql = "SELECT preference_category, value FROM user_preferences WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$preferences = $stmt->get_result();
$stmt->close();

// Fetch user search history
$sql = "SELECT search_query, searched_at FROM search_history WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$search_history = $stmt->get_result();
$stmt->close();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View User</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<div class="container">
    <h2>User Details</h2>
    <p><strong>Name:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
    <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
    <p><strong>Role:</strong> <?php echo htmlspecialchars($user['role']); ?></p>

    <h3>Preferences</h3>
    <?php if ($preferences->num_rows > 0): ?>
        <ul>
            <?php while ($pref = $preferences->fetch_assoc()): ?>
                <li><strong><?php echo htmlspecialchars($pref['preference_category']); ?>:</strong> <?php echo htmlspecialchars($pref['value']); ?></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No preferences set.</p>
    <?php endif; ?>

    <h3>Search History</h3>
    <?php if ($search_history->num_rows > 0): ?>
        <ul>
            <?php while ($search = $search_history->fetch_assoc()): ?>
                <li><?php echo htmlspecialchars($search['search_query']) . " - " . $search['searched_at']; ?></li>
            <?php endwhile; ?>
        </ul>
    <?php else: ?>
        <p>No search history.</p>
    <?php endif; ?>

    <a href="manage_users.php" class="btn">Back</a>
</div>

</body>
</html>
