<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once 'db.php'; 
require 'API.php';

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
    'auto_reload' => true,
]);
dbConnect();

$isAuthenticated = isset($_SESSION['user_id']);
$isAdmin = isset($_SESSION['admin']) && $_SESSION['admin'];

$user = [
    'name' => $isAuthenticated ? $_SESSION['username'] : 'Guest',
    'email' => $isAuthenticated ? $_SESSION['email'] : '',
    'is_authenticated' => $isAuthenticated,
    'is_admin' => $isAdmin,
];

function handleLogin($twig, $user) {
    global $pdo;
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->bindParam(':username', $_POST['username']);
            $stmt->execute();
            $userRecord = $stmt->fetch();
            if ($userRecord && password_verify($_POST['password'], $userRecord['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userRecord['user_id'];
                $_SESSION['username'] = $userRecord['username'];
                $_SESSION['email'] = $userRecord['email'];
                $_SESSION['admin'] = $userRecord['admin'];
                header('Location: /InvestmentClub');
                exit();
            }
        } catch (Exception $e) {
            echo "Error logging in: " . $e->getMessage();
        }
    }
    echo $twig->render('login.html.twig', ['user' => $user]);
}

function handleTransactions($twig, $user, $isAuthenticated) {
    global $pdo;
    if (!$isAuthenticated) {
        header('Location:login');
        exit();
    }
    try {
        $stmt = $pdo->prepare("SELECT s.symbol AS stock_symbol, s.name AS stock_name, t.transaction_type, t.quantity, t.price_per_share, t.buy_sell_date FROM transactions t JOIN stocks s ON t.stock_id = s.stock_id ORDER BY t.buy_sell_date DESC");
        $stmt->execute();
        echo $twig->render('transactions.html.twig', ['user' => $user, 'transactions' => $stmt->fetchAll()]);
    } catch (Exception $e) {
        echo "Error fetching transactions: " . $e->getMessage();
    }
}

function handleAdmin($twig, $user, $isAuthenticated, $isAdmin) {
    global $pdo;
    if (!$isAuthenticated || !$isAdmin) {
        header('Location: home');
        exit();
    }
    try {
        $stocks = $pdo->query("SELECT stock_id, symbol FROM stocks")->fetchAll(PDO::FETCH_ASSOC);
        $proposals = $pdo->query("SELECT * FROM stockProposal WHERE status = 'pending'")->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('adminPage.html.twig', ['user' => $user, 'stocks' => $stocks, 'proposals' => $proposals]);
    } catch (Exception $e) {
        echo "Error loading admin page: " . $e->getMessage();
    }
}

function handleLogout() {
    session_unset();
    session_destroy();
    header('Location: /InvestmentClub');
    exit();
}

function handlePresentations($twig, $user, $isAuthenticated) {
    global $pdo;
    if (!$isAuthenticated) {
        header('Location: login');
        exit();
    }
    try {
        $stmt = $pdo->prepare("SELECT p.presentation_id, p.title, p.date, p.file_path, u.username FROM presentation p JOIN users u ON p.user_id = u.user_id ORDER BY p.date DESC");
        $stmt->execute();
        $presentations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo $twig->render('presentations.html.twig', ['user' => $user, 'presentations' => $presentations]);
    } catch (Exception $e) {
        echo "Error fetching presentations: " . $e->getMessage();
    }
}

function handleAbout($twig, $user) {
    echo $twig->render('about.html.twig', ['user' => $user]);
}

function handleStock($twig) {
    try {
        $symbol = $_GET['symbol'] ?? null;
        if (!$symbol) throw new Exception("No stock symbol specified");
        echo $twig->render('stock.html.twig', [
            'profile' => getProfile($symbol),
            'quote' => getQuote($symbol),
            'trends' => getTrends($symbol),
            'financials' => getFinancials($symbol),
            'news' => getNews($symbol, 5)
        ]);
    } catch (Exception $e) {
        die("Error loading stock data: " . $e->getMessage());
    }
}

function handleHome($twig, $user) {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT symbol FROM stocks");
        $stocks = array_map(fn($row) => [
            'symbol' => $row['symbol'],
            'profile' => getProfile($row['symbol']),
            'quote' => getQuote($row['symbol']),
            'trends' => getTrends($row['symbol'])
        ], $stmt->fetchAll(PDO::FETCH_ASSOC));
        echo $twig->render('index.html.twig', ['stocks' => $stocks, 'user' => $user]);
    } catch (Exception $e) {
        echo "Error loading home page: " . $e->getMessage();
    }
}
