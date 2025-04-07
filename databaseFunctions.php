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
function fetchPendingProposals() {
    global $pdo;
    $proposals = [];
    try {
        $stmt = $pdo->query("
            SELECT sp.proposal_id, sp.stock_name, sp.stock_symbol, sp.status, u.username
            FROM stockProposal sp
            JOIN users u ON sp.proposed_by = u.user_id
            WHERE sp.status = 'pending'
        ");
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $proposals[] = [
                'proposal_id' => $row['proposal_id'],
                'stock_name' => $row['stock_name'],
                'symbol' => $row['stock_symbol'],
                'proposed_by' => $row['username'],
                'status' => $row['status'],
            ];
        }
    } catch (Exception $e) {
        error_log("Error fetching pending proposals: " . $e->getMessage());
    }
    return $proposals;
}


function fetchAllStocks() {
    global $pdo;
    try {
        return $pdo->query("SELECT stock_id, symbol FROM stocks")->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        error_log("Error fetching stocks: " . $e->getMessage());
        return [];
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


        $plusQuery = "SELECT COALESCE(SUM(quantity), 0) AS total FROM transactions WHERE transaction_type = 'Buy' AND stock_id = :stock_id";
        $stmt = $pdo->prepare($plusQuery);
        $stmt->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
        $stmt->execute();
        $plusResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $plusTotal = $plusResult['total'];

        $lessQuery = "SELECT COALESCE(SUM(quantity), 0) AS total FROM transactions WHERE transaction_type = 'Sell' AND stock_id = :stock_id";
        $stmt = $pdo->prepare($lessQuery);
        $stmt->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
        $stmt->execute();
        $lessResult = $stmt->fetch(PDO::FETCH_ASSOC);
        $lessTotal = $lessResult['total'];

        $total = $plusTotal - $lessTotal;

        if ($total <= 0) { 
            if ($stock['active']) { 
                $updateStock = "UPDATE stocks SET active = 0 WHERE stock_id = :stock_id";
                $stmt2 = $pdo->prepare($updateStock);
                $stmt2->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
                $stmt2->execute();
            }
        } else {
            if (!$stock['active']) {
                $updateStock = "UPDATE stocks SET active = 1 WHERE stock_id = :stock_id";
                $stmt2 = $pdo->prepare($updateStock);
                $stmt2->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
                $stmt2->execute();
            }
        }

        if (!$stock) {
            return false;
        }



        return true;
    } catch (Exception $e) {
        return false;
    }
}
function deletePresentation($presentation_id) {
    global $pdo;
    try {
        $deletePresentation = "DELETE FROM presentation WHERE presentation_id = ?";
        $stmt = $pdo->prepare($deletePresentation);
        $stmt->execute([$presentation_id]);
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

function getPresentations() {
    global $pdo;
    $presentations = [];
    try {
        $stmt = $pdo->prepare("
            SELECT p.presentation_id, p.title, p.date, p.file_path, u.username 
            FROM presentation p 
            JOIN users u ON p.user_id = u.user_id 
            ORDER BY p.date DESC
        ");
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $presentations[] = $row;
        }
    } catch (Exception $e) {
        error_log("Error fetching presentations: " . $e->getMessage());
    }
    return $presentations;
}

function getVotesByPresentationId($presentation_id) {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT * FROM vote WHERE presentation_id = :presentation_id");
        $stmt->bindParam(':presentation_id', $presentation_id, PDO::PARAM_INT);
        $stmt->execute();
        
        $votes = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $votes[] = $row;
        }
        
        return $votes;
    } catch (Exception $e) {
        return false;
    }
}


function getStockProposals() {
    global $pdo;
    try {
        $stmt = $pdo->prepare("SELECT sp.proposal_id, sp.presentation_id, sp.stock_symbol, sp.stock_name, sp.action, sp.quantity
            FROM stockProposal sp");
        $stmt->execute();
        
        $proposals = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $proposals[] = $row;
        }
        
        return $proposals;
    } catch (Exception $e) {
        return false;
    }
}

