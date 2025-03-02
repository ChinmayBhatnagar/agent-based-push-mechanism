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

</head>
<style>
    /* General styles */
body {
    font-family: Arial, sans-serif;
    background: linear-gradient(to right, #4facfe, #00f2fe);
    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
    margin: 0;
}

/* Login container */
.login-container {
    background: white;
    padding: 20px;
    border-radius: 10px;
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
    text-align: center;
    width: 350px;
}

/* Heading */
h2 {
    margin-bottom: 20px;
    color: #333;
}

/* Input fields */
input[type="email"],
input[type="password"] {
    width: 100%;
    padding: 10px;
    margin: 10px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 16px;
}

/* Login button */
.btn {
    width: 100%;
    padding: 10px;
    background: #4facfe;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 16px;
    transition: 0.3s;
}

.btn:hover {
    background: #00c6ff;
}

/* Error message */
.error {
    color: red;
    font-size: 14px;
    margin-bottom: 10px;
}

/* Signup link */
.signup-link {
    margin-top: 10px;
    font-size: 14px;
}

.signup-link a {
    color: #007bff;
    text-decoration: none;
    font-weight: bold;
}

.signup-link a:hover {
    text-decoration: underline;
}

</style>
<body>
    <div class="login-container">
        <h2>Login</h2>
        <?php if (!empty($error)): ?>
            <p class="error"><?php echo $error; ?></p>
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
