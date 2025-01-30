<?php
session_start();
require 'db.php';
dbConnect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = $_POST['title'];
    $url = $_POST['url'];
    $user_id = $_SESSION['user_id'];
    $addPresentation = "INSERT INTO presentation (user_id, title, file_path, date) VALUES (?, ?, ?, NOW())";
    $stmt = $pdo->prepare($addPresentation);
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $title, PDO::PARAM_STR);
    $stmt->bindValue(3, $url, PDO::PARAM_STR);
    $stmt->execute();
    $presentation_id = $pdo->lastInsertId();

    if (isset($_POST['stocks'])) {
        foreach ($_POST['stocks'] as $stock) {
            $symbol = $stock['symbol'];
            $name = $stock['name'];

            if (!empty($symbol) && !empty($name)) {
                $addStockProposal = "INSERT INTO stockProposal (presentation_id, stock_symbol, stock_name, proposed_by) VALUES (?, ?, ?, ?)";
                $stmt = $pdo->prepare($addStockProposal);
                $stmt->bindValue(1, $presentation_id, PDO::PARAM_INT);
                $stmt->bindValue(2, $symbol, PDO::PARAM_STR);
                $stmt->bindValue(3, $name, PDO::PARAM_STR);
                $stmt->bindValue(4, $user_id, PDO::PARAM_INT);
                $stmt->execute();
            }
        }
    }

    echo "Presentation and stock proposals submitted successfully!";
    ?>
    <form action="index.php" method="post">
	<input type="submit" value="Back to Home">
	</form>
    <?php
} else {
    echo "Invalid request.";
}
?>
