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
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['username'], $_POST['password'])) {
        try {
            $userRecord = checkLogin($_POST['username'], $_POST['password']);
            
            if ($userRecord) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $userRecord['user_id'];
                $_SESSION['username'] = $userRecord['username'];
                $_SESSION['email'] = $userRecord['email'];
                $_SESSION['admin'] = $userRecord['admin'];
                header('Location: /InvestmentClub');
                exit();
            } else {
                echo "Invalid username or password.";
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
        $transactions = getAllTransactions();
        echo $twig->render('transactions.html.twig', [
            'user' => $user,
            'transactions' => $transactions
        ]);
    } catch (Exception $e) {
        echo "Error fetching transactions: " . $e->getMessage();
    }
}

function handlePortfolio($twig, $user, $isAuthenticated) {
    if (!$isAuthenticated) {
        header('Location: home');
        exit();
    }
    try {
        $activeStocks = selectActiveStocks(); 
        $stocks = array_map(fn($row) => [
            'symbol' => $row['symbol'],
            'profile' => getProfile($row['symbol']),
            'quote' => getQuote($row['symbol']),
            'trends' => getTrends($row['symbol'])
        ], $activeStocks);
        
        echo $twig->render('index.html.twig', ['stocks' => $stocks, 'user' => $user]);
    } catch (Exception $e) {
        echo "Error portfolio: " . $e->getMessage();
    }
}



function handleAdmin($twig, $user, $isAuthenticated, $isAdmin) {
    if (!$isAuthenticated || !$isAdmin) {
        header('Location: home');
        exit();
    }

    $stocks = fetchAllStocks();
    $proposals = fetchPendingProposals();

    echo $twig->render('adminPage.html.twig', [
        'user' => $user,
        'stocks' => $stocks,
        'proposals' => $proposals
    ]);
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
   // $contents = showContent();
    $contents = [
        [
            'title' => 'Sample Video',
            'description' => 'This is a sample video from YouTube.',
            'type' => 'video', // This tells Twig to render it as a video
            'url' => 'https://www.youtube.com/embed/CqKSNpSm4E0' // Use the embed link format
        ]
    ];
    
    echo $twig->render('about.html.twig', ['contents' => $contents]);
    
    echo $twig->render('about.html.twig', ['user' => $user, 'contents' => $contents]);
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
    $stocks = fetchWatchlistStocks();
    echo $twig->render('index.html.twig', ['stocks' => $stocks, 'user' => $user]);
}

function handleKey_Members($twig, $user) {
    $members = fetchKeyMembers();
    echo $twig->render('key_members.html.twig', ['user' => $user, 'members' => $members]);
}

function edit_About($twig, $user) {
    echo $twig->render('edit.html.twig', ['user' => $user]);
}
function keyMemberEdit($twig, $user){
    echo $twig->render('keyMembersEdit.html.twig', ['user' => $user]);
}