<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];

    // Fetch user from database
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        
        // Verify password
        if (password_verify($password, $user['password'])) {
            $_SESSION["user_id"] = $user["user_id"];
            $_SESSION["username"] = $user["username"];
            $_SESSION["email"] = $user["email"];
            $_SESSION["role"] = $user["role"];

            // Log successful login
            $log_sql = "INSERT INTO logs (user_id, action, details) VALUES (?, 'Login Success', ?)";
            if ($log_stmt = $conn->prepare($log_sql)) {
                $details = "User logged in successfully. IP: " . $_SERVER['REMOTE_ADDR'];
                $log_stmt->bind_param("is", $user["user_id"], $details);
                $log_stmt->execute();
                $log_stmt->close();
            }

            // Redirect user based on role
            header("Location: " . ($user["role"] === "admin" ? "admin_dashboard.php" : "target_dashboard.php"));
            exit;
        }
    }

    // Failed login attempt
    $error = "Invalid email or password!";
    
    // Log failed login attempt
    $log_sql = "INSERT INTO logs (user_id, action, details) VALUES (NULL, 'Login Failed', ?)";
    if ($log_stmt = $conn->prepare($log_sql)) {
        $sanitized_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
        $details = "Failed login attempt for email: $sanitized_email. IP: " . $_SERVER['REMOTE_ADDR'];
        $log_stmt->bind_param("s", $details);
        $log_stmt->execute();
        $log_stmt->close();
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
    <title>Login</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <p class="error"> <?php echo $error; ?> </p>
        <?php endif; ?>
        <form method="post" action="">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" class="btn">Login</button>
        </form>
        <p class="signup-link">Don't have an account? <a href="signup.php">Sign up here</a></p>
    </div>
</body>
</html>
