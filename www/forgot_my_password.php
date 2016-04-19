<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Forgot my Password";
	
	// check to see if a user is already logged in
	if ($session->is_logged_in) 
	{
		$session->message("You are already logged in! To use the Forgot my Password feature, please logout first.");
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
			$new_request = new Reset_Password();
			$new_request->set_new_key();
			$new_request->user_wk = $found_user->user_wk;
			//save the record
			$new_request->save();
			
			//send e-mail here
			//only if we're not in a local environment
			if(!$am_i_local) {
				$to = $found_user->email_address;
				$subject = "Password Reset Request";

				$message = "
				<html>
					<head>
						<title>".$subject."</title>
					</head>
					<body>
						<p>Your username is: <strong>".$found_user->username."</strong></p>
						<p>Please the link below to reset your password. The link will be acive for 24 hours.</p>
						<p><a href=\"".ROOT_URL."reset_my_password.php?reset_key=".$new_request->random_key."\">".ROOT_URL."reset_my_password.php?reset_key=".$new_request->random_key."</a></p>
					</body>
				</html>
				";

				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

				// More headers
				$headers .= 'From: <support@pet_adoption.com>' . "\r\n";
			
				//send out the email
				mail($to,$subject,$message,$headers);
			}
			
			//redirect
			$session->message("Success! Please check your e-mail for instructions on how to reset your password.");
			redirect_head(ROOT_URL."forgot_my_password.php");
		}
	}
	
	// header
	require_once "requires/template/header.php";

?>
	
	<!-- form -->
	<section id="registration" class="container"><form class="center" role="form" action="<?php echo file_name_with_get(); ?>" method="post"><fieldset class="registration-form">
		<p>Please enter the Email Address associated with your account.</p>
		<div class="form-group"><input type="text" name="email_address" class="form-control" value="<?php if(isset($_POST['submit'])) echo $_POST['email_address']; ?>"/> </div>
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section><!--/#registration-->

<?php

	//this is a special instance, remove the message, if it's set, since we set the messages in this form
	$session->unset_variable('message');

	//footer
	require_once "requires/template/footer.php";

?>