<?php
session_start();
require_once '../includes/database.php';

// Enhanced debugging
error_log("\n\n=== NEW LOGIN ATTEMPT ===");
error_log("Time: " . date('Y-m-d H:i:s'));
error_log("POST data: " . print_r($_POST, true));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    error_log("Attempting login for username: '$username'");

    $stmt = $connection->prepare("SELECT admin_id, password_hash FROM admins WHERE username = ? AND is_active = 1");
    if (!$stmt) {
        error_log("Prepare failed: " . $connection->error);
        die("System error. Please try later.");
    }
    
    $stmt->bind_param("s", $username);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        die("System error. Please try later.");
    }
    
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $admin = $result->fetch_assoc();
        error_log("Admin found. Full record: " . print_r($admin, true));
        error_log("Stored hash: " . $admin['password_hash']);
        
        if (password_verify($password, $admin['password_hash'])) {
            error_log("Password verification SUCCESSFUL");
            
            $_SESSION['admin_id'] = $admin['admin_id'];
            $_SESSION['is_admin'] = true;
            $_SESSION['login_time'] = time();
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            
            session_regenerate_id(true);
            
            error_log("Login successful, redirecting to dashboard");
            header("Location: dashboard.php");
            exit;
        } else {
            error_log("Password verification FAILED");
            error_log("Input password: '$password'");
            error_log("Hash verification result: " . (password_verify($password, $admin['password_hash']) ? 'true' : 'false'));
            $error = "Invalid credentials";
        }
    } else {
        error_log("User not found or inactive");
        $error = "Invalid credentials";
    }
    
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kavirex Admin Portal</title>
    <style>
        :root {
            --primary: #2c3e50;    /* Dark blue */
            --secondary: #e74c3c;  /* Red accent */
            --light: #ecf0f1;      /* Light gray */
        }
        
        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('../assets/images/login-bg.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            width: 380px;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 8px;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        
        .logo {
            width: 100px;
            margin-bottom: 20px;
        }
        
        h1 {
            color: var(--primary);
            margin-bottom: 30px;
            font-weight: 600;
        }
        
        .input-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: var(--primary);
            font-weight: 500;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(44, 62, 80, 0.1);
        }
        
        button {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        button:hover {
            background: #1a252f;
        }
        
        .error {
            color: var(--secondary);
            margin: 15px 0;
            padding: 10px;
            background: rgba(231, 76, 60, 0.1);
            border-radius: 4px;
        }
        
        .security-notice {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 25px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <img src="../assets/images/logo.png" alt="Kavirex Logo" class="logo">
        <h1>Admin Portal</h1>
        
        <?php if (isset($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="off">
            </div>
            
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Sign In</button>
        </form>
        
        <p class="security-notice">
            <i class="fas fa-lock"></i> Restricted access. All activities are logged.
        </p>
    </div>
</body>
</html>