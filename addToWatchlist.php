<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $symbol = $_POST['symbol'];
    $name = $_POST['name'];

    if (!empty($symbol) && !empty($name)) {
        if (addToWatchlist($symbol, $name)) {
            //not sure how to redirect back to that specific stock page after watchlisting, but that would be ideal
            //possibly use $symbol to add to the redirect "url"
            header("Location: /InvestmentClub");
            exit();
        } else {
            echo "Error adding stock to watchlist";
            ?>
            <a href="/InvestmentClub">Home</a>
            <?php
        }
    } else {
        echo "Error: Symbol and name are required.";
        ?>
        <a href="/InvestmentClub">Home</a>
        <?php
    }
}
?>
