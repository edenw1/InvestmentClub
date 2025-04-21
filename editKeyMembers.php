<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
    $name = $_POST['name'];
    $position = $_POST['position'];
    $description = $_POST['description'];

    $params = [
        ':name' => $name,
        ':position' => $position,
        ':description' => $description
    ];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['photo']['name']);
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $params[':photo_path'] = $target_file;
            $sql = "INSERT INTO member (name, position, description, photo_path) VALUES (:name, :position, :description, :photo_path)";
        } else {
            echo "Error uploading file.";
            exit;
        }
    } else {
        $sql = "INSERT INTO member (name, position, description) VALUES (:name, :position, :description)";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    header('Location: key-members');
    exit;
}
