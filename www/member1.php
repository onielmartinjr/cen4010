<?php

	//require the framework
	require_once "requires/initialize.php";
	
	$page_var = new Page();
	$page_var->name = "Member1";
	$page_var->body = "This is the body";
	$page_var->is_user_only = 1;
	
	
	$session->message("ERROR: Go fuck yourself ;)");
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
	</nav> <br />
	
	<?php echo $page_var->body;?>
	

</body>
</html>

<?php

	//close connection
	$database->close_connection();

?>