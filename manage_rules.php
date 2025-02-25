<?php
session_start();
include 'db_connect.php';

// Ensure only admin can access
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    die("Access denied!");
}

// Fetch rules from the database
$sql = "SELECT * FROM rules ORDER BY created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Rules</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>

    <h2>Manage Rules</h2>

    <table border="1">
        <tr>
            <th>Rule ID</th>
            <th>Keyword</th>
            <th>Category</th>
            <th>Response</th>
            <th>Created At</th>
            <th>Actions</th>
        </tr>
        
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?php echo $row['rule_id']; ?></td>
                <td><?php echo $row['keyword']; ?></td>
                <td><?php echo isset($row['category']) ? $row['category'] : 'N/A'; ?></td>
                <td><?php echo isset($row['response']) ? $row['response'] : 'N/A'; ?></td>
                <td><?php echo $row['created_at']; ?></td>
                <td>
                    <a href="edit_rule.php?rule_id=<?php echo $row['rule_id']; ?>">Edit</a> | 
                    <a href="delete_rule.php?rule_id=<?php echo $row['rule_id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <br>
    <a href="add_rule.php">Add New Rule</a>

</body>
</html>

<?php
$conn->close();
?>
