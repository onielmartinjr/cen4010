<?php

	/*
		Defines the Setting class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Setting extends Database_Object {
	
	protected static $table_name = 'setting';
	protected static $db_fields = array('setting_wk', 'variable_name', 'variable_value', 'create_dt');
	
	public $setting_wk;
	public $variable_name;
	public $variable_value;
	public $create_dt;
	
}

?>