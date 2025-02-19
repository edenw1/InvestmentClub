<?php
require 'databaseFunctions.php';

$transaction_type = $_POST['transaction_type'];
$stock_id = $_POST['stock_id'];
$quantity = $_POST['quantity'];
$price_per_share = $_POST['price_per_share'];
$buy_sell_date = $_POST['buy_sell_date'];

$result = addTransaction($transaction_type, $stock_id, $quantity, $price_per_share, $buy_sell_date);

if ($result == 'Transaction added successfully.') {

    ?>
    Transaction Made
    <form action="/InvestmentClub" method="post">
    <input type="submit" value="Back to Home">
    </form>
    <?php
} else {
    echo '<br>' . $result; 
}
?>
