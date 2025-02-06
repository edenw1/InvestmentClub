<?php
session_start();
require 'db.php';
require 'API.php';
dbConnect();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{% block title %}Stock Website{% endblock %}</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="homePageStyles.css">
</head>

<body>
    <div class="sidebar">
        <img src="Inv_Logo.png" alt="Logo">
        <p></p>
    </div>
    <div class="content">
        <header>
            <h1>{% block header %}Investment Club{% endblock %}</h1>
        </header>
        <nav>
            <div class="container">
                <?php
                // Display menu if the user is logged in
                if (isset($_SESSION['user_id'])) {
                    echo '<a href="transactions.php">See Transactions</a>';
                    echo '<a href="presentations.php">Presentations</a>';

                    // Admin Panel link for admins only
                    if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
                        echo '<a href="adminPage.php">Admin Panel</a>';
                    }

                    // Logout button
                    echo '<a href="logout.php">Logout</a>';

                    // Process stock symbols
                    processStockSymbols();
                } else {
                    // Login button for guests
                    echo '<a href="login.html">Login</a>';
                }
                
                function processStockSymbols() {
                    global $pdo;
                  
                    // Select all stock symbols
                    $query = "SELECT symbol FROM stocks";
                    $stmt = $pdo->prepare($query);
                    $stmt->execute();
                  
                    if ($stmt->rowCount() > 0) {
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $symbol = $row['symbol'];
                            echo "<strong> Processing stock: $symbol </strong><div class='stock-card'>";
                            // Example of processing the stock symbol with an API call
                            parseProfile(getProfile($symbol));
                            parseQuote(getQuote($symbol));
                            parseTrends(getTrends($symbol));
                            parseNews(getNews($symbol, 1)); //input the scope of prior days to include in the news call. Can not exceed 1 yr
                            echo "</div>";
                        }
                    } else {
                        echo "No stocks found.";
                    }
                  }

                ?>
        </nav>

    </div>
    <footer>
        <p>Muskingum Univeristy Investment Club</p>
    </footer>
    </div>
</body>

</html>
