<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    $symbol = $_POST['symbol'];

    if (!empty($symbol)) {
        if (removeFromWatchlist($symbol)) { 
            header("Location: /");
            exit();
        } else {
            echo "Error removing stock from watchlist";
            ?>
            <a href="/">Home</a>
            <?php
        }
    } else {
        echo "Error: Symbol is required.";
        ?>
        <a href="/">Home</a>
        <?php
    }
}
?>