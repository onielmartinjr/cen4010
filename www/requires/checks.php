<?php
	/*
		This page will do all the checks that we want to implement.
		For example: if someone is currently logged in & actively navigating the site AND they get
		disabled or removed by an admin - we want the user to get immediately logged out of the site.
		
		This page is included in the initialize page, so it will be called upon EVERY page load.	
	*/
	
		
	//This check will log a user out and redirect them if they are currently
	//logged in and that user's is_deleted status changes to 1.
	if(isset($user) && $session->is_logged_in)
	{
		 if($user->is_deleted == 1) 
		{
			$session->logout();
			$session->message("Your account was disabled and you have been redirected to the homepage.");
			redirect_head(ROOT_URL);
		}
	}
	
	
	//create a log entry for ever page a user visits
	$log = new Log();
	//only set a user_wk if it's in the system
	if(isset($user))
		$log->user_wk = $user;
	$log->url = current_url();
	$log->ip = users_current_ip();
	$log->save();
	
?>