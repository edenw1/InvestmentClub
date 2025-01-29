<?php
session_start();
require 'db.php';
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
} else {
    //guests
    echo '<form action="login.php" method="post">
            <input type="submit" value="Login">
          </form>';
}
?>
