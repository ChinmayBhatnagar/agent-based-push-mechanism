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

// Delete the rule
$sql = "DELETE FROM rules WHERE rule_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $rule_id);

if ($stmt->execute()) {
    header("Location: manage_rules.php");
    exit;
} else {
    echo "Error deleting rule: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
