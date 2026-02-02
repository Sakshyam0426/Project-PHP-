<?php
session_start();

// Check if logged in
if (!isset($_SESSION['user'])) {
    header('Location: login.php');
    exit;
}

$username = $_SESSION['user'];

// Load user data
$users = [];
if (file_exists('users.json')) {
    $users = json_decode(file_get_contents('users.json'), true) ?? [];
}

if (!isset($users[$username])) {
    // User doesn't exist, logout
    session_destroy();
    header('Location: login.php');
    exit;
}

$user = $users[$username];
$error = '';
$success = '';

// Get flash messages
if (isset($_SESSION['success'])) {
    $success = $_SESSION['success'];
    unset($_SESSION['success']);
}

// Handle profile picture update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_pic'])) {
    if ($_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $extension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
        
        if (!in_array($extension, $allowed)) {
            $error = 'Only JPG, JPEG, PNG, GIF files allowed!';
        } elseif ($_FILES['profile_pic']['size'] > 2097152) { // 2MB
            $error = 'File too large! Maximum size is 2MB.';
        } else {
            // Delete old profile picture if exists
            if (!empty($user['profile_pic']) && file_exists('uploads/' . $user['profile_pic'])) {
                unlink('uploads/' . $user['profile_pic']);
            }
            
            $filename = $username . '_' . time() . '.' . $extension;
            if (!is_dir('uploads')) {
                mkdir('uploads', 0755, true);
            }
            
            if (move_uploaded_file($_FILES['profile_pic']['tmp_name'], 'uploads/' . $filename)) {
                $users[$username]['profile_pic'] = $filename;
                file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
                $user['profile_pic'] = $filename;
                $success = 'Profile picture updated successfully!';
            } else {
                $error = 'Failed to upload profile picture!';
            }
        }
    } elseif ($_FILES['profile_pic']['error'] !== UPLOAD_ERR_NO_FILE) {
        $error = 'Error uploading file!';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile - User Profile System</title>
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
            padding: 40px 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            margin-bottom: 30px;
            text-align: center;
        }
        .profile-pic-container {
            text-align: center;
            margin-bottom: 30px;
        }
        .profile-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #667eea;
            margin-bottom: 15px;
        }
        .no-pic {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            background: #f0f0f0;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px;
            border: 4px solid #ddd;
            color: #999;
            font-size: 14px;
        }
        .info-group {
            background: #f9f9f9;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 15px;
            border-left: 4px solid #667eea;
        }
        .info-label {
            font-weight: 600;
            color: #555;
            margin-bottom: 5px;
        }
        .info-value {
            color: #333;
            font-size: 16px;
        }
        .upload-form {
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #eee;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #555;
            font-weight: 500;
        }
        input[type="file"] {
            width: 100%;
            padding: 10px;
            border: 2px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s;
            text-decoration: none;
            display: inline-block;
        }
        .btn:hover {
            transform: translateY(-2px);
        }
        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .btn-secondary {
            background: #6c757d;
            color: white;
        }
        .btn-danger {
            background: #dc3545;
            color: white;
        }
        .button-group {
            display: flex;
            gap: 10px;
            margin-top: 30px;
            flex-wrap: wrap;
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
        .file-info {
            font-size: 12px;
            color: #888;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>My Profile</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <div class="profile-pic-container">
            <?php if (!empty($user['profile_pic'])): ?>
                <img src="uploads/<?= htmlspecialchars($user['profile_pic']) ?>" 
                     alt="Profile Picture" class="profile-pic">
            <?php else: ?>
                <div class="no-pic">No Profile Picture</div>
            <?php endif; ?>
        </div>
        
        <div class="info-group">
            <div class="info-label">Username</div>
            <div class="info-value"><?= htmlspecialchars($username) ?></div>
        </div>
        
        <div class="info-group">
            <div class="info-label">Email</div>
            <div class="info-value"><?= htmlspecialchars($user['email']) ?></div>
        </div>
        
        <div class="info-group">
            <div class="info-label">Member Since</div>
            <div class="info-value"><?= htmlspecialchars($user['created_at']) ?></div>
        </div>
        
        <div class="upload-form">
            <h2 style="margin-bottom: 15px; color: #333; font-size: 18px;">Update Profile Picture</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="profile_pic">Choose New Picture</label>
                    <input type="file" id="profile_pic" name="profile_pic" accept="image/*" required>
                    <div class="file-info">JPG, JPEG, PNG, GIF only. Max 2MB</div>
                </div>
                <button type="submit" class="btn btn-primary">Upload Picture</button>
            </form>
        </div>
        
        <div class="button-group">
            <a href="change-password.php" class="btn btn-secondary">Change Password</a>
            <a href="logout.php" class="btn btn-danger">Logout</a>
        </div>
    </div>
</body>
</html>