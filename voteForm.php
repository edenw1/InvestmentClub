<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['presentation_id'], $_POST['vote'])) {
    $user_id = $_SESSION['user_id'] ?? null;
    $presentation_id = intval($_POST['presentation_id']);
    if ($_POST['vote'] == '1') {
        $vote = 1;
    } else {
        $vote = 0;
    }
    
    if (!$user_id) {
        echo('You must be logged in to vote.');
    } else {
        $result = voteOnPresentation($user_id, $presentation_id, $vote);
        echo $result;
    }
}
?>
<br>
<a href="presentations">Presentations</a>
