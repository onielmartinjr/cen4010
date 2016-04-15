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
	<section id="registration" class="container"><form class="center" role="form" action="<?php echo current_url(); ?>" method="post" ><fieldset class="registration-form">
		<div class="form-group has-feedback"><input type="text" name="username" placeholder="Username" class="form-control has-error has-success" value="<?php if(isset($_GET['username'])) echo $_GET['username']; ?>" /> 
		   <span class="form-control-feedback fui-check"></span>
		</div>
		<div class="form-group has-feedback"><input type="password" name="password" placeholder="Password" class="form-control has-error has-success" />
			  <span class="form-control-feedback fui-check"></span>
		</div>
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section><!--/#registration-->
	<div id="row" class="center">
		<p>No account? <a href="create_new_user.php">Create a new account!</a></p>
		<p><a href="forgot_my_password.php">Forgot my Password</a></p>
	</div>
	<br><br>
	
<?php

	//footer
	require_once "requires/template/footer.php";

?>