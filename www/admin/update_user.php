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
		
		// if user is the last admin or staff, cannot delete account
		if($is_deleted == '1' && $the_user->is_deleted == '0')
		{
			//only do this check if we're disabling an account
			$user_array = User::find_by_sql("SELECT * FROM `user` WHERE `role_wk` = " . $the_user->role_wk . " AND `is_deleted` = 0;"); // find all of the ADMINs
			if (count($user_array) <= 1) // if last ADMIN of last STAFF account...
			{
				$session->message("You are the last " . $user->role_wk->name . "!<br />Another " . $user->role_wk->name . " account must be created before this one can be disabled.");
				redirect_head("search_users.php");
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
	<section id="registration" class="container"><form class="center" role="form" id="update_user" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		Email address: <br><div class="form-group"><input type="text" class="form-control" name="email_address" value="<?php echo $the_user->email_address; ?>" /> </div>
		Role: <br><div class="form-group"><select class="form-control" name="role_wk">
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
			  </select></div>
		Password: <br><div class="form-group"><input type="password" class="form-control" name="password" value=""/> </div>
		Confirm password: <br><div class="form-group"><input type="password" class="form-control" name="confirmed_password" value=""/> </div>
		First name: <br><div class="form-group"><input type="text" class="form-control" name="first_name" value="<?php echo $the_user->first_name; ?>" /></div>
		Last name: <br><div class="form-group"><input type="text" class="form-control" name="last_name" value="<?php echo $the_user->last_name; ?>" /> </div>
		Phone number: <br><div class="form-group"><input type="text" class="form-control" name="phone_number" value="<?php echo $the_user->phone_number; ?>" /> </div>
		Receive email notifications: <br><div class="form-group"><input type="radio" class="custom-radio" name="email_notifications" <?php if($the_user->is_notifications_enabled == "0") echo "checked"; ?>  value="0">&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;
	    <input type="radio" class="custom-radio" name="email_notifications" <?php if($the_user->is_notifications_enabled == "1") echo "checked"; ?>  value="1">&nbsp;&nbsp;Yes</div>
	    Disable: <br><div class="form-group"><input type="radio" class="custom-radio" name="is_deleted" <?php if($the_user->is_deleted == "0") echo "checked"; ?>  value="0">&nbsp;&nbsp;No&nbsp;&nbsp;&nbsp;&nbsp;
	    <input type="radio" class="custom-radio" name="is_deleted" <?php if($the_user->is_deleted == "1") echo "checked"; ?>  value="1">&nbsp;&nbsp; Yes</div>
		
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section>

<?php
	
	//this is a special instance, remove the message, if it's set, since we set the messages in this form
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>