<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Reset my Password";
	
	// check to see if a user is already logged in
	if ($session->is_logged_in) 
	{
		$session->message("You are already logged in! To use the Reset my Password feature, please logout first.");
		redirect_head(ROOT_URL);
	}
	
	//make sure the key is setup as a GET superglobal
	if(!isset($_GET['reset_key'])) {
		$session->message("You have a bad URL, please copy the correct URL.");
		redirect_head(ROOT_URL);
	}
	
	//at this point, we know there is a key set
	//now we need to make sure the key exists
	$the_key = Reset_Password::find_by_name($_GET['reset_key'], 'random_key');
	if(!$the_key) {
		$session->message("You have a bad URL, please copy the correct URL.");
		redirect_head(ROOT_URL);
	}
	
	//at this point, we now know that there is a key entered
	//also, we now know that the key actually exists
	//so now, we need to do the following checks
	//1. Make sure that the request entered does not belong to a user who's deleted.
	//2. Make sure that the request entered is the latest request for that user.
	//3. Make sure that the request entered has not already been used.
	//4. Make sure that the request entered is less than 24 hours old.
	
	//check #1
	if($the_key->user_wk->is_deleted == '1') {
		$session->message("You cannot reset a password for a disabled account.");
		redirect_head(ROOT_URL);
	}
	
	//check #2
	$result = $database->fetch_array($database->query("SELECT MAX(reset_password_wk) AS reset_password_wk FROM `reset_password` WHERE `user_wk` = ".$the_key->user_wk->user_wk.";"))[0];
	if($result != $the_key->reset_password_wk) {
		$session->message("This is not the latest reset password request; please make sure to click on the latest request.");
		redirect_head(ROOT_URL);
	}
	
	//check #3
	if($the_key->is_reset == '1') {
		$session->message("This reset password request has already been used. Please request a new password.");
		redirect_head(ROOT_URL."forgot_my_password.php");
	}
	
	//check #4
	if((time() - strtotime($the_key->create_dt)) >= 86400) {
		$session->message("This reset password request is older than 24 hours. Please request a new password.");
		redirect_head(ROOT_URL."forgot_my_password.php");
	}
	
	/////////////////////////////////////////////
	//if we are here, then the user is fully authenticated
	//and is completely allowed to be on this page
	//we can resume with normal processing
	
	//if the form is submitted
	if(isset($_POST['submit'])) {
		
		$password = sha1($database->escape_value($_POST['password']));
		$hashed_password = sha1($database->escape_value($_POST['confirmed_password']));
		
		//first thing's first, we need to make sure the passwords match
		if($password != $hashed_password) {
			$session->message("The passwords you entered do not match.");
			redirect_head(ROOT_URL.file_name_with_get());
		}
		
		//if we're here, then the password do match
		//we can successfully update the user's e-mail address
		
		//set the new password
		$the_key->user_wk->hashed_password = $password;
		
		if(!$the_key->user_wk->save()) {
			//there was an error
			$session->message("There was an error in your request, please try again.");
			redirect_head(ROOT_URL.file_name_with_get());
		} else {
			//success
			
			//so now we need to set this reset password request to complete
			$the_key->is_reset = 1;
			$the_key->save();
			
			//redirect
			$session->message("Your password was successfully reset!");
			redirect_head(ROOT_URL."login.php");
		}
		
	}
	
	
	// header
	require_once "requires/template/header.php";
		
?>
	<!-- form -->
	<section id="registration" class="container"><form class="center" role="form" action="<?php echo file_name_with_get(); ?>" method="post"><fieldset class="registration-form">
		<p>Your username is: <strong><?php echo $the_key->user_wk->username; ?></strong></p>
		<br /><p>Please enter and confirm your new password.</p>
		<div class="form-group has-feedback"><input type="password" name="password" value="" placeholder="Password" class="form-control has-error has-success" /></div>
		<div class="form-group has-feedback"><input type="password" name="confirmed_password" value="" placeholder="Retype Password" class="form-control has-error has-success" /> </div>
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section><!--/#registration-->
	
<?php

	//footer
	require_once "requires/template/footer.php";

?>