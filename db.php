<?php
function dbConnect()
//now in every document where you are using the database use this to establish database connetion 
//require 'db.php';
//dbConnect();

{
    global $pdo;
    try {
        $pdo = new PDO('mysql:host=localhost;dbname=investment_club', 'root', 'password');
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->exec('SET NAMES "utf8"');
    } catch (PDOException $e) {
        echo "Database connection failed: " . $e->getMessage();
        exit();
    }
}
?>
