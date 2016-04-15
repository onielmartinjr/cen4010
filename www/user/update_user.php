<?php

	//require the framework
	require_once "../requires/initialize.php";
		
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
			
			redirect_head(ROOT_URL."user/update_user.php");
			die();
		}
	}
	
	//header template
	require_once ("../requires/template/header.php");

?>
	
	<!-- update user form -->
	<section id="registration" class="container"><form class="center" role="form" id="update_user" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		<h2>Update User</h2>
		email address: <br><div class="form-group"><input type="text" name="email_address" class="form-control" value="<?php echo $user->email_address; ?>" /> </div>
		password: <br><div class="form-group"><input type="password" name="password" class="form-control" value=""/> </div>
		confirm password: <br><div class="form-group"><input type="password" name="confirmed_password" class="form-control" value=""/></div>
		first name: <br><div class="form-group"><input type="text" name="first_name" class="form-control" value="<?php echo $user->first_name; ?>" /></div>
		last name: <br><div class="form-group"><input type="text" name="last_name" class="form-control" value="<?php echo $user->last_name; ?>" /> </div>
		phone number: <br><div class="form-group"><input type="text" name="phone_number" class="form-control" value="<?php echo $user->phone_number; ?>" /> </div>
		receive email notifications: <br><div class="form-group"><input type="radio" name="email_notifications" <?php if($user->is_notifications_enabled == "0") echo "checked"; ?>  value="0">&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;
	    <input type="radio" name="email_notifications" <?php if($user->is_notifications_enabled == "1") echo "checked"; ?>  value="1">&nbsp;&nbsp;Yes</div>
		
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section>

<?php
	
	//this is a special instance, remove the message, if it's set, since we set the messages in this form
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>