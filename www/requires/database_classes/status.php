<?php

	/*
		Defines the Status class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Status extends Database_Object {
	
	protected static $table_name = 'status';
	protected static $db_fields = array('status_wk', 'name', 'create_dt');
	
	public $status_wk;
	public $name;
	public $create_dt;
	
}

?>