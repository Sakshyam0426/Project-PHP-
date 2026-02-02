<?php
session_start();

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
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Load existing users
    $users = [];
    if (file_exists('users.json')) {
        $users = json_decode(file_get_contents('users.json'), true) ?? [];
    }
    
    // Validation
    if (empty($username)) {
        $error = 'Username is required!';
    } elseif (strpos($username, ' ') !== false) {
        $error = 'Username cannot contain spaces!';
    } elseif (isset($users[$username])) {
        $error = 'Username already exists!';
    } elseif (empty($email)) {
        $error = 'Email is required!';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format!';
    } else {
        // Check if email already exists
        foreach ($users as $user) {
            if ($user['email'] === $email) {
                $error = 'Email already registered!';
                break;
            }
        }
    }
    
    if (empty($error)) {
        if (strlen($password) < 6) {
            $error = 'Password must be at least 6 characters!';
        } elseif ($password !== $confirm_password) {
            $error = 'Passwords do not match!';
        }
    }
    
    // Handle profile picture upload
    $profile_pic = '';
    if (empty($error) && isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed)) {
            $error = 'Only JPG, JPEG, PNG, GIF files allowed!';
        } elseif ($_FILES['profile_pic']['size'] > 2097152) { // 2MB
            $error = 'File too large! Maximum size is 2MB.';
        } else {
            $filename = $username . '_' . time() . '.' . $extension;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/' . $filename)) {
                $profile_pic = $filename;
            } else {
                $error = 'Failed to upload profile picture!';
            }
        }
    }
    
    // If no errors, save user
    if (empty($error)) {
        $users[$username] = [
            'password' => password_hash($password, PASSWORD_DEFAULT),
            'email' => $email,
            'profile_pic' => $profile_pic,
            'created_at' => date('Y-m-d'),
            'remember_token' => null
        ];
        
        file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
        
        $_SESSION['success'] = 'Registration successful! Please login.';
        header('Location: login.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - User Profile System</title>
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
        input[type="email"],
        input[type="password"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            transition: border-color 0.3s;
        }
        input[type="text"]:focus,
        input[type="email"]:focus,
        input[type="password"]:focus,
        input[type="file"]:focus {
            outline: none;
            border-color: #667eea;
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
        .file-info {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Register</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username *</label>
                <input type="text" id="username" name="username" required 
                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required 
                       value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">Password *</label>
                <input type="password" id="password" name="password" required>
                <div class="file-info">Minimum 6 characters</div>
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password *</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <div class="form-group">
                <label for="profile_pic">Profile Picture (Optional)</label>
                <input type="file" id="profile_pic" name="profile_pic" accept="image/*">
                <div class="file-info">JPG, JPEG, PNG, GIF only. Max 2MB</div>
            </div>
            
            <button type="submit" class="btn">Register</button>
        </form>
        
        <div class="link">
            Already have an account? <a href="login.php">Login here</a>
        </div>
    </div>
</body>
</html>