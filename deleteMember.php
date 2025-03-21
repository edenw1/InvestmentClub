<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    $member_id = $_POST['member_id'];

    if (deleteMember($member_id)) {
        header('Location: key-members');
        exit();
    } else {
        echo "Error deleting member.";
        echo("No rows deleted. Member ID: " . $member_id);
        ?>
        <a href="key-members">Back to Members</a>
        <?php
    }
} else {
    echo "Error deleting member.";
    ?>
    <a href="key-members">Back to Members</a>
    <?php
}
?>
