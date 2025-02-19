<?php
session_start();
require 'databaseFunctions.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin'])) {
    header("Location: admin");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['proposal_id']) && isset($_POST['status'])) {
    $proposal_id = $_POST['proposal_id'];
    $status = $_POST['status'];

    if (reviewStocks($proposal_id, $status)) {
        header("Location: admin");
        exit();
    } else {
        echo "Error reviewing stock proposal.";
    }
}
?>
<form action="/InvestmentClub" method="post">
    <input type="submit" value="Back to Home">
</form>
