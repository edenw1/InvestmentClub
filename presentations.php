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

        <?php for ($i = 0; $i < 3; $i++): ?>
            <label for="stock<?= $i ?>_symbol">Stock Symbol:</label>
            <input type="text" id="stock<?= $i ?>_symbol" name="stocks[<?= $i ?>][symbol]" placeholder="AAPL">
            
            <label for="stock<?= $i ?>_name">Stock Name:</label>
            <input type="text" id="stock<?= $i ?>_name" name="stocks[<?= $i ?>][name]" placeholder="Apple Inc.">
            
            <label for="stock<?= $i ?>_action">Action:</label>
            <select id="stock<?= $i ?>_action" name="stocks[<?= $i ?>][action]">
                <option value="">Select Action</option>
                <option value="add">Add to Portfolio</option>
                <option value="remove">Remove from Portfolio</option>
                <option value="increase">Increase Holdings</option>
                <option value="decrease">Decrease Holdings</option>
            </select>

            <label for="stock<?= $i ?>_quantity">Quantity:</label>
            <input type="number" id="stock<?= $i ?>_quantity" name="stocks[<?= $i ?>][quantity]" min="1">
            
            <br><br>
        <?php endfor; ?>

        <input type="submit" value="Upload Presentation">
    </form>

    <form action="/" method="post">
        <input type="submit" value="Back to Home">
    </form>
    <br>
</body>
</html>