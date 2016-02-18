<?php

	/*
		Defines the Reset_Password class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Reset_Password extends Database_Object {
	
	protected static $table_name = 'reset_password';
	protected static $db_fields = array('reset_password_wk', 'user_wk', 'random_key', 'create_dt', 'is_reset');
	
	public $reset_password_wk;
	public $user_wk;
	public $random_key;
	public $create_dt;
	public $is_reset;
	
}

?>