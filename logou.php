+<?php
session_start();

// Clear remember me cookie if exists
if (isset($_COOKIE['remember_token'])) {
    setcookie('remember_token', '', time() - 3600, '/', '', false, true);
    
    // Remove token from users.json
    if (isset($_SESSION['user'])) {
        $users = [];
        if (file_exists('users.json')) {
            $users = json_decode(file_get_contents('users.json'), true) ?? [];
        }
        
        if (isset($users[$_SESSION['user']])) {
            $users[$_SESSION['user']]['remember_token'] = null;
            file_put_contents('users.json', json_encode($users, JSON_PRETTY_PRINT));
        }
    }
}

// Clear session
$_SESSION = [];

// Destroy session
session_destroy();

// Set success message for next page load
session_start();
$_SESSION['success'] = 'You have been logged out successfully.';

// Redirect to login
header('Location: login.php');
exit;