<?php
require 'db.php';
dbConnect();

$transaction_type = $_POST['transaction_type'];
$stock_id = $_POST['stock_id'];
$quantity = $_POST['quantity'];
$price_per_share = $_POST['price_per_share'];
$buy_sell_date = $_POST['buy_sell_date'];

$checkStock = "SELECT * FROM stocks WHERE stock_id = :stock_id";
$stmt1 = $pdo->prepare($checkStock);
$stmt1->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
$stmt1->execute();
$stock = $stmt1->fetch();

if (!$stock) {
    echo '<br>' . 'Stock ID not found.';
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

    // mark stock as active if it isn't already
    if (!$stock['active']) {
        $updateStock = "UPDATE stocks SET active = 1 WHERE stock_id = :stock_id";
        $stmt2 = $pdo->prepare($updateStock);
        $stmt2->bindParam(':stock_id', $stock_id, PDO::PARAM_INT);
        $stmt2->execute();
    }

    header("Location: controller.php?action=admin");
    exit();
}
?>
