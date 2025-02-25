<?php
session_start();
include 'db_connect.php';

// Check if admin is logged in
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Fetch logs from the database
$sql = "SELECT logs.log_id, users.username, logs.action, logs.details, logs.timestamp 
        FROM logs 
        LEFT JOIN users ON logs.user_id = users.user_id 
        ORDER BY logs.timestamp DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Logs</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="container">
        <h2>System Logs</h2>
        <table border="1">
            <tr>
                <th>Log ID</th>
                <th>Username</th>
                <th>Action</th>
                <th>Details</th>
                <th>Timestamp</th>
                <th>Delete</th>
            </tr>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['log_id']; ?></td>
                    <td><?php echo $row['username'] ? $row['username'] : 'System'; ?></td>
                    <td><?php echo $row['action']; ?></td>
                    <td><?php echo $row['details']; ?></td>
                    <td><?php echo $row['timestamp']; ?></td>
                    <td><a href="delete_log.php?log_id=<?php echo $row['log_id']; ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>

<?php $conn->close(); ?>
