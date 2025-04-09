<?php
session_start();
require 'databaseFunctions.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_credentials'])) {
    $new_username = trim($_POST['new_username']);
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Passwords don't match";
        header("Location: admin");
        exit;
    }
    
    if (isset($_SESSION['user_id'])) {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        if (!empty($new_username)) {
            // update both username and password
            $stmt = $pdo->prepare("UPDATE users SET username = ?, password = ? WHERE user_id = ?");
            $stmt->execute([$new_username, $hashed_password, $_SESSION['user_id']]);
        } else {
            // update only password
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE user_id = ?");
            $stmt->execute([$hashed_password, $_SESSION['user_id']]);
        }
        header("Location: admin");
        exit;
    } else {
        header("Location: login");
        exit;
    }
}