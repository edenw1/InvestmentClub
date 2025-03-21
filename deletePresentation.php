<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
if (isset($_POST['presentation_id'])) {
    $presentation_id = $_POST['presentation_id'];
    if (deletePresentation($presentation_id)) {
        header('Location: presentations');
        exit();
    } else {
        echo "Error deleting presentation.";
        ?>
        <a href="presentations">Back to Presentations</a>
        <?php
    }
} else {
    echo "Error deleting presentation.";
    ?>
    <a href="presentations">Back to Presentations</a>
    <?php
}
}
?>