function voteOnPresentation($user_id, $presentation_id, $vote) {
    global $pdo;

    try {
        $checkVoteinDB = "SELECT * FROM vote WHERE user_id = ? AND presentation_id = ?";
        $stmt = $pdo->prepare($checkVoteinDB);
        $stmt->execute([$user_id, $presentation_id]);
        $existing_vote = $stmt->fetch();

        if ($existing_vote) {
            return false;
        } else {

            $insertVote = "INSERT INTO vote (user_id, presentation_id, yes_or_no) VALUES (?, ?, ?)";
            $stmt = $pdo->prepare($insertVote);
            $stmt->execute([$user_id, $presentation_id, $vote]);
            return true;
        }
    } catch (Exception $e) {
        return 'Error: ' . $e->getMessage();
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

function addToWatchlist($symbol, $name) {
    global $pdo;
    try {
        $addWatchlist = "INSERT INTO stocks (symbol, name, watchlist) VALUES (:symbol, :name, TRUE) ON DUPLICATE KEY UPDATE watchlist = TRUE";
        $stmt = $pdo->prepare($addWatchlist);
        $stmt->bindParam(':symbol', $symbol, PDO::PARAM_STR);
        $stmt->bindParam(':name', $name, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}
function removeFromWatchlist($symbol) {
    global $pdo;
    try {
        $removeWatchlist = "UPDATE stocks SET watchlist = FALSE WHERE symbol = :symbol";
        $stmt = $pdo->prepare($removeWatchlist);
        $stmt->bindParam(':symbol', $symbol, PDO::PARAM_STR);

        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}
// function addContent($title, $description, $url, $type){
//     global $pdo;
//     try {
//         $sql = "INSERT content SET title = :title, description = :description, url = :url, type = :type, updated_at = NOW()"; 
//         $stmt = $pdo->prepare($sql);
//         $stmt->execute([':title' => $title,':description' => $description,':url' => $url,':type' => $type]);
//         return true;
//     } catch (Exception $e){
//         return false;
//     }
// }
function selectActiveStocks() {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT symbol FROM stocks WHERE active = 1");

        $stocks = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stocks[] = $row;
        }

        return $stocks;
    } catch (Exception $e) {
        throw new Exception("Error selecting active stocks: " . $e->getMessage());
    }
}


function addContent($title, $description, $url, $type) {
    global $pdo;
    try {
        $sql = "INSERT INTO content (title, description, url, type) VALUES (:title, :description, :url, :type)";
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindParam(':title', $title, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':url', $url, PDO::PARAM_STR);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        error_log("Error inserting content: " . $e->getMessage());
        return false;
    }
}
//this is for carter to use later
function showContent() {
    global $pdo;
    try {
        $sql = "SELECT * FROM content ORDER BY created_at DESC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        
        $contents = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $contents[] = $row;
        }
        
        return $contents;
    } catch (Exception $e) {
        return false;
    }
}
function deleteContent($content_id) {
    global $pdo;
    try {
        $sql = "DELETE FROM content WHERE content_id = :content_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':content_id', $content_id, PDO::PARAM_INT);
        return $stmt->execute();
    } catch (Exception $e) {
        return false;
    }
}


function deleteMember($member_id) {
    global $pdo;
        $deleteMem = "DELETE FROM member WHERE member_id = ?";
        $stmt = $pdo->prepare($deleteMem);
        $stmt->bindParam(1, $member_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
}

function getAllTransactions($sortBy = 'date_desc') {
    try {
        global $pdo;
        //defaulting to ordering by date
        $order = "ORDER BY t.buy_sell_date DESC";

        if ($sortBy == 'date_asc') {
            $order = "ORDER BY t.buy_sell_date ASC";
        } elseif ($sortBy == 'stock') {
            $order = "ORDER BY s.symbol ASC";
        }

        $stmt = $pdo->prepare("SELECT s.symbol AS stock_symbol, 
                s.name AS stock_name, 
                t.transaction_type, 
                t.quantity, 
                t.price_per_share, 
                t.buy_sell_date 
            FROM transactions t 
            JOIN stocks s ON t.stock_id = s.stock_id $order");
        $stmt->execute();

        $transactions = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $transactions[] = $row;
        }

        return $transactions;
    } catch (Exception $e) {
        error_log("Error fetching transactions: " . $e->getMessage());
        return [];
    }
}


function fetchWatchlistStocks() {
    global $pdo;
    $stocks = [];
    try {
        $stmt = $pdo->query("SELECT symbol FROM stocks WHERE watchlist = 1");        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stocks[] = [
                'symbol' => $row['symbol'],
                'profile' => getProfile($row['symbol']),
                'quote' => getQuote($row['symbol']),
                'trends' => getTrends($row['symbol'])
            ];
        }
    } catch (Exception $e) {
        error_log("Error fetching watchlist stocks: " . $e->getMessage());
    }
    return $stocks;
}

function fetchKeyMembers() {
    global $pdo;
    $members = [];
    try {
        $sql = "SELECT member_id, name, position, description, photo_path FROM member";
        $stmt = $pdo->prepare($sql);
        $stmt->execute();
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $members[] = $row;
        }
    } catch (Exception $e) {
        error_log("Error fetching key members: " . $e->getMessage());
    }
    return $members;
}

