<?php
require_once 'db.php';
dbConnect();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: /InvestmentClub/admin");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proposal_id']) && isset($_POST['status'])) {
    $proposal_id = $_POST['proposal_id'];
    $status = $_POST['status'];
    $updateProposal = "UPDATE stockProposal SET status = :status WHERE proposal_id = :proposal_id";
    $stmt = $pdo->prepare($updateProposal);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':proposal_id', $proposal_id, PDO::PARAM_INT);

    $stmt->execute();
    if ($status == 'approved') {
        $proposal = "SELECT stock_symbol, stock_name FROM stockProposal WHERE proposal_id = :proposal_id";
        $stmt = $pdo->prepare($proposal);
        $stmt->bindParam(':proposal_id', $proposal_id, PDO::PARAM_INT);

        $stmt->execute();
        $stock = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($stock) {
            // see if the stock already exists in the stocks table
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
    header("Location: /InvestmentClub/admin");
    exit();
}

// all pending stock proposals
$pendingProposals = "SELECT * FROM stockProposal WHERE status = 'pending'";
$stmt = $pdo->prepare($pendingProposals);

$stmt->execute();
$proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<form action="/InvestmentClub" method="post">
    <input type="submit" value="Back to Home">
</form>
