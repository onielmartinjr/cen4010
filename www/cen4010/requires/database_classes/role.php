<?php

	/*
		Defines the Role class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Role extends Database_Object {
	
	protected static $table_name = 'role';
	protected static $db_fields = array('role_wk', 'name', 'create_dt');
	
	public $role_wk;
	public $name;
	public $create_dt;
	
}

?>