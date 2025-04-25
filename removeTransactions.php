<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {

    $transaction_id = $_POST['transaction_id']; 
    $result = removeTransaction($transaction_id);
}

if ($result == true) {
    header('Location: transactions');
    exit();
} else {
    echo '<br>' . $result;
    ?>
    <br>
    <a href="transactions">Back to Transactions</a>
    <?php
}
?>
