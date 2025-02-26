<?php
require 'databaseFunctions.php';

$transaction_type = $_POST['transaction_type'];
$stock_id = $_POST['stock_id'];
$quantity = $_POST['quantity'];
$price_per_share = $_POST['price_per_share'];
$buy_sell_date = $_POST['buy_sell_date'];

$result = addTransaction($transaction_type, $stock_id, $quantity, $price_per_share, $buy_sell_date);

if ($result == true) {

    header('Location: admin');
    exit();
} else {
    echo '<br>' . $result; 
    ?>
    <br>
    <a href="admin">Back to Admin Panel</a>
    <?php
}
?>
