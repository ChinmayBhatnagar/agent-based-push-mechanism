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
<style>
    /* General styles */
body {
    font-family: Arial, sans-serif;
    /* background: linear-gradient(to right, #4facfe, #00f2fe); */
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
}

/* Container */
.container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 90%;
    max-width: 900px;
    overflow-x: auto;
}

/* Heading */
h2 {
    color: #333;
    margin-bottom: 20px;
}

/* Table styles */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
}

thead {
    background: #4facfe;
    color: white;
}

th, td {
    padding: 10px;
    border: 1px solid #ddd;
    text-align: left;
}

tbody tr:nth-child(even) {
    background: #f9f9f9;
}

/* Buttons */
.edit-btn, .delete-btn, .add-btn {
    display: inline-block;
    padding: 6px 12px;
    color: white;
    border-radius: 5px;
    text-decoration: none;
    transition: 0.3s;
    font-size: 14px;
}

.edit-btn {
    background: #f39c12;
}

.edit-btn:hover {
    background: #e67e22;
}

.delete-btn {
    background: #e74c3c;
}

.delete-btn:hover {
    background: #c0392b;
}

.add-btn {
    background: #2ecc71;
    padding: 8px 16px;
    font-weight: bold;
    margin-top: 15px;
    display: inline-block;
}

.add-btn:hover {
    background: #27ae60;
}

</style>
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
