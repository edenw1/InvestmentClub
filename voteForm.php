<?php
require 'db.php'; 
dbConnect(); 

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['presentation_id'], $_POST['vote'])) {
    $user_id = $_SESSION['user_id'] ?? null;
    $presentation_id = (int)$_POST['presentation_id'];
    $vote = $_POST['vote'] == '1' ? 1 : 0;
    
    if (!$user_id) {
        echo 'You must be logged in to vote.';
    } else {
        $result = voteOnPresentation($user_id, $presentation_id, $vote);
        echo $result;
    }
}