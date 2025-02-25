<?php
session_start();
include 'db_connect.php';

// Ensure only admin can access
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Access denied!");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyword = $_POST["keyword"];
    $category = $_POST["category"];
    $suggestion = $_POST["suggestion"];

    // Insert new rule into database
    $sql = "INSERT INTO rules (keyword, category, response) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $keyword, $category, $suggestion);

    if ($stmt->execute()) {
        header("Location: manage_rules.php");
        exit;
    } else {
        echo "Error: " . $stmt->error;
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
    <title>Add Rule</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h2>Add New Rule</h2>
    <form method="post">
        <label>Keyword:</label>
        <input type="text" name="keyword" required>

        <label>Category:</label>
        <input type="text" name="category" required>

        <label>Suggestion:</label>
        <textarea name="suggestion" required></textarea>

        <button type="submit">Add Rule</button>
    </form>

    <br>
    <a href="manage_rules.php">Back to Manage Rules</a>

</body>
</html>
