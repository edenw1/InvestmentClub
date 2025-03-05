<?php
session_start();
require 'databaseFunctions.php'; 

global $pdo; 

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $url = $_POST['url'];
    $type = $_POST['type'];
    
    $result = addContent($title, $description, $url, $type);
    if ($result == true){
    header('Location: about');
    } else {
        echo "Error adding content";
        ?>
        <br>
        <a href="about">Back to About</a>
        <?php
    }
} else {
    echo "Invalid request.";
}
?>
