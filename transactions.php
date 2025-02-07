<?php
session_start();
require 'db.php';
dbConnect();

echo '<form action="controller.php" method="post">
<input type="submit" value="Back To Home">
</form>';

$sql = "SELECT s.symbol, s.name AS stock_name, t.transaction_type, t.quantity, t.price_per_share, t.buy_sell_date 
        FROM transactions t
        JOIN stocks s 
        ON t.stock_id = s.stock_id
        ORDER BY t.buy_sell_date DESC";

$stmt1 = $pdo->prepare($sql);
$stmt1->execute();
$transactions = $stmt1->fetchAll(PDO::FETCH_ASSOC);
?>

<table>
    <tr>
        <th>Stock</th>
        <th>Type</th>
        <th>Quantity</th>
        <th>Price per Share</th>
        <th>Date</th>
    </tr>
    <?php foreach ($transactions as $row) { ?>
    <tr>
        <td><?php echo $row['symbol'] . " - " . $row['stock_name'];  ?></td>
        <td><?php echo $row['transaction_type']; ?></td>
        <td><?php echo $row['quantity']; ?></td>
        <td><?php echo "$" . number_format($row['price_per_share'], 2); ?></td>
        <td><?php echo $row['buy_sell_date']; ?></td>
    </tr>
    <?php } ?>
</table>
