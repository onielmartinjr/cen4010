<?php

	//require the framework
	require_once "requires/initialize.php";
		
	// create the page
	$page = new Page();
	$page->name = "Update Account";
	$page->is_user_only = true;
	
	if(isset($_POST["submit"])) 
	{			
		$email_address = $_POST['email_address'];
		if ($_POST["password"] != "")
			$hashed_password = sha1($database->escape_value($_POST['password']));
		else
			$hashed_password = $user->hashed_password;
		if ($_POST["confirmed_password"] != "")
			$confirmed_password = sha1($database->escape_value($_POST['confirmed_password']));
		else
			$confirmed_password = $user->hashed_password;
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$phone_number = return_numeric($_POST['phone_number']);
		$is_notifications_enabled = $_POST['email_notifications'];
	
	
		// validations
		
		//make sure the email address is not already taken
		if ($user->email_address != $email_address) 
		{
			if (User::find_by_name($database->escape_value($email_address), "email_address")) 
			{
				$session->message($session->message."That email address is already taken, please enter a new email address. ");
				$email_address = $user->email_address;
			}
		}
		
		//make sure passwords are the same
		if ($user->hashed_password != $hashed_password)
		{
			if($hashed_password != $confirmed_password) {
				$session->message($session->message."The passwords you entered do not match.");
			}
		}
		
		//only actually create the user if there are no errors
		if($session->message == "") {
			$user->email_address = $email_address;
			$user->hashed_password = $hashed_password;
			$user->first_name = $first_name;
			$user->last_name = $last_name;
			$user->phone_number = $phone_number;
			$user->is_notifications_enabled = $is_notifications_enabled;
			if($user->save()) {
				$session->message("Your account was updated successfully!");
				redirect_head(ROOT_URL."update_user.php");
				die();
			} 
			else 
			{
				$session->message("Your account was not updated successfully.");
			}
		}
	}
	
	// header
	require_once "requires/template/header.php";

?>
	
	<!-- update user form -->
	<form id="update_user" action="update_user.php" method="post">
		email address: <input type="text" name="email_address" value="<?php echo $user->email_address; ?>" /> <br />
		password: <input type="password" name="password" value=""/> <br />
		confirm password: <input type="password" name="confirmed_password" value=""/> <br />
		first name: <input type="text" name="first_name" value="<?php echo $user->first_name; ?>" /> <br />
		last name: <input type="text" name="last_name" value="<?php echo $user->last_name; ?>" /> <br />
		phone number: <input type="text" name="phone_number" value="<?php echo $user->phone_number; ?>" /> <br />
		receive email notifications: <input type="radio" name="email_notifications" value="0"<?php if($user->is_notifications_enabled == "0") echo " checked"; ?>>No
		<input type="radio" name="email_notifications" value="1"<?php if($user->is_notifications_enabled == "1") echo " checked"; ?>>Yes<br>
		<input type="submit" value="submit" name="submit"/>
	</form>

<?php
	
	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->remove_message();
	
	//footer
	require_once "requires/template/footer.php";
	
?>