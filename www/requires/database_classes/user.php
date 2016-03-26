<?php

	/*
		Defines the User class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class User extends Database_Object {
	
	protected static $table_name = 'user';
	protected static $db_fields = array('user_wk', 'role_wk', 'username', 'email_address', 'hashed_password', 
										'first_name', 'last_name', 'phone_number', 'is_notifications_enabled', 
										'create_dt', 'last_update_dt', 'is_deleted', 'deleted_dt');
	
	public $user_wk;
	public $role_wk;
	public $username;
	public $email_address;
	public $hashed_password;
	public $first_name;
	public $last_name;
	public $phone_number;
	public $is_notifications_enabled;
	public $create_dt;
	public $last_update_dt;
	public $is_deleted;
	public $deleted_dt;
	
	
	//login script
	public static function login($username="", $password="") {
		//will retrieve user credentials if username and password are a match
		//if a match, it will spit out 1 user object
		//if not a match, it will return false
		global $database;
		global $session;
		global $page_file_name_with_get;
		$username = $database->escape_value($username);
		$password = sha1($database->escape_value($password));
	
		$sql  = "SELECT * FROM `".self::$table_name."` ";
		$sql .= "WHERE username = '{$username}' ";
		$sql .= "AND hashed_password = '{$password}' ";
		$sql .= "LIMIT 1;";
		$result_array = self::find_by_sql($sql);
		//if soft deleted, display error message
		if (!empty($result_array)) {
			$user = array_shift($result_array);
			if ($user->is_deleted == 1) {
				//account was found, but is disabled
				$session->message($user->username.", your account has been disabled. If you feel this is an error please contact the administrator.");
				redirect_head(ROOT_URL."login.php?username=".$username);
				return false;
			} else {
				//successfully logged in
				$session->unset_variable('login_attempt');
				$session->message("Successfully logged in!");
				$session->login($user);
				
				//this will determine where we redirect to
				//depending on whether or not there is a $_GET['url'] superglobal set
				if(isset($_GET['url'])) 
					redirect_head($_GET['url']);
				else
					redirect_head(ROOT_URL);
			}
		}
		//the username password combination does not exist
		
		//so now, we need to do a couple of checks for the lockout security
		//1. We need to see if the username exists.
			//If it does, we ned to make a note that this username was incorrectly
			//logged into X number of times
			//Also - if the number of times this account has been logged into is 5 attempts
			//then we need to disable the account and display a relevant error message
		//If it does not exist, then do nothing 
		$try_to_find_user = User::find_by_name($username, "username");
		
		if($try_to_find_user) {
			//the username does exist
			
			//so now we need to determine the # of login attemps, and the account
			if(isset($session->login_attempt)) {
				$login_attempt = $session->login_attempt;
				
				//depending on whether or not the username is the same
				//we can either increment the login attempt number, or
				//we set the default
				if($login_attempt['username'] == $username) {
					$login_attempt['number']++;
					$session->set_variable('login_attempt', $login_attempt);
				} else {
					//there is no previous login attempt
					//set the default
					$login_attempt = array();
					$login_attempt['username'] = $username;
					$login_attempt['number'] = 1;
				
					//save it
					$session->set_variable('login_attempt', $login_attempt);
				}
				
				//if the # of logins = 5, lockout the user account
				if($login_attempt['number'] == 5) {
					$try_to_find_user->is_deleted = 1;
					$try_to_find_user->deleted_dt = current_timestamp();
					$try_to_find_user->save();
					
					$session->message("You have had 5 incorrect login attempets, your account has been locked.</br>Please contact the administrator.");
					$redirect = ROOT_URL."login.php";
					$redirect .= (isset($_GET['url']) ? "?url=".$_GET['url'] : '');
					redirect_head($redirect);
				}
			} else {
				//there is no previous login attempt
				//set the default
				$login_attempt = array();
				$login_attempt['username'] = $username;
				$login_attempt['number'] = 1;
				
				//save it
				$session->set_variable('login_attempt', $login_attempt);
			}
		} else {
			//the username does not exist
			$session->unset_variable('login_attempt');
		}
		$session->message("The username and password combination does not exist.");
		$redirect = ROOT_URL."login.php?username=".$username;
		$redirect .= (isset($_GET['url']) ? "&url=".$_GET['url'] : '');
		redirect_head($redirect);
		return false;
	}
	
}

?>