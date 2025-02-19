<?php
session_start();
require 'databaseFunctions.php';

$username = $_POST["username"];
$password = $_POST["password"];

$user = checkLogin($username, $password);

if ($user != NULL) {
    session_regenerate_id(true);
    $_SESSION['user_id'] = $user['user_id'];
    $_SESSION['admin'] = $user['admin'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['email'] = $user['email'];

    echo 'Login Successful';
    header("Location: /InvestmentClub");
    exit();
} else {
    echo 'Incorrect username or password';
    header("Location: login");
    exit();
}
?>
