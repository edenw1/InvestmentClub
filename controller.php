<?php
session_start();

require_once __DIR__ . '/vendor/autoload.php';
require_once 'db.php'; 

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
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['username']) && isset($_POST['password'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];

                $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->bindParam(':username', $username);
                $stmt->execute();
                $userRecord = $stmt->fetch();

                if ($userRecord && password_verify($password, $userRecord['password'])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $userRecord['user_id'];
                    $_SESSION['username'] = $userRecord['username'];
                    $_SESSION['email'] = $userRecord['email'];
                    $_SESSION['admin'] = $userRecord['admin'];

                    header('Location: controller.php?action=home');
                    exit();
                } else {
                    echo 'Invalid username or password.';
                }
            } else {
                echo $twig->render('login.html.twig', ['user' => $user]);
            }
        } else {
            echo $twig->render('login.html.twig', ['user' => $user]);
        }
        break;

    case 'transactions':
        if ($isAuthenticated) {
            $sql = "SELECT s.name AS stock_name, t.transaction_type, t.quantity, t.price_per_share, t.buy_sell_date 
            FROM transactions t
            JOIN stocks s ON t.stock_id = s.stock_id
            ORDER BY t.buy_sell_date DESC";
    

            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $transactions = $stmt->fetchAll();

            echo $twig->render('transactions.html.twig', [
                'user' => $user,
                'transactions' => $transactions
            ]);
        } else {
            header('Location: controller.php?action=login');
            exit();
        }
        break;

    case 'presentations':
        if ($isAuthenticated) {
            echo $twig->render('presentations.html.twig', ['user' => $user]);
        } else {
            echo 'Please login before clicking on transaction';
            header('Location: controller.php?action=login');
            exit();
        }
        break;

    case 'admin':
        if ($isAuthenticated && $isAdmin) {
            $stmt = $pdo->prepare("SELECT stock_id, symbol FROM stocks");
            $stmt->execute();
            $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $stockProposalsQuery = "SELECT * FROM stockProposal WHERE status = 'pending'";
            $stmt = $pdo->prepare($stockProposalsQuery);
            $stmt->execute();
            $proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
            echo $twig->render('adminPage.html.twig', [
                'user' => $user,
                'stocks' => $stocks,
                'proposals' => $proposals
            ]);
        } else {
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
    case 'about':
    case 'home':
    default:
        echo $twig->render('index.html.twig', ['user' => $user]);
        exit();
}
?>
