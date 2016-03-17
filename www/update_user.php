<?php

	//require the framework
	require_once "requires/initialize.php";
		
	// create the page
	$page = new Page();
	$page->name = "Update Account";
	$page->is_user_only = true;
	
	//only process the form if it's submitted
	if(isset($_POST["submit"])) 
	{		
		//assign variables to all form-submitted values	
		$email_address = $_POST['email_address'];
		$hashed_password = sha1($database->escape_value($_POST['password']));
		$confirmed_password = sha1($database->escape_value($_POST['confirmed_password']));
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$phone_number = return_numeric($_POST['phone_number']);
		$is_notifications_enabled = $_POST['email_notifications'];
	
		// validations
		
		//make sure passwords (first and confirmed) are the same
		if($hashed_password != $confirmed_password) {
			$session->message($session->message."The passwords you entered do not match. ");
		}
		
		//make sure the email address is not already taken
		if ($user->email_address != $email_address) 
		{
			if (User::find_by_name($database->escape_value($email_address), "email_address")) 
			{
				$session->message($session->message."That email address is already taken, please enter a new email address. ");
				$email_address = $user->email_address;
			}
		}
		
		//only actually create the user if there are no errors
		if(empty($session->message)) {
			$user->email_address = $email_address;
			//only change the password if it's not empty
			if(!empty($_POST['password']))
				$user->hashed_password = $hashed_password;
			$user->first_name = $first_name;
			$user->last_name = $last_name;
			$user->phone_number = $phone_number;
			$user->is_notifications_enabled = $is_notifications_enabled;
			if($user->save()) 
				$session->message("Your account was updated successfully!");
			else 
				$session->message("Your account was not updated.");
			
			redirect_head(ROOT_URL."update_user.php");
			die();
		}
	}
	
	// header
	require_once "requires/template/header.php";

?>
	
	<!-- update user form -->
	<form id="update_user" action="<?php echo file_name_with_get(); ?>" method="post">
		email address: <input type="text" name="email_address" value="<?php echo $user->email_address; ?>" /> <br />
		password: <input type="password" name="password" value=""/> <br />
		confirm password: <input type="password" name="confirmed_password" value=""/> <br />
		first name: <input type="text" name="first_name" value="<?php echo $user->first_name; ?>" /> <br />
		last name: <input type="text" name="last_name" value="<?php echo $user->last_name; ?>" /> <br />
		phone number: <input type="text" name="phone_number" value="<?php echo $user->phone_number; ?>" /> <br />
		receive email notifications: <input type="radio" name="email_notifications" <?php if($user->is_notifications_enabled == "0") echo "checked"; ?>  value="0">No
	    <input type="radio" name="email_notifications" <?php if($user->is_notifications_enabled == "1") echo "checked"; ?>  value="1">Yes<br />
		<input type="submit" value="submit" name="submit"/>
	</form>

<?php
	
	//this is a special instance, remove the message, if it's set, since we set the messages in this form
	$session->remove_message();
	
	//footer
	require_once "requires/template/footer.php";
	
?>