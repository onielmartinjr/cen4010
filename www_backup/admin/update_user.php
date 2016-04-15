<?php

	//require the framework
	require_once "../requires/initialize.php";
		
	// create the page
	$page = new Page();
	$page->name = "Update User";
	$page->is_admin_only = true;
	
	// check if user_wk is set
	if (!isset($_GET["user_wk"])) 
	{
		$session->message("There is an error with the user you were trying to view.");
		redirect_head(ROOT_URL."admin/search_users.php");
	}
	
	
	//get the object info
	$the_user = User::find_by_id($_GET["user_wk"]);	

	
	// check that the object exists
	if (!$the_user) 
	{
		$session->message("There is an error with the user you were trying to view.");
		redirect_head(ROOT_URL."admin/search_users.php");
	}
	
	//only process the form if it's submitted
	if(isset($_POST["submit"])) 
	{		
		
		//assign variables to all form-submitted values	
		$email_address = $_POST['email_address'];
		$role_wk = $_POST['role_wk'];
		$hashed_password = sha1($database->escape_value($_POST['password']));
		$confirmed_password = sha1($database->escape_value($_POST['confirmed_password']));
		$first_name = $_POST['first_name'];
		$last_name = $_POST['last_name'];
		$phone_number = return_numeric($_POST['phone_number']);
		$is_notifications_enabled = $_POST['email_notifications'];
		$is_deleted = $_POST['is_deleted'];
	
		// validations
		
		//make sure passwords (first and confirmed) are the same
		if($hashed_password != $confirmed_password) {
			$session->message($session->message."The passwords you entered do not match. ");
		}
		
		//make sure the email address is not already taken
		if ($the_user->email_address != $email_address) 
		{
			if (User::find_by_name($database->escape_value($email_address), "email_address")) 
			{
				$session->message($session->message."That email address is already taken, please enter a new email address. ");
				$email_address = $the_user->email_address;
			}
		}
		
		//only save the user if there are no errors
		if(empty($session->message)) {
			$the_user->email_address = $email_address;
			$the_user->role_wk = $role_wk;
			//only change the password if it's not empty
			if(!empty($_POST['password']))
				$the_user->hashed_password = $hashed_password;
			$the_user->first_name = $first_name;
			$the_user->last_name = $last_name;
			$the_user->phone_number = $phone_number;
			$the_user->is_notifications_enabled = $is_notifications_enabled;
			$the_user->is_deleted = $is_deleted;
			if($the_user->save()) 
				$session->message("The user was updated successfully!");
			else 
				$session->message("The user was not updated.");
			
			redirect_head(ROOT_URL."admin/".file_name_with_get());
			die();
		}
		
	}
	
	//header template
	require_once ("../requires/template/header.php");

?>
	
	<!-- update user form -->
	<form id="update_user" action="<?php echo file_name_with_get(); ?>" method="post">
		email address: <input type="text" name="email_address" value="<?php echo $the_user->email_address; ?>" /> <br />
		role: <select name="role_wk">
					<?php
						
						//get all the roles
						$roles = Role::find_all();
						foreach($roles AS $value) {
							echo "<option value=\"".$value->role_wk."\"";
							
							//echo this if this item is selected
							if($the_user->role_wk == $value->role_wk)
								echo " selected";
							
							echo ">".$value->name."</option>";
						}
					
					?>
			  </select><br />
		password: <input type="password" name="password" value=""/> <br />
		confirm password: <input type="password" name="confirmed_password" value=""/> <br />
		first name: <input type="text" name="first_name" value="<?php echo $the_user->first_name; ?>" /> <br />
		last name: <input type="text" name="last_name" value="<?php echo $the_user->last_name; ?>" /> <br />
		phone number: <input type="text" name="phone_number" value="<?php echo $the_user->phone_number; ?>" /> <br />
		receive email notifications: <input type="radio" name="email_notifications" <?php if($the_user->is_notifications_enabled == "0") echo "checked"; ?>  value="0">No
	    <input type="radio" name="email_notifications" <?php if($the_user->is_notifications_enabled == "1") echo "checked"; ?>  value="1">Yes<br />
	    disable: <input type="radio" name="is_deleted" <?php if($the_user->is_deleted == "0") echo "checked"; ?>  value="0">No
	    <input type="radio" name="is_deleted" <?php if($the_user->is_deleted == "1") echo "checked"; ?>  value="1">Yes<br />
		<input type="submit" value="submit" name="submit"/>
	</form>

<?php
	
	//this is a special instance, remove the message, if it's set, since we set the messages in this form
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>