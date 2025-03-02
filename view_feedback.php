<?php
session_start();
include 'db_connect.php';

// Redirect if not logged in or not admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

// Fetch feedback from the database
$sql = "SELECT feedback.id, users.username, users.user_id, feedback.feedback_type, feedback.message, feedback.status, feedback.created_at 
        FROM feedback 
        JOIN users ON feedback.user_id = users.user_id 
        ORDER BY feedback.created_at DESC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Feedback</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin: auto;
        }
        h2 {
            text-align: center;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        .delete-btn, .resolve-btn {
            padding: 5px 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            color: white;
        }
        .delete-btn {
            background: red;
        }
        .delete-btn:hover {
            background: darkred;
        }
        .resolve-btn {
            background: green;
        }
        .resolve-btn:hover {
            background: darkgreen;
        }
        .back-btn {
            display: inline-block;
            margin-top: 20px;
            padding: 10px;
            background: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        .back-btn:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2>📩 User Feedback</h2>

        <?php if ($result->num_rows > 0) : ?>
            <table>
                <tr>
                    <th>Username</th>
                    <th>Type</th>
                    <th>Message</th>
                    <th>Status</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['username']); ?></td>
                        <td><?php echo htmlspecialchars($row['feedback_type']); ?></td>
                        <td><?php echo htmlspecialchars($row['message']); ?></td>
                        <td>
                            <?php if ($row['status'] === 'pending') : ?>
                                <span style="color: red;">Pending</span>
                            <?php else : ?>
                                <span style="color: green;">Resolved ✅</span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $row['created_at']; ?></td>
                        <td>
                            <?php if ($row['feedback_type'] === 'Request' && $row['status'] === 'pending') : ?>
                                <form action="mark_feedback_resolved.php" method="post">
                                    <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $row['user_id']; ?>">
                                    <button type="submit" class="resolve-btn">✅ Mark as Resolved</button>
                                </form>
                            <?php endif; ?>
                            <form action="delete_feedback.php" method="post" onsubmit="return confirm('Are you sure you want to delete this feedback?');">
                                <input type="hidden" name="feedback_id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="delete-btn">🗑 Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else : ?>
            <p>No feedback available.</p>
        <?php endif; ?>

        <a href="admin_dashboard.php" class="back-btn">⬅️ Back to Dashboard</a>
    </div>

</body>
</html>
