<?php
require 'db.php';
dbConnect();

$email = $_POST['email'];

$checkUser = "SELECT * FROM users WHERE email = :email";
$stmt = $pdo->prepare($checkUser);
$stmt->bindParam(':email', $email);
$stmt->execute();
$user = $stmt->fetch();

if ($user) {
    $deleteUser = "DELETE FROM users WHERE email = :email";
    $stmtDelete = $pdo->prepare($deleteUser);
    $stmtDelete->bindParam(':email', $email);
    $stmtDelete->execute();
    header("Location: admin");
} else {
    echo "No user found with this email.";
    ?>
    <br>
    <a href="admin">Admin Panel</a>
<?php
}
?>
