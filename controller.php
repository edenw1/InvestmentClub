<?php
// Start the session
session_start();

// Include the Composer autoloader
require_once __DIR__ . '/vendor/autoload.php';
require_once 'db.php'; // Your database connection file

// Initialize Twig
$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'auto_reload' => true,
]);

// Function to connect to the database
dbConnect();

// Check if the user is authenticated
$isAuthenticated = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

// Example user data
$user = [
    'name' => $isAuthenticated ? $_SESSION['username'] : 'Guest',
    'email' => $isAuthenticated ? $_SESSION['email'] : '',
    'is_authenticated' => $isAuthenticated,
    'is_admin' => $isAdmin,
];

// Determine the action from the query parameter
$action = $_GET['action'] ?? 'home';

// Route handling
switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Check if 'username' and 'password' are set in the POST data
            if (isset($_POST['username']) && isset($_POST['password'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];

                // Prepare and execute the query to fetch user data
                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $userRecord = $stmt->fetch();

                if ($userRecord && password_verify($password, $userRecord['password'])) {
                    // Password is correct, set session variables
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $userRecord['user_id'];
                    $_SESSION['username'] = $userRecord['username'];
                    $_SESSION['email'] = $userRecord['email'];
                    $_SESSION['admin'] = $userRecord['admin'];

                    // Redirect to home page
                    header('Location: controller.php?action=home');
                    exit();
                } else {
                    // Invalid credentials
                    echo 'Invalid username or password.';
                }
            } else {
                // Missing username or password
                echo $twig->render('login.html.twig', ['user' => $user]);
            }
        } else {
            // Display login form
            echo $twig->render('login.html.twig', ['user' => $user]);
        }
        break;

    case 'transactions':
        if ($isAuthenticated) {
            echo $twig->render('transactions.html.twig', ['user' => $user]);
        } else {
            // Redirect to login if not authenticated
            header('Location: controller.php?action=login');
            exit();
        }
        break;

    case 'presentations':
        if ($isAuthenticated) {
            echo $twig->render('presentations.html.twig', ['user' => $user]);
        } else {
            // Redirect to login if not authenticated
            echo 'Please login before clicking on transaction';
            header('Location: controller.php?action=login');
            exit();
        }
        break;

    case 'admin':
        if ($isAuthenticated && $isAdmin) {
            echo $twig->render('adminPage.html.twig', ['user' => $user]);
        } else {
            // Redirect to home if not authorized
            header('Location: controller.php?action=home');
            exit();
        }
        break;

    case 'logout':
        session_start();
        session_unset();
        session_destroy();
        header('Location: controller.php?action=home');
        exit();

    case 'home':
    default:
        echo $twig->render('index.html.twig', ['user' => $user]);
        break;
}
?>
