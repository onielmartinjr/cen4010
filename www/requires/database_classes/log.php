<?php

	/*
		Defines the Log class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Log extends Database_Object {
	
	protected static $table_name = 'log';
	protected static $db_fields = array('log_wk', 'user_wk', 'url', 'ip', 'create_dt');
	
	public $log_wk;
	public $user_wk;
	public $url;
	public $ip;
	public $create_dt;
	
}

?>