<?php
require 'db.php'; 
dbConnect(); 
function checkMember($username,$email) {
    global $pdo;
        $checkUser = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt1 = $pdo->prepare($checkUser);
        $stmt1->bindParam(':username', $username);
        $stmt1->bindParam(':email', $email);
        $stmt1->execute();
        return $stmt1->fetch();
}
function addMember($username, $password, $email, $admin) {
    global $pdo;
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
function getUsersByIds($userIds) {
    global $pdo; // Use the global $pdo object

    // Prepare the SQL query to select users by multiple IDs
    $sql = "SELECT * FROM users WHERE id IN (" . implode(',', array_fill(0, count($userIds), '?')) . ")";
    //vulnerable to SQL injection
    //pass in the parameters to execute
    $stmt = $pdo->prepare($sql);
    
    // Bind the user IDs to the placeholders
    foreach ($userIds as $index => $userId) {
        $stmt->bindValue($index + 1, $userId, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    $users = [];
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $users[] = [
            'id' => $row['id'],
            'username' => $row['username'],
            'email' => $row['email'],
            'password' => $row['password'], // or store hashed password as needed
            'admin' => $row['admin'] 
        ];
    }

    return $users;
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
function addPresentation($user_id, $title, $url) {
    global $pdo;

    $addPresentation = "INSERT INTO presentation (user_id, title, file_path, date) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($addPresentation);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $title, PDO::PARAM_STR);
    $stmt->bindValue(3, $url, PDO::PARAM_STR);
    $stmt->execute();
    
    return $pdo->lastInsertId(); 
}

function addStockProposal($presentation_id, $user_id, $symbol, $name, $action, $quantity) {
    global $pdo;

    $addStockProposal = "INSERT INTO stockProposal (presentation_id, stock_symbol, stock_name, proposed_by, action, quantity) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($addStockProposal);
    $stmt->bindValue(1, $presentation_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $symbol, PDO::PARAM_STR);
    $stmt->bindValue(3, $name, PDO::PARAM_STR);
    $stmt->bindValue(4, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(5, $action, PDO::PARAM_STR);
    $stmt->bindValue(6, $quantity, PDO::PARAM_INT);
    $stmt->execute();
    
    return "Stock proposal added successfully!";
}
?>