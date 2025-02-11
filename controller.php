<?php
session_start();

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
$action = $_GET['action'] ?? 'home';

switch ($action) {
    case 'login':
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (isset($_POST['username']) && isset($_POST['password'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];
                try {
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
                        echo "Login successful! Redirecting...";
                        header('Refresh:2; url=controller.php?action=home');
                        exit();
                    } else {
                        echo "Invalid username or password.";
                    }
                } catch (Exception $e) {
                    echo "Error logging in: " . $e->getMessage();
                }
            }
        }
        echo $twig->render('login.html.twig', ['user' => $user]);
        break;

        case 'transactions':
            if ($isAuthenticated) {
                try {
                    $sql = "SELECT s.symbol AS stock_symbol, 
                                   s.name AS stock_name, 
                                   t.transaction_type, 
                                   t.quantity, 
                                   t.price_per_share, 
                                   t.buy_sell_date 
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
                    
                } catch (Exception $e) {
                    echo "Error fetching transactions: " . $e->getMessage();
                }
            } else {
                header('Location: controller.php?action=login');
                exit();
            }
            break;
    case 'admin':
        if ($isAuthenticated && $isAdmin) {
            try {
                $stmt = $pdo->prepare("SELECT stock_id, symbol FROM stocks");
                $stmt->execute();
                $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);
                $stmt = $pdo->prepare("SELECT * FROM stockProposal WHERE status = 'pending'");
                $stmt->execute();
                $proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
                echo $twig->render('adminPage.html.twig', [
                    'user' => $user,
                    'stocks' => $stocks,
                    'proposals' => $proposals
                ]);
            } catch (Exception $e) {
                echo "Error loading admin page: " . $e->getMessage();
            }
        } else {
            header('Location: controller.php?action=home');
            exit();
        }
        break;
    
    case 'logout':
        session_unset();
        session_destroy();
        echo "Logged out successfully! Redirecting...";
        header('Refresh:2; url=controller.php?action=home');
        exit();

    case 'presentations':
        if ($isAuthenticated) {
            try {
                $sql = "SELECT p.presentation_id, p.title, p.date, p.file_path, u.username
                FROM presentation p
                JOIN users u ON p.user_id = u.user_id
                ORDER BY p.date DESC";
        
                $stmt = $pdo->prepare($sql);
                $stmt->execute();
                $presentations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
                foreach ($presentations as &$presentation) {
                    $sql = "SELECT sp.stock_symbol, sp.stock_name, sp.action, sp.quantity
                            FROM stockProposal sp
                            WHERE sp.presentation_id = :presentation_id";
                    $stmt = $pdo->prepare($sql);
                    $stmt->bindParam(':presentation_id', $presentation['presentation_id']);
                    $stmt->execute();
                    $presentation['stock_proposals'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
                }
    
                echo $twig->render('presentations.html.twig', [
                    'user' => $user,
                    'presentations' => $presentations
                ]);
            } catch (Exception $e) {
                echo "Error fetching presentations: " . $e->getMessage();
            }
        } else {
            header('Location: controller.php?action=login');
            exit();
        }
        break;

    case 'about':
        echo $twig->render('about.html.twig', ['user' => $user]);
        break;

    case 'stock':
        try {
            // 1. Get requested symbol
            $symbol = $_GET['symbol'] ?? null;
            if (!$symbol) throw new Exception("No stock symbol specified");
        
            // 2. Fetch raw data using your existing functions
            $profileRaw = getProfile($symbol);
            $quoteRaw = getQuote($symbol);
            $trendsRaw = getTrends($symbol);
            $financialsRaw = getFinancials($symbol);
            $newsRaw = getNews($symbol, 5); // Get 5 news items
        
            // 3. Structure data for Twig template
            $data = [
                'profile' => [
                    'name' => $profileRaw['name'] ?? 'N/A',
                    'ticker' => $profileRaw['ticker'] ?? $symbol,
                    'country' => $profileRaw['country'] ?? 'N/A',
                    'currency' => $profileRaw['currency'] ?? 'N/A',
                    'ipo' => $profileRaw['ipo'] ?? 'N/A',
                    'marketCap' => $profileRaw['marketCapitalization'] ?? 0,
                    'website' => $profileRaw['weburl'] ?? '#',
                    'industry' => $profileRaw['finnhubIndustry'] ?? 'N/A'
                ],
                
                'quote' => [
                    'current' => $quoteRaw['c'] ?? 0,
                    'high' => $quoteRaw['h'] ?? 0,
                    'low' => $quoteRaw['l'] ?? 0,
                    'open' => $quoteRaw['o'] ?? 0,
                    'previousClose' => $quoteRaw['pc'] ?? 0,
                    'timestamp' => isset($quoteRaw['t']) ? date('Y-m-d H:i:s', $quoteRaw['t']) : 'N/A'
                ],
                
                'trends' => array_map(function($t) {
                    return [
                        'period' => $t['period'] ?? 'N/A',
                        'strongBuy' => $t['strongBuy'] ?? 0,
                        'buy' => $t['buy'] ?? 0,
                        'hold' => $t['hold'] ?? 0,
                        'sell' => $t['sell'] ?? 0,
                        'strongSell' => $t['strongSell'] ?? 0
                    ];
                }, $trendsRaw ?? []),
                
                'financials' => isset($financialsRaw['data'][0]['report']['bs']) 
                    ? $financialsRaw['data'][0]['report']['bs'] 
                    : [],
                    
                'news' => array_map(function($n) {
                    return [
                        'headline' => $n['headline'] ?? 'No headline',
                        'source' => $n['source'] ?? 'Unknown',
                        'url' => $n['url'] ?? '#'
                    ];
                }, $newsRaw ?? [])
            ];
        
           
            echo $twig->render('stock.html.twig', $data);
        
        } catch (Exception $e) {
            die("Error loading stock data: " . $e->getMessage());
        }
        break;
    case 'home':
        default:
        try {
            
            $stocks = [];
    
            $query = "SELECT symbol FROM stocks";
            $stmt = $pdo->prepare($query);
            $stmt->execute();
    
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $symbol = $row['symbol'];
                    $stockData = [
                        'symbol' => $symbol,
                        'profile' => (getProfile($symbol)),
                        'quote' => parseQuote(getQuote($symbol)),
                        'trends' => (getTrends($symbol)),
                        //'news' => parseNews(getNews($symbol, 1))
                    ];
                    $stocks[] = $stockData;
                }
            }
            
            // Pass both stocks and user in a single array
            echo $twig->render('index.html.twig', [
                'stocks' => $stocks,
                'user' => $user
            ]);
    
        } catch (Exception $e) {
            echo "Error loading home page: " . $e->getMessage();
        }
    }