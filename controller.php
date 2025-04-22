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
                header('Location: /');
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
        $sortBy = $_GET['sort_by'] ?? 'date_desc';
        $transactions = getAllTransactions($sortBy);
        echo $twig->render('transactions.html.twig', [
            'user' => $user,
            'transactions' => $transactions,
            'sort_by' => $sortBy
        ]);
    } catch (Exception $e) {
        echo "Error fetching transactions: " . $e->getMessage();
    }
}



function handlePortfolio($twig, $user, $isAuthenticated) {
    if (!$isAuthenticated) {
        header('Location: login');
        exit();
    }
    try {
        // Make sure this function correctly fetches data based on the logged-in user
        $portfolioDetails = getPortfolioStockDetails($_SESSION['user_id']); // Pass user_id if necessary
        $stocksDataForView = [];
        $totalPortfolioNetEarnings = 0;
        $totalPortfolioCurrentValue = 0; // <-- Initialize Total Current Value
        $totalPortfolioCost = 0;         // <-- Initialize Total Cost (Invested)

        foreach ($portfolioDetails as $stock) {
            $quoteData = getQuote($stock['symbol']);
            $currentPrice = $quoteData['c'] ?? 0;
            $profileData = getProfile($stock['symbol']);

            $totalBought = floatval($stock['total_quantity_bought'] ?? 0); // Ensure float for division
            $totalSold = floatval($stock['total_quantity_sold'] ?? 0);
            $totalCost = floatval($stock['total_cost'] ?? 0);
            $totalProceeds = floatval($stock['total_proceeds'] ?? 0);

            $netQuantityHeld = $totalBought - $totalSold;
            $currentValue = ($netQuantityHeld > 0 && $currentPrice > 0) ? ($netQuantityHeld * $currentPrice) : 0;
            $netEarnings = ($totalProceeds + $currentValue) - $totalCost;

            // Calculate Average Purchase Price
            $averagePurchasePrice = ($totalBought > 0) ? ($totalCost / $totalBought) : 0; // <-- Calculate Avg Price

            // Accumulate totals for the portfolio summary
            $totalPortfolioNetEarnings += $netEarnings;
            $totalPortfolioCurrentValue += $currentValue; // <-- Accumulate Current Value
            $totalPortfolioCost += $totalCost;             // <-- Accumulate Total Cost

            $stocksDataForView[] = [
                'stock_id' => $stock['stock_id'] ?? null,
                'symbol' => $stock['symbol'],
                'name' => $stock['name'] ?? ($profileData['name'] ?? 'N/A'),
                'total_quantity_bought' => $totalBought,
                'total_quantity_sold' => $totalSold,
                'net_quantity_held' => $netQuantityHeld,
                'total_cost' => $totalCost,
                'total_proceeds' => $totalProceeds,
                'current_price' => $currentPrice,
                'current_value' => $currentValue,
                'net_earnings' => $netEarnings,
                'average_purchase_price' => $averagePurchasePrice, // <-- Add Avg Price to stock data
                'quote' => $quoteData,
                'profile' => $profileData,
            ];
        }

        // Pass the calculated totals along with other data
        echo $twig->render('index.html.twig', [
            'displayStocks' => $stocksDataForView,
            'viewType' => 'portfolio',
            'user' => $user,
            'page_title' => '- Portfolio Page',
            'total_portfolio_earnings' => $totalPortfolioNetEarnings,
            'total_portfolio_value' => $totalPortfolioCurrentValue, // <-- Pass Total Value
            'total_portfolio_cost' => $totalPortfolioCost          // <-- Pass Total Cost
        ]);

    } catch (Exception $e) {
        error_log("Error in handlePortfolio: " . $e->getMessage());
        echo $twig->render('index.html.twig', [
             'error_message' => 'Could not load portfolio data. Error: ' . $e->getMessage(),
             'viewType' => 'portfolio',
             'user' => $user,
             'page_title' => '- Portfolio Error',
             // Pass defaults or nulls for the new values on error
             'total_portfolio_earnings' => null,
             'total_portfolio_value' => null,
             'total_portfolio_cost' => null
        ]);
    }
}
function handleAdmin($twig, $user, $isAuthenticated) {
    if (!$isAuthenticated) {
        header('Location: login');
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
    header('Location: /');
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
    $contents = showContent();
    
 //   echo $twig->render('about.html.twig', ['contents' => $contents]);
    
    echo $twig->render('about.html.twig', ['user' => $user, 'contents' => $contents]);
}

function handleStock($twig, $user) {
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
            'news' => $news,
            'user' => $user
        ]);
    } catch (Exception $e) {
        die("Error loading stock data: " . $e->getMessage());
    }
}


function handleHome($twig, $user) {
    try {
        $watchlistStocks = fetchWatchlistStocks(); // Fetches data including profile and quote

        // Pass using 'displayStocks' and add 'viewType'
        echo $twig->render('index.html.twig', [
            'displayStocks' => $watchlistStocks, // Standardized variable name
            'viewType' => 'watchlist',           // Flag indicating the context
            'user' => $user,
            'page_title' => '- Watchlist'       // Adjust page title if needed
        ]);
    } catch (Exception $e) {
         error_log("Error in handleHome: " . $e->getMessage());
         echo $twig->render('index.html.twig', [
              'error_message' => 'Could not load watchlist data.',
              'viewType' => 'watchlist', // Still indicate context on error
              'user' => $user,
              'page_title' => '- Watchlist Error'
         ]);
    }
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