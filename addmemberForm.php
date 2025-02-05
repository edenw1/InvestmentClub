<?php
require 'db.php';
require 'databaseFunctions.php';
dbConnect();

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
} else {
    addMember($username, $password, $email, $admin = 0);
}
?>
