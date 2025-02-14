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

$router->map('GET', '/admin', function () {
    global $twig, $user, $isAuthenticated, $isAdmin;
    handleAdmin($twig, $user, $isAuthenticated, $isAdmin);
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

$router->map('GET', '/stock/[*:symbol]', function ($symbol) {
    global $twig;
    $_GET['symbol'] = $symbol; 
    handleStock($twig);
});

$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else {
    header($_SERVER["SERVER_PROTOCOL"] . ' 404 Not Found');
    echo "Page not found!";
}
