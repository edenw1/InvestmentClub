<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    $content_id = $_POST['content_id'];

    if (deleteContent($content_id)) {
        header("Location:about "); 
        exit();
    } else {
        echo "Error deleting content.";
        echo "No rows deleted. Content ID: " . htmlspecialchars($content_id);
        ?>
        <a href="/about">Back to About</a>
        <?php
    }
} else {
    echo "Error deleting content.";
    ?>
    <a href="/about">Back to About</a>
    <?php
}
?>
