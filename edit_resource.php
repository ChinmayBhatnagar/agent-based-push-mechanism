<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Check if resource ID is provided
if (!isset($_GET['id'])) {
    echo "Invalid request.";
    exit;
}

$resource_id = $_GET['id'];

// Fetch resource details
$sql = "SELECT * FROM resources WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $resource_id);
$stmt->execute();
$result = $stmt->get_result();
$resource = $result->fetch_assoc();
$stmt->close();

// If resource not found
if (!$resource) {
    echo "Resource not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Resource</title>
</head>
<body>
    <h2>Edit Resource</h2>
    <form method="post" action="update_resource.php">
        <input type="hidden" name="id" value="<?php echo $resource['id']; ?>">

        <label>Category:</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($resource['category']); ?>" required><br>

        <label>Title:</label>
        <input type="text" name="title" value="<?php echo htmlspecialchars($resource['title']); ?>" required><br>

        <label>Description:</label>
        <textarea name="description" required><?php echo htmlspecialchars($resource['description']); ?></textarea><br>

        <label>Link:</label>
        <input type="text" name="link" value="<?php echo htmlspecialchars($resource['link']); ?>" required><br>

        <input type="submit" value="Update Resource">
    </form>

    <a href="manage_resources.php">Back to Manage Resources</a>
</body>
</html>
