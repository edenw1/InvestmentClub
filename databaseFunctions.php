<?php
require 'db.php'; 
dbConnect(); 
function addMember($username, $password, $email, $admin = 0) {
    global $pdo;
    $checkUser = "SELECT * FROM users WHERE username = :username OR email = :email";
    $stmt1 = $pdo->prepare($checkUser);
    $stmt1->bindParam(':username', $username);
    $stmt1->bindParam(':email', $email);
    $stmt1->execute();
    $row = $stmt1->fetch();

    if ($row) {
        return 'This username or email already exists.';
    } else {
        // Insert the new user into the database
        $sql = 'INSERT INTO users (username, email, password, admin) VALUES (:username, :email, :password, :admin)';
        $stmt = $pdo->prepare($sql);
        $encryptedPass = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $encryptedPass);
        $stmt->bindParam(':admin', $admin, PDO::PARAM_INT);
        $stmt->execute();

        return 'User Registered';
    }
}

function getUserById($userId) {
    global $pdo; // Use the global $pdo object

    $sql = "SELECT * FROM users WHERE id = :id";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $userId, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function removeMemberByEmail($email) {
    global $pdo;

    // Check if the user with the given email exists
    $checkUser = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($checkUser);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
        // User exists, delete them from the database
        $deleteUser = "DELETE FROM users WHERE email = :email";
        $stmtDelete = $pdo->prepare($deleteUser);
        $stmtDelete->bindParam(':email', $email);
        $stmtDelete->execute();

        echo "User successfully removed.";
        header("Location: adminPage.php");
        exit; 
    } else {
        echo "No user found with this email.";
    }
}
?>