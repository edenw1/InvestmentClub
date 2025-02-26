<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $url = $_POST['url'];
    $user_id = $_SESSION['user_id'];
    addPresentation($user_id, $title, $url);
    $presentation_id = $pdo->lastInsertId();

    if (isset($_POST['stocks'])) {
        foreach ($_POST['stocks'] as $stock) {
            $symbol = $stock['symbol'];
            $name = $stock['name'];
            $action = $stock['action'];
            if (!empty($stock['quantity'])) {
                $quantity = intval($stock['quantity']);
            } else {
                $quantity = NULL;
            }
            if (!empty($symbol) && !empty($name) && !empty($action)) {
                addStockProposal($presentation_id, $user_id, $symbol, $name, $action, $quantity );
            }
        }
    }

    header('Location: presentations');
    exit();
} else {
    echo "Could not add presentation";
    ?>
    <br>
    <a href="presentations">Back to Presentations</a>
    <?php
}
?>
