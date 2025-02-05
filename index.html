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
            <a href="#">Home</a>
            <a href="#">Transaction</a>
            <a href="#">About</a>
            <a href="#">Presentations</a>
        </nav>
        <div class="container">
            <?php
            // Welcome message for logged-in users
            if (isset($_SESSION['user_id'])) {
                echo "<h2>Welcome, Investment Club!</h2>";

                // Admin Panel button (only visible for admins)
                if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
                    echo '<form action="adminPage.php" method="post">
                            <input type="submit" value="Admin Panel">
                          </form>';
                }

                // Logged-in user buttons
                echo '<form action="logout.php" method="post">
                        <input type="submit" value="Logout">
                      </form>';
                echo '<form action="transactions.php" method="post">
                        <input type="submit" value="See Transactions">
                      </form>';
                echo '<form action="presentations.php" method="post">
                        <input type="submit" value="Presentations">
                      </form>';

                // Iterate through stock symbols (function is assumed to exist in API.php)
                processStockSymbols();

            } else {
                // If user is not logged in (guest)
                echo '<form action="login.php" method="post">
                        <input type="submit" value="Login">
                      </form>';
            }
            ?>
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
            <p>&copy; 2025 Stock Tracker. All rights reserved.</p>
        </footer>
    </div>
</body>
</html>
