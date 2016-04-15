<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Delete User";
	$page->is_user_only = true;
	
	// check if user wants to delete their account
	if (isset($_POST["confirm"]))
	{
		if (in_array($user->role_wk, array("2", "3"))) // if logged in as ADMIN or STAFF
		{
			// if user is the last admin or staff, cannot delete account
			$user_array = User::find_by_sql("SELECT * FROM `user` WHERE `role_wk` = " . $user->role_wk . " AND `is_deleted` = 0;"); // find all of the ADMINs
			if (count($user_array) <= 1) // if last ADMIN of last STAFF account...
			{
				$session->message("You are the last " . $user->role_wk->name . "! Another " . $user->role_wk->name . " account must be created before this one can be deleted.");
				redirect_head(ROOT_URL);
			}
		}
		
		// delete the user
		$user->delete();
		$session->message("Your account has been deleted!");
		$session->logout(true);
		redirect_head(ROOT_URL . "index.php");
		die();
	}
	elseif (isset($_POST["deny"]))
	{
		$session->message("Your account was not deleted.");
		redirect_head(ROOT_URL);
	}
	
	//header template
	require_once ("../requires/template/header.php");
	
?>	
	<section class="container"><form class="center" role="form"  id="confirm_delete" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		<label>Are you sure you want to delete your account?</label> <br>
		<input type="submit" value="No, keep my account!" class="btn btn-success btn-md btn-block" name="deny" />
		<input type="submit" value="Yes, delete my account" class="btn btn-success btn-md btn-block" name="confirm" />
	</fieldset></form></section>
	
<?php
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>