<?php
session_start();
require 'db.php';
dbConnect();
?>

<html>
<head>
    <title> Presentations </title>
</head>
<body>
    <h1>Add Presentation</h1>
    <form action="addPresentation.php" method="post">
        <label for="title">Presentation Title:</label>
        <input type="text" id="title" name="title" required>
        <br><br>

        <label for="url">Presentation URL:</label>
        <input type="url" id="url" name="url" required>
        <br><br>

        <h3>Stock Proposals</h3>
        <label for="stock1_symbol">Stock Symbol:</label>
        <input type="text" id="stock1_symbol" name="stocks[0][symbol]" placeholder="e.g., AAPL" required>
        <label for="stock1_name">Stock Name:</label>
        <input type="text" id="stock1_name" name="stocks[0][name]" placeholder="e.g., Apple Inc." required>
        <br><br>

        <label for="stock2_symbol">Stock Symbol:</label>
        <input type="text" id="stock2_symbol" name="stocks[1][symbol]" placeholder="e.g., GOOGL">
        <label for="stock2_name">Stock Name:</label>
        <input type="text" id="stock2_name" name="stocks[1][name]" placeholder="e.g., Alphabet Inc.">
        <br><br>

        <label for="stock3_symbol">Stock Symbol:</label>
        <input type="text" id="stock3_symbol" name="stocks[2][symbol]" placeholder="e.g., AMZN">
        <label for="stock3_name">Stock Name:</label>
        <input type="text" id="stock3_name" name="stocks[2][name]" placeholder="e.g., Amazon.com Inc.">
        <br><br>

        <input type="submit" value="Upload Presentation">
    </form>

    <form action="index.php" method="post">
        <input type="submit" value="Back to Home">
        </form>
        <br>
</body>
</html>