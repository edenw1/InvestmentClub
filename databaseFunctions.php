<?php
require 'db.php'; 
dbConnect(); 

function checkLogin($username, $password){
    global $pdo;
    try {
        $checkUsername = "SELECT * FROM users WHERE username = :username";
        $stmt = $pdo->prepare($checkUsername);
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(); 

        if (!$user) {
            return false;
        }
        
        if (password_verify($password, $user['password'])) {
            return $user;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function checkMember($username, $email) {
    global $pdo;
    try {
        $checkUser = "SELECT * FROM users WHERE username = :username OR email = :email";
        $stmt = $pdo->prepare($checkUser);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        if ($stmt->fetch()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function addMember($username, $password, $email, $admin) {
    global $pdo;
    try {
        $sql = 'INSERT INTO users (username, email, password, admin) VALUES (:username, :email, :password, :admin)';
        $stmt = $pdo->prepare($sql);
        $encryptedPass = password_hash($password, PASSWORD_BCRYPT);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', $encryptedPass);
        $stmt->bindParam(':admin', $admin, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}


function addTransaction($transaction_type, $stock_id, $quantity, $price_per_share, $buy_sell_date) {
    global $pdo;
    try {
        $checkStock = "SELECT * FROM stocks WHERE stock_id = :stock_id";
        $stmt1 = $pdo->prepare($checkStock);
        $stmt1->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
        $stmt1->execute();
        $stock = $stmt1->fetch();

        if (!$stock) {
            return false;
        }

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

        return true;
    } catch (Exception $e) {
        return false;
    }
}

function removeMemberByEmail($email) {
    global $pdo;
    try {
        $checkUser = "SELECT * FROM users WHERE email = :email";
        $stmt = $pdo->prepare($checkUser);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch();

        if ($user) {
            $deleteUser = "DELETE FROM users WHERE email = :email";
            $stmtDelete = $pdo->prepare($deleteUser);
            $stmtDelete->bindParam(':email', $email);
            
            if ($stmtDelete->execute()) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function addPresentation($user_id, $title, $url) {
    global $pdo;
    try {
        $addPresentation = "INSERT INTO presentation (user_id, title, file_path, date) VALUES (?, ?, ?, NOW())";
        $stmt = $pdo->prepare($addPresentation);
        $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $title, PDO::PARAM_STR);
        $stmt->bindValue(3, $url, PDO::PARAM_STR);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function addStockProposal($presentation_id, $user_id, $symbol, $name, $action, $quantity) {
    global $pdo;
    try {
        $addStockProposal = "INSERT INTO stockProposal (presentation_id, stock_symbol, stock_name, proposed_by, action, quantity) VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($addStockProposal);
        $stmt->bindValue(1, $presentation_id, PDO::PARAM_INT);
        $stmt->bindValue(2, $symbol, PDO::PARAM_STR);
        $stmt->bindValue(3, $name, PDO::PARAM_STR);
        $stmt->bindValue(4, $user_id, PDO::PARAM_INT);
        $stmt->bindValue(5, $action, PDO::PARAM_STR);
        $stmt->bindValue(6, $quantity, PDO::PARAM_INT);
        
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

function reviewStocks($proposal_id, $status) {
    global $pdo;
    try {
        $updateProposal = "UPDATE stockProposal SET status = :status WHERE proposal_id = :proposal_id";
        $stmt = $pdo->prepare($updateProposal);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':proposal_id', $proposal_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($status === 'approved') {
            $proposalQuery = "SELECT stock_symbol, stock_name FROM stockProposal WHERE proposal_id = :proposal_id";
            $stmt = $pdo->prepare($proposalQuery);
            $stmt->bindParam(':proposal_id', $proposal_id, PDO::PARAM_INT);
            $stmt->execute();
            $stock = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($stock) {
                //check if the stock already exists in the stocks table
                $checkStock = "SELECT COUNT(*) FROM stocks WHERE symbol = :symbol";
                $stmt = $pdo->prepare($checkStock);
                $stmt->bindParam(':symbol', $stock['stock_symbol'], PDO::PARAM_STR);
                $stmt->execute();
                $stockExists = $stmt->fetchColumn();

                if ($stockExists == 0) {
                    $insertStock = "INSERT INTO stocks (symbol, name) VALUES (:symbol, :name)";
                    $stmt = $pdo->prepare($insertStock);
                    $stmt->bindParam(':symbol', $stock['stock_symbol'], PDO::PARAM_STR);
                    $stmt->bindParam(':name', $stock['stock_name'], PDO::PARAM_STR);
                    $stmt->execute();
                }
            }
        }

        return true;
    } catch (Exception $e) {
        return false;
    }
}


