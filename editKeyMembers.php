<?php
session_start();
require 'databaseFunctions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $member_id = $_POST['member_id'];
    $name = $_POST['name'];
    $position = $_POST['position'];
    $description = $_POST['description'];

    $params = [
        ':member_id' => $member_id,
        ':name' => $name,
        ':position' => $position,
        ':description' => $description
    ];

    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === 0) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES['photo']['name']);
        
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $target_file)) {
            $params[':photo_path'] = $target_file;
            $sql = "UPDATE member SET name = :name, position = :position, description = :description, photo_path = :photo_path WHERE member_id = :member_id";
        } else {
            echo "Error uploading file.";
            exit;
        }
    } else {
        $sql = "UPDATE member SET name = :name, position = :position, description = :description WHERE member_id = :member_id";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    header('Location: about');
    exit;
}