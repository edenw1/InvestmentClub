<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $presentation_id = (int)$_POST['presentation_id'];
    $vote = $_POST['vote'] == '1' ? 1 : 0;
    $result = voteOnPresentation($user_id, $presentation_id, $vote);
    if ($result == true){
    header("Location: presentations");
    } else {
        echo("You have already voted on this presentation");
        ?>
        <br>
        <a href="presentations">Back to Presentations </a>
        <?php
    }
    }