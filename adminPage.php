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
    need to ask on how I will be adding stocks? right now stock_ids were added manually
    into the database and the transaction will look for the stock with that ID
    <br>
        <label for="stock_id">Stock ID:</label>
        <input type="number" id="stock_id" name="stock_id" required><br><br>
        
        <label for="quantity">Quantity:</label>
        <input type="number" id="quantity" name="quantity" required><br><br>
        
        <label for="price_per_share">Price per Share:</label>
        <input type="text" id="price_per_share" name="price_per_share" required><br><br>
        
        <label for="buy_sell_date">Transaction Date:</label><br>
        <input type="date" id="buy_sell_date" name="buy_sell_date" required><br><br>

        <input type="submit" value="Make Transaction">
    </form>
</body>
</html>
