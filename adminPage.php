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
<body>
    <h1>Add New Club Member</h1>
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

    <html>
    <h1>Remove Club Member</h1>
    <form action="removeMemberForm.php" method="post">
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        
        <input type="submit" value="Remove Member"><br>
    </form>
</body>
</html>
