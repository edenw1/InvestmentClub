<?php
session_start();
require 'databaseFunctions.php';

if (isset($_POST['member_id'])) {
    $member_id = $_POST['member_id'];

    if (deleteMember($member_id)) {
        header('Location: key-members');
        exit();
    } else {
        echo "Error deleting member.";
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
