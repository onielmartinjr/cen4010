<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Login";
	
	// check to see if a user is already logged in
	if ($session->is_logged_in) 
	{
		$session->message("You are already logged in! To change users please logout first.");
		redirect_head(ROOT_URL);
	}
	
	// log the user in
	if(isset($_POST["submit"])) 
	{ 
		$username = $_POST["username"];
		$password = $_POST["password"];

		User::login($username, $password);
	}
	
	// header
	require_once "requires/template/header.php";
		
	?>
	
	<!-- login form -->
	<form id="login" action="login.php" method="post">
		username<input type="text" name="username" /> <br />
		password<input type="password" name="password" /> <br />
		<input type="submit" value="submit" name="submit"/>
	</form>
	
<?php

	//footer
	require_once "requires/template/footer.php";

?>