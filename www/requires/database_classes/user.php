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
				$session->message($user->username.", your account has been disabled. If you feel this is an error please contact the administrator.");
				redirect_head(ROOT_URL."login.php");
				return false;
			} else
				//successfully logged in
				$session->message("Successfully logged in!");
				$session->login($user);
				redirect_head(ROOT_URL);
				return $user;
		}
		$session->message("The username and password combination does not exist.");
		redirect_head(ROOT_URL."login.php");
		return false;
	}
	
}

?>