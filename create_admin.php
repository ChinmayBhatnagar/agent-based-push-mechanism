<?php
include 'db_connect.php';

// Define admin credentials
$email = 'admin@gmail.com';
$password = '123456';
$hashed_password = password_hash($password, PASSWORD_DEFAULT); // Hashing the password

// Delete previous admin (if exists)
$sql_delete = "DELETE FROM users WHERE role = 'admin'";
$conn->query($sql_delete);

// Insert new admin with hashed password
$sql_insert = "INSERT INTO users (username, email, password, role) VALUES ('Admin', ?, ?, 'admin')";
$stmt = $conn->prepare($sql_insert);
$stmt->bind_param("ss", $email, $hashed_password);

if ($stmt->execute()) {
    echo "Admin account created successfully!";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
