<?php
require 'db.php';
dbConnect();

$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
//if the checkbox is checked, set admin to 1, else 0
$admin = isset($_POST['admin']) ? 1 : 0;  

$checkUser = "SELECT * FROM users WHERE username = :username OR email = :email";
$stmt1 = $pdo->prepare($checkUser);
$stmt1->bindParam(':username', $username);
$stmt1->bindParam(':email', $email);
$stmt1->execute();
$row = $stmt1->fetch();

if ($row) {
    echo '<br>' . 'This username or email already exists.';
} else {
    $sql = 'INSERT INTO users (username, email, password, admin) VALUES (:username, :email, :password, :admin)';
    $stmt = $pdo->prepare($sql);
    $encryptedPass = password_hash($password, PASSWORD_BCRYPT);
    $stmt->bindParam(':username', $username);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':password', $encryptedPass);
    $stmt->bindParam(':admin', $admin, PDO::PARAM_INT);
    $stmt->execute();
    echo '<br>' . "User Registered";
    header("Location: addmember.php");
}
?>
