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
	
}

?>