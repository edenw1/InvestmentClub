<html>
<head>
<title>Returning User </title>
<link rel="stylesheet" type="text/css" href="formatStyles.css">
</head>
<body>
<div>
 <h1>User Login</h1>
    <form action="doform2.php" method="POST">
        Username: <input type="text" name="username"><br>
        Password: <input type="password" name="password"><br>
        <input type="submit" value="Submit">
    </form>
	
	
	<br>
Are you a new user? 
<br>
<form action="register.php" method="post">
	<input type="submit" value="Register">
</form>
<form action="index.php" method="post">
	<input type="submit" value="Continue as Guest">
	</form>
</div> 
</body>
</html>