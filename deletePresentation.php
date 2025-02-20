<?php
session_start();
require 'databaseFunctions.php';

if (isset($_POST['presentation_id'])) {
    $presentation_id = $_POST['presentation_id'];
   $deletePresentation = "DELETE FROM presentations WHERE presentation_id = ?";
    $stmt = $pdo->prepare($deletePresentation);
    echo "Presentation deleted";
    ?>
    <form action="/InvestmentClub" method="post">
        <input type="submit" value="Back to Home">
    </form>
    <?php
    } else {
        echo "Error deleting presentation.";
    }
?>
