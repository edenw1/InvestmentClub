<?php
require 'databaseFunctions.php';

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
    <form action="/InvestmentClub" method="post">
    <input type="submit" value="Back to Home"></form>
    <?php
} else {
    addMember($username, $password, $email, $admin);
    header("/InvestmentClub/Admin");
    exit();
}
?>
