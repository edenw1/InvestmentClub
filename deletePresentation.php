<?php
session_start();
require 'databaseFunctions.php';

if (isset($_POST['presentation_id'])) {
    $presentation_id = $_POST['presentation_id'];
    if (deletePresentation($presentation_id)) {
        echo "Presentation deleted";
        ?>
        <form action="/InvestmentClub" method="post">
            <input type="submit" value="Back to Home">
        </form>
        <?php
    } else {
        echo "Error deleting presentation.";
    }
} else {
    echo "Error deleting presentation.";
}
?>
