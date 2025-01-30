<?php
session_start();
require 'db.php';
require 'API.php';
dbConnect();

echo "Welcome, Investment Club!";

if (isset($_SESSION['user_id'])) {
    if (isset($_SESSION['admin']) && $_SESSION['admin'] == 1) {
		//only admins buttons
        echo '<form action="adminPage.php" method="post">
                <input type="submit" value="Admin Panel">
              </form>';
    }
		//only logged in users
    echo '<form action="logout.php" method="post">
            <input type="submit" value="Logout">
          </form>';
          echo '<form action="transactions.php" method="post">
          <input type="submit" value="See Transactions">
        </form>';
        echo '<form action="presentations.php" method="post">
        <input type="submit" value="Presentations">
        </form>';

    //iterate each stock as $ticker
    processStockSymbols();

} else {
    //guests
    echo '<form action="login.php" method="post">
            <input type="submit" value="Login">
          </form>';
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
          echo "<strong> Processing stock: $symbol </strong>";
          // Example of processing the stock symbol with an API call
          parseProfile(getProfile($symbol));
          parseQuote(getQuote($symbol));
          parseTrends(getTrends($symbol));
          echo "<hr>";
      }
  } else {
      echo "No stocks found.";
  }
}
?>
