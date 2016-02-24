<?php

	//require the framework
	require_once "requires/initialize.php";
	
	$page_var = new Page();
	$page_var->name = "Login";
	
	if(isset($_POST["submit"])) 
	{ 
		$username = $_POST["username"];
		$password = $_POST["password"];

		User::login($username, $password);
	}
	
?>
<!DOCTYPE html>
<html>
<head>

	<title><?php echo $page_var->name;?></title>

</head>
<body>

	<h1><?php echo $page_var->name;?></h1>
	
	<nav>
		<a href="index.php">Homepage</a><br />
		<a href="login.php">Login Here!</a><br />
		<a href="public1.php">Public 1</a><br />
		<a href="member1.php">Member 1</a><br />
		<a href="admin1.php">Admin 1</a><br />
	</nav> 

	<br />
	
	<?php display_error(); ?>
	
	<form id="login" action="login.php" method="post">
		username<input type="text" name="username" /> <br />
		password<input type="password" name="password" /> <br />
		<input type="submit" value="submit" name="submit"/>
		
	</form>

</body>
</html>

<?php

	//close connection
	$database->close_connection();

?>