<?php
session_start();
require 'databaseFunctions.php';


if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
$username = $_POST['username'];
$email = $_POST['email'];
$password = $_POST['password'];
//if the checkbox is checked, set admin to 1, else 0
if (isset($_POST['admin'])) {
    $admin = 1;
} else {
    $admin = 0;
}

$row = checkMember($username, $email);

if ($row) {
    echo '<br>' . 'This username or email already exists.';
    ?>
    <a href="admin">Back to Admin Panel</a>
    <?php
} else {
    addMember($username, $password, $email, $admin);
}
    header('Location: admin');
    exit();
}
?>
