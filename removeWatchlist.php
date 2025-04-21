<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    $symbol = $_POST['symbol'];
    $name = $_POST['name'];

    if (!empty($symbol) && !empty($name)) {
        if (removeWatchlist($symbol, $name)) {
            header("Location: /");
            exit();
        } else {
            echo "Error aremoving stock from watchlist";
            ?>
            <a href="/">Home</a>
            <?php
        }
    } else {
        echo "Error: Symbol and name are required.";
        ?>
        <a href="/">Home</a>
        <?php
    }
}
?>
