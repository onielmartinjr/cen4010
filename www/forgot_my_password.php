<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Forgot my Password";
	
	// check to see if a user is already logged in
	if ($session->is_logged_in) 
	{
		$session->message("You are already logged in! To use the <b>Forgot my Password</b> feature, please logout first.");
		redirect_head(ROOT_URL);
	}
	
	// the user submitted the form
	if(isset($_POST["submit"])) 
	{ 
		$found_user = User::find_by_name($database->escape_value($_POST['email_address']), 'email_address');
		
		if($found_user) {
			//the e-mail address was found
			
			//now we need to make sure it does not belong to an account that is deleted
			if($found_user->is_deleted == '1')
				$session->message("The account associated to that Email Address is disabled.");
					
		} else 
			//the e-mail address is not associated with an account
			$session->message("The e-mail address you entered does not belong to an account.");
			
		
		//only execute here if there was an account found, AND it is not soft-deleted
		if(empty($session->message())) {
			$new_request = new Reset_Password($found_user->user_wk);
			//create the record
			$new_request->save();
			
			//send e-mail here
			
			
			//redirect
			$session->message("Success! Please check your e-mail for instructions on how to reset your password.");
			redirect_head(ROOT_URL."forgot_my_password.php");
			die();
		}
	}
	
	// header
	require_once "requires/template/header.php";
		
	?>
	
	<!-- form -->
	<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		<p>Please enter the Email Address associated with your account.</p>
		<input type="text" name="email_address" value="<?php if(isset($_POST['submit'])) echo $_POST['email_address']; ?>"/> <br />
		<input type="submit" value="submit" name="submit"/>
	</form>
	
<?php

	//this is a special instance, remove the message, if it's set, since we set the messages in this form
	$session->remove_message();

	//footer
	require_once "requires/template/footer.php";

?>