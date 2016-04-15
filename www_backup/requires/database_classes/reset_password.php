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
	
	//set a new random key
	public function set_new_key() {
		//now we're going to set the random key
		$this->random_key = random_key(20);
		
		//before we can set that random key to the object
		//we NEED to make sure it doesn't exist
		while(Reset_Password::is_random_key_being_used($this->random_key)) {
			//while this key does it exist, keep looping through and generating new 
			//random keys until it already exists
			$this->random_key = random_key(20);
		}
	}
	
	//boolean function that returns true or false if key is being used
	public static function is_random_key_being_used($key) {
		global $database;	
	
		$sql = "SELECT COUNT(*) FROM `reset_password` WHERE `random_key` = '{$key}'";
		$result_set = $database->query($sql);
		//return the total # of rows
		$row = $database->fetch_array($result_set);

		//if 1 record was returned, then it is already being used
		if(array_shift($row) == '1')
			return true;
		
		//else, it's not being used
		return false;
	}
	
}

?>