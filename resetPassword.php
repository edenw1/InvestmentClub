<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $password2 = $_POST['check_password'];

    if ($password === $password2) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        $updatePass = "UPDATE users SET password = :password WHERE email = :email";
        $stmt = $pdo->prepare($updatePass);
        $stmt->bindParam(':password', $hashedPassword);
        $stmt->bindParam(':email', $email);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            echo "Password updated successfully.";
        } else {
            echo "No user found with this email, or password not changed.";
        }
    } else {
        echo "Passwords do not match.";
    }
}
?>
