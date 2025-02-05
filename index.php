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
    <title>Stock Website</title>
    <!-- Link to the external CSS file -->
    <link rel="stylesheet" href="homePageStyles.css">
</head>

<body>
    <div class="sidebar">
        <img src="logo.png" alt="Logo">
        <p></p>
    </div>
    <div class="content">
        <header>
            <h1>Investment Club</h1>
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
                    echo '<a href="login.php">Login</a>';
                }
                ?>
        </nav>
        <div class="stock-card">
            <!-- Stock Card content -->
        </div>
        <div class="stock-card">
            <!-- Stock Card content -->
        </div>
        <div class="stock-card">
            <!-- Stock Card content -->
        </div>
    </div>
    <footer>
        <p>&copy; Muskingum Univeristy Investment Club</p>
    </footer>
    </div>
</body>

</html>