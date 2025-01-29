<?php
session_start();
require 'db.php';
dbConnect();
?>

<html>
<head>
    <title> Presentations </title>
</head>
<h1> Presentations </h1>
<br><br>
<form action="index.php" method="post">
	<input type="submit" value="Back to Home">
	</form>
    <br>
<body>