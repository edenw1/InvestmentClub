<?php
// Include the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';

// Set up the Twig loader to look in the 'templates' folder
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates'); // Adjust the path if needed
$twig = new \Twig\Environment($loader, [
    'cache' => __DIR__ . '/compilation_cache', // Optional cache directory
]);

// Example data to pass to the template
$user = [
    'name' => 'John Doe',
    'email' => 'john@example.com',
];

// Render the 'indexx.html.twig' template
echo $twig->render('index.html.twig', [
    'user' => $user, // Pass the user data to the template
]);
?>
