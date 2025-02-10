<?php
require 'db.php'; 
dbConnect(); 
//return true or false, don't redirect to another location
function checkLogin($username, $password){
    global $pdo;
    $checkUsername = "SELECT * FROM users WHERE username = :username";
    $stmt = $pdo->prepare($checkUsername);
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $user = $stmt->fetch(); 

    if (!$user) {
        echo 'User not found';
        return NULL;
    }
    if (password_verify($password, $user['password'])) {
        return $user;
    } else {
        return NULL;
    }
}
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
        //add try catch
        return 'User Registered';
}

function addTransaction($transaction_type, $stock_id, $quantity, $price_per_share, $buy_sell_date) {
    global $pdo;
    $checkStock = "SELECT * FROM stocks WHERE stock_id = :stock_id";
    $stmt1 = $pdo->prepare($checkStock);
    $stmt1->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
    $stmt1->execute();
    $stock = $stmt1->fetch();

    if (!$stock) {
        return 'Stock ID not found.';
    } else {
        $sql = 'INSERT INTO transactions (transaction_type, quantity, price_per_share, buy_sell_date, stock_id) 
                VALUES (:transaction_type, :quantity, :price_per_share, :buy_sell_date, :stock_id)';
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':transaction_type', $transaction_type);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':price_per_share', $price_per_share);
        $stmt->bindParam(':buy_sell_date', $buy_sell_date);
        $stmt->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
        $stmt->execute();

        if (!$stock['active']) {
            $updateStock = "UPDATE stocks SET active = 1 WHERE stock_id = :stock_id";
            $stmt2 = $pdo->prepare($updateStock);
            $stmt2->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
            $stmt2->execute();
        }

        return 'Transaction added successfully.';
    }
}


function removeMemberByEmail($email) {
    global $pdo;

    $checkUser = "SELECT * FROM users WHERE email = :email";
    $stmt = $pdo->prepare($checkUser);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $user = $stmt->fetch();

    if ($user) {
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