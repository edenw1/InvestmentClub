<?php
require 'databaseFunctions.php';
require_once 'vendor/autoload.php';

$loader = new \Twig\Loader\FilesystemLoader('templates');
$twig = new \Twig\Environment($loader);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $admin = isset($_POST['admin']) ? 1 : 0;

    if (empty($username) || empty($email) || empty($password)) {
        echo $twig->render('form.twig', [
            'error' => 'All fields are required!',
            'username' => $username,
            'email' => $email,
            'url' => 'process_form.php'  // Form action URL
        ]);
    } else {
        // Call the addMember function
        $result = addMember($username, $password, $email, $admin);

        if ($result === 'User Registered') {
            header("Location: addmember.php");
            exit();
        } else {
            // If registration fails (e.g., username/email exists)
            echo $twig->render('form.twig', [
                'error' => $result,
                'username' => $username,
                'email' => $email,
                'url' => 'process_form.php'  // Form action URL
            ]);
        }
    }
} else {
    echo $twig->render('form.twig', [
        'url' => 'process_form.php'  // Form action URL
    ]);
}
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['remove_email'])) {
    $email = $_POST['remove_email'];
    removeMemberByEmail($email);
}
?>