<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Create New User";
	
	// check to see if a user is already logged in
	if ($session->is_logged_in) 
	{
		$session->message("You are already logged in! To create a new account, please logout first.");
		redirect_head(ROOT_URL);
	}
	
	// if the form is submitted, attempt to create their new user account
	if(isset($_POST["submit"])) 
	{
		$new_user = new User();
		
		$new_user->username = $_POST['username'];
		$new_user->email_address = $_POST['email_address'];
		$new_user->hashed_password = sha1($database->escape_value($_POST['password']));
		$confirmed_password = sha1($database->escape_value($_POST['confirmed_password']));
		$new_user->first_name = $_POST['first_name'];
		$new_user->last_name = $_POST['last_name'];
		$new_user->phone_number = return_numeric($_POST['phone_number']);
		$new_user->is_notifications_enabled = $_POST['email_notifications'];
		
		//make sure the username does not already exist
		if(User::find_by_name($database->escape_value($new_user->username), "username")) {
			$session->message("That username is already taken, please enter a new username. ");
			$new_user->username = "";
		}
		
		//make sure the email address is not already taken
		if(User::find_by_name($database->escape_value($new_user->email_address), "email_address")) {
			$session->message($session->message."That email address is already taken, please enter a new email address. ");
			$new_user->email_address = "";
		}
		
		//make sure passwords are the same
		if($new_user->hashed_password != $confirmed_password) {
			$session->message($session->message."The passwords you entered do not match.");
		}
		
		//make sure password isn't blank
		if($_POST['password'] == "") {
			$session->message($session->message."Your password cannot be blank. ");
		}
		
		//make sure username isn't blank
		if($_POST['username'] == "") {
			$session->message($session->message."Your username cannot be blank. ");
		}
		
		//make sure email_adsress isn't blank
		if($_POST['email_address'] == "") {
			$session->message($session->message."Your email address cannot be blank. ");
		}
		
		//only actually create the user if there are no errors
		if($session->message == "") {
			if($new_user->save()) {
				$session->message("Your account was created successfully!");
				redirect_head(ROOT_URL."login.php");
			} else {
				$session->message("Your account was not created successfully.");
			}
		}
	}
	
	// header
	require_once "requires/template/header.php";
		
	?>
	
	<!-- create new user form -->
	<section id="registration" class="container"><form class="center" role="form" id="create_new_user" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form x-md-12">
		<h3>Create New User</h3>
		username: <br><div class="form-group"> <input type="text" class="form-control"name="username" value="<?php if(isset($new_user)) echo $new_user->username; ?>" /> </div>
		email address: <br><div class="form-group"> <input type="text" class="form-control"name="email_address" value="<?php if(isset($new_user)) echo $new_user->email_address; ?>" /> </div>
		password: <br><div class="form-group"><input type="password" name="password" class="form-control"/> </div>
		confirm password: <br><div class="form-group"> <input type="password" name="confirmed_password" class="form-control"/> </div>
		first name: <br><div class="form-group"> <input type="text" name="first_name" class="form-control" value="<?php if(isset($new_user)) echo $new_user->first_name; ?>" /></div>
		last name: <br><div class="form-group"> <input type="text" name="last_name" class="form-control" value="<?php if(isset($new_user)) echo $new_user->last_name; ?>" /> </div>
		phone number: <br><div class="form-group"> <input type="text" name="phone_number" class="form-control" value="<?php if(isset($new_user)) echo $new_user->phone_number; ?>" /> </div>
		receive email notifications: <br><input type="radio" name="email_notifications" value="0"<?php if(isset($new_user)) { if($new_user->is_notifications_enabled == "0") echo " checked"; } ?>>&nbsp;No &nbsp;&nbsp;
			<input type="radio" name="email_notifications" value="1"<?php if(isset($new_user)) { if($new_user->is_notifications_enabled == "1") echo " checked"; } else echo " checked"; ?>>&nbsp;Yes <br>
		<br><div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section><!--/#registration-->
	
<?php

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable('message');

	//footer
	require_once "requires/template/footer.php";

?>