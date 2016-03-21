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
		User::login($_POST["username"], $_POST["password"]);
	}
	
	// header
	require_once "requires/template/header.php";
		
?>
	
	<!-- form -->
	<form action="<?php echo file_name_with_get(); ?>" method="post">
		username: <input type="text" name="username" /> <br />
		password: <input type="password" name="password" /> <br />
		<input type="submit" value="submit" name="submit"/>
	</form>
	<p>No account? <a href="create_new_user.php">Create a new account!</a></p>
	<p><a href="forgot_my_password.php">Forgot my Password</a></p>
	
<?php

	//footer
	require_once "requires/template/footer.php";

?>