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

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    
    $proposal_id = $_POST['proposal_id'];
    $status = $_POST['status'];

    if (reviewStocks($proposal_id, $status)) {
        header("Location: admin");
        exit();
    } else {
        echo "Error reviewing stock proposal.";
        ?>
            <a href="admin">Admin Panel</a>
        <?php
    }
}
?>
    <a href="admin">Admin Panel</a>
