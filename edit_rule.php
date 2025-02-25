<?php
session_start();
include 'db_connect.php';

// Ensure only admin can access
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Access denied!");
}

// Check if rule_id is provided
if (!isset($_GET["rule_id"])) {
    die("Rule ID is required!");
}

$rule_id = $_GET["rule_id"];
$sql = "SELECT * FROM rules WHERE rule_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rule_id);
$stmt->execute();
$result = $stmt->get_result();
$rule = $result->fetch_assoc();

if (!$rule) {
    die("Rule not found!");
}

// Update rule if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $keyword = $_POST["keyword"];
    $category = $_POST["category"];
    $suggestion = $_POST["suggestion"];

    $update_sql = "UPDATE rules SET keyword = ?, category = ?, response = ? WHERE rule_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("sssi", $keyword, $category, $suggestion, $rule_id);

    if ($update_stmt->execute()) {
        header("Location: manage_rules.php");
        exit;
    } else {
        echo "Error: " . $update_stmt->error;
    }

    $update_stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Rule</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h2>Edit Rule</h2>
    <form method="post">
        <label>Keyword:</label>
        <input type="text" name="keyword" value="<?php echo htmlspecialchars($rule['keyword']); ?>" required>

        <label>Category:</label>
        <input type="text" name="category" value="<?php echo htmlspecialchars($rule['category']); ?>" required>

        <label>Suggestion:</label>
        <textarea name="suggestion" required><?php echo htmlspecialchars($rule['response']); ?></textarea>

        <button type="submit">Update Rule</button>
    </form>

    <br>
    <a href="manage_rules.php">Back to Manage Rules</a>

</body>
</html>
