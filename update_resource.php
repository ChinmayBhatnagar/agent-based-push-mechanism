<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $category = $_POST["category"];
    $title = $_POST["title"];
    $description = $_POST["description"];
    $link = $_POST["link"];

    // Update resource in database
    $sql = "UPDATE resources SET category = ?, title = ?, description = ?, link = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $category, $title, $description, $link, $id);

    if ($stmt->execute()) {
        header("Location: manage_resources.php?success=updated");
        exit;
    } else {
        echo "Error updating resource.";
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request.";
}
?>
