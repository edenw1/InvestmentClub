<?php
session_start();
require_once __DIR__ . '/vendor/autoload.php';
require_once 'controller.php';

$router = new AltoRouter();

$router->setBasePath('/InvestmentClub');

$router->map('GET', '/', function () {
    global $twig, $user;
    handleHome($twig, $user);
});

$router->map('GET|POST', '/login', function () {
    global $twig, $user;
    handleLogin($twig, $user);
});

$router->map('GET', '/transactions', function () {
    global $twig, $user, $isAuthenticated;
    handleTransactions($twig, $user, $isAuthenticated);
});

$router->map('GET', '/portfolio', function () {
    global $twig, $user, $isAuthenticated;
    handlePortfolio($twig, $user, $isAuthenticated);
});

$router->map('GET', '/admin', function () {
    global $twig, $user, $isAuthenticated, $isAdmin;
    handleAdmin($twig, $user, $isAuthenticated);
});

$router->map('GET', '/logout', function () {
    handleLogout();
});

$router->map('GET', '/presentations', function () {
    global $twig, $user, $isAuthenticated;
    handlePresentations($twig, $user, $isAuthenticated);
});

$router->map('GET', '/about', function () {
    global $twig, $user;
    handleAbout($twig, $user);
});
$router->map('GET', '/edit', function () {
    global $twig, $user;
    edit_About($twig, $user);
});

$router->map('GET', '/key-members', function () {
    global $twig, $user;
    handleKey_Members($twig, $user);
});

$router->map('GET', '/key-members-edit', function () {
    global $twig, $user;
    keyMemberEdit($twig, $user);
});

$router->map('GET', '/Stock/[*:symbol]', function ($symbol) {
    global $twig;
    global $user;
    $_GET['symbol'] = $symbol; 
    handleStock($twig, $user);
});


$router->map('GET|POST', '/Stock/[*:symbol]', function ($symbol) {
    global $twig;
    global $user;
    //require 'databaseFunctions.php';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        if (!empty($symbol) && !empty($name)) {
            if (addToWatchlist($symbol, $name)) {
                $_SESSION['message'] = "Stock added to watchlist successfully.";
            } else {
                $_SESSION['error'] = "Error adding stock to watchlist.";
            }
        } else {
            $_SESSION['error'] = "Error: Name is required.";
        }
    }

    $_GET['symbol'] = $symbol;
    handleStock($twig, $user);
});

$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "Page not found!";
}
