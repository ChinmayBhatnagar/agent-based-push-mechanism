<?php
session_start();
include 'db_connect.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION["user_id"]) || $_SESSION["role"] !== "admin") {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category = $_POST["category"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $link = $_POST["link"];
    $added_by = $_SESSION["user_id"]; // Logged-in admin ID

    // Validate inputs
    if (empty($category) || empty($title) || empty($link)) {
        $errors[] = "Category, Title, and Link are required!";
    }

    if (empty($errors)) {
        $sql = "INSERT INTO resources (category, title, description, link, added_by) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $category, $title, $description, $link, $added_by);

        if ($stmt->execute()) {
            $success = "Resource added successfully!";
        } else {
            $errors[] = "Error adding resource.";
        }
        $stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Resource</title>
    <link rel="stylesheet" href="css/style.css"> <!-- Link to external CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            width: 400px;
            text-align: center;
        }
        h2 {
            color: #333;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 15px;
            font-size: 14px;
        }
        .error {
            background-color: #ffcccc;
            color: #cc0000;
        }
        .success {
            background-color: #ccffcc;
            color: #008000;
        }
        label {
            display: block;
            text-align: left;
            font-weight: bold;
            margin-top: 10px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 14px;
        }
        textarea {
            height: 80px;
            resize: vertical;
        }
        .btn {
            background-color: #28a745;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
            margin-top: 15px;
            font-size: 16px;
        }
        .btn:hover {
            background-color: #218838;
        }
        .back-link {
            display: inline-block;
            margin-top: 15px;
            color: #007bff;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Add New Resource</h2>
        
        <?php if (!empty($errors)): ?>
            <div class="message error"><?php echo implode("<br>", $errors); ?></div>
        <?php endif; ?>

        <?php if (!empty($success)): ?>
            <div class="message success"><?php echo $success; ?></div>
        <?php endif; ?>

        <form method="post">
            <label>Category:</label>
            <input type="text" name="category" required>

            <label>Title:</label>
            <input type="text" name="title" required>

            <label>Description:</label>
            <textarea name="description"></textarea>

            <label>Link:</label>
            <input type="url" name="link" required>

            <button type="submit" class="btn">Add Resource</button>
        </form>

        <a href="manage_resources.php" class="back-link">Back to Manage Resources</a>
    </div>
</body>
</html>
