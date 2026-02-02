<?php
session_start();

// Check for remember me cookie
if (!isset($_SESSION['user']) && isset($_COOKIE['remember_token'])) {
    $users = [];
    if (file_exists('users.json')) {
        $users = json_decode(file_get_contents('users.json'), true) ?? [];
    }
    
    // Find user with matching token
    foreach ($users as $username => $user) {
        if (isset($user['remember_token']) && $user['remember_token'] === $_COOKIE['remember_token']) {
            $_SESSION['user'] = $username;
            header('Location: profile.php');
            exit;
        }
    }
}

// If already logged in, redirect to profile
if (isset($_SESSION['user'])) {
    header('Location: profile.php');
    exit;
}

$error = '';
$success = '';

// Get flash messages
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    // Load users
    $users = [];
    if (file_exists('users.json')) {
        $users = json_decode(file_get_contents('users.json'), true) ?? [];
    }
    
    // Check if user exists
    if (!isset($users[$username])) {
        $error = 'Invalid username or password!';
    } else {
        // Verify password
        if (password_verify($password, $users[$username]['password'])) {
            // Login successful
            $_SESSION['user'] = $username;
            
            // Handle Remember Me
            if ($remember) {
                // Generate secure token
                $token = bin2hex(random_bytes(32));
                $users[$username]['remember_token'] = $token;
                file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
                
                // Set cookie for 30 days
                setcookie('remember_token', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
            }
            
            header('Location: profile.php');
            exit;
        } else {
            $error = 'Invalid username or password!';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - User Profile System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="password"]:focus {
            outline: none;
            border-color: #667eea;
        }
        .checkbox-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
        }
        input[type="checkbox"] {
            margin-right: 8px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }
        .checkbox-group label {
            margin: 0;
            cursor: pointer;
            font-weight: 400;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .error {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #c33;
        }
        .success {
            background: #efe;
            color: #3c3;
            padding: 12px;
            border-radius: 5px;
            margin-bottom: 20px;
            border-left: 4px solid #3c3;
        }
        .link {
            text-align: center;
            margin-top: 20px;
            color: #666;
        }
        .link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }
        .link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Login</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="checkbox-group">
                <input type="checkbox" id="remember" name="remember">
                <label for="remember">Remember Me (30 days)</label>
            </div>
            
            <button type="submit" class="btn">Login</button>
        </form>
        
        <div class="link">
            Don't have an account? <a href="register.php">Register here</a>
        </div>
    </div>
</body>
</html>