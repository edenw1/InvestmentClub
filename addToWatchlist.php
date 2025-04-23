<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $symbol = $_POST['symbol'];
    $name = $_POST['name'];

    if (!empty($symbol) && !empty($name)) {
        if (addToWatchlist($symbol, $name)) {
            header("Location: /");
            exit();
        } else {
            echo "Error adding stock to watchlist";
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
