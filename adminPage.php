<?php
session_start();
REQUIRE 'db.php';
dbConnect();
if (!isset($_SESSION['admin'])) {
    header("Location: index.php"); 
	//not an admin, take to home
    exit();
}
?>

<html>
<head>
    <title>Admin Functionality </title>
</head>
<h1> Admin Functionality </h1>
<form action="index.php" method="post">
	<input type="submit" value="Back to Home">
	</form>
    <br>
<body>
    <h2>Add New Club Member</h2>
    <form action="addmemberForm.php" method="post">
        <label for="username">Username:</label><br>
        <input type="text" id="username" name="username" required><br><br>
        
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required>
        <br><br>
        
        <label for="password">Password:</label><br>
        <input type="password" id="password" name="password" required><br><br>

        <label for="admin">Make Admin:</label>
        <input type="checkbox" name="admin" value="1"><br>
        
        <input type="submit" value="Add Member">
        <br>
    </form>
    <h2>Remove Club Member</h2>
    <form action="removeMemberForm.php" method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <input type="submit" value="Remove Member"><br>
    </form>

    <h2>Make Stock Transaction</h2>
    <form action="addTransaction.php" method="post">
        <label for="transaction_type">Transaction Type:</label>
        <select id="transaction_type" name="transaction_type" required>
            <option value="Buy">Buy</option>
            <option value="Sell">Sell</option>
        </select><br><br>
        
        <label for="stock_id">Stock:</label>
        <select id="stock_id" name="stock_id" required>
            <?php
            //fetch all 'approved' stocks from the stocks table
            $selectStocksQuery = "SELECT stock_id, symbol, name FROM stocks";
            $stmt = $pdo->prepare($selectStocksQuery);
            $stmt->execute();
            $stocks = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($stocks as $stock) {
                echo "<option value='" . $stock['stock_id'] . "'>" . $stock['symbol'] . " - " . $stock['name'] . "</option>";
            }
            ?>
        </select><br><br>
        
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br><br>
        
        <label for="price_per_share">Price per Share:</label>
        <input type="text" id="price_per_share" name="price_per_share" required><br><br>
        
        <label for="buy_sell_date">Transaction Date:</label><br>
        <input type="date" id="buy_sell_date" name="buy_sell_date" required><br><br>

        <input type="submit" value="Make Transaction">
    </form>

    <h2>Pending Stock Proposals</h2>
    <?php
        $stockProposals = "SELECT * FROM stockProposal WHERE status = 'pending'";
        $stmt = $pdo->prepare($stockProposals);
        $stmt->execute();
        $proposals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($proposals) > 0) {
        foreach ($proposals as $proposal) {
            echo "<div>";
            echo "<p><strong>Proposal ID:</strong> " . $proposal['proposal_id'] . "</p>";
            echo "<p><strong>Stock Symbol:</strong> " . $proposal['stock_symbol'] . "</p>";
            echo "<p><strong>Stock Name:</strong> " . $proposal['stock_name'] . "</p>";
            echo "<p><strong>Proposed By User ID:</strong> " . $proposal['proposed_by'] . "</p>";
            echo "<form action='pendingStocks.php' method='post'>";
            echo "<input type='hidden' name='proposal_id' value='" . $proposal['proposal_id'] . "'>";
            echo "<label for='status'>Action:</label>";
            echo "<select name='status' required>";
            echo "<option value='approved'>Approve</option>";
            echo "<option value='rejected'>Reject</option>";
            echo "</select>";
            echo "<input type='submit' value='Submit'>";
            echo "</form>";
            echo "</div>";
        }
    } else {
        echo "<p>No pending stock proposals .</p>";
    }
    ?>
</body>
</html>
