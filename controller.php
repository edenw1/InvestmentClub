<?php

require_once __DIR__ . '/vendor/autoload.php';
//require_once 'db.php'; 
require 'API.php';
require 'databaseFunctions.php';
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

function handlePortfolio($twig, $user, $isAuthenticated) {
    if (!$isAuthenticated) {
        header('Location: home');
        exit();
    }
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT symbol FROM stocks WHERE active = 1");        
        $stocks = array_map(fn($row) => [
            'symbol' => $row['symbol'],
            'profile' => getProfile($row['symbol']),
            'quote' => getQuote($row['symbol']),
            'trends' => getTrends($row['symbol'])
        ], $stmt->fetchAll(PDO::FETCH_ASSOC));
        echo $twig->render('index.html.twig', ['stocks' => $stocks, 'user' => $user]);
    } catch (Exception $e) {
        echo "Error portfolio: " . $e->getMessage();
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
        $proposals = $pdo->query("
            SELECT sp.*, u.username 
            FROM stockProposal sp
            JOIN users u ON sp.proposed_by = u.user_id
            WHERE sp.status = 'pending'
        ")->fetchAll(PDO::FETCH_ASSOC);

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
        $presentations = getPresentations();
        $stockProposals = getStockProposals();
        
        $presentationsById = [];
        foreach ($presentations as $presentation) {
            $presentationId = $presentation['presentation_id'];
            $votes = getVotesByPresentationId($presentationId);
            
            $numYes = 0;
            $numNo = 0;
            
            foreach ($votes as $vote) {
                if ($vote['yes_or_no'] == 1) {
                    $numYes++;
                } elseif ($vote['yes_or_no'] == 0) {
                    $numNo++;
                }
            }
            $totalVotes = $numYes + $numNo;
            if ($totalVotes > 0) {
                $percentYes = ($numYes / $totalVotes) * 100;
                $percentNo = ($numNo / $totalVotes) * 100;
            } else {
                $percentYes = 100;
                $percentNo = 0;
            }
            
            $presentationsById[$presentationId] = array_merge($presentation, [
                'stock_proposals' => [],
                'numYes' => $numYes,
                'numNo' => $numNo,
                'percentYes' => $percentYes,
                'percentNo' => $percentNo
            ]);
        }

        foreach ($stockProposals as $proposal) {
            if (isset($presentationsById[$proposal['presentation_id']])) {
                $presentationsById[$proposal['presentation_id']]['stock_proposals'][] = $proposal;
            }
        }

        echo $twig->render('presentations.html.twig', [
            'user' => $user,
            'presentations' => array_values($presentationsById)
        ]);
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

        $profile = getProfile($symbol);
        $quote = getQuote($symbol);
        $trends = getTrends($symbol);
        $financials = getFinancials($symbol);
        $news = getNews($symbol, 5);

        echo $twig->render('stock.html.twig', [
            'profile' => $profile,
            'quote' => $quote,
            'trends' => $trends,
            'financials' => $financials,
            'news' => $news
        ]);
    } catch (Exception $e) {
        die("Error loading stock data: " . $e->getMessage());
    }
}


function handleHome($twig, $user) {
    global $pdo;
    try {
        $stmt = $pdo->query("SELECT symbol FROM stocks WHERE watchlist = 1");        
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

function handleKey_Members($twig, $user) {
    echo $twig->render('key_members.html.twig', ['user' => $user]);
}
function edit_About($twig, $user) {
    echo $twig->render('edit.html.twig', ['user' => $user]);
}
function keyMemberEdit($twig, $user){
    echo $twig->render('keyMembersEdit.html.twig', ['user' => $user]);
}