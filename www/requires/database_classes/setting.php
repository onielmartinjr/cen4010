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
	
	//function to return variable_value by variable_name
	public static function find_by_variable_name($variable_name) {
		global $database;
		
		$variable_name = $database->escape_value($variable_name);
		$sql = "SELECT `".static::$table_name."`.* FROM `".
			static::$table_name."` WHERE `variable_name`='{$variable_name}' LIMIT 1;";
		$object = array_shift(self::find_by_sql($sql));
		
		//if we found a setting, return the variable value
		if(isset($object))
			return $object->variable_value;
		else {
			return false;
		}
	}	
}

//get all the website settings
//flatten into an associated array
//where the keys are the indexes
$temp_website_settings = Setting::find_all();
$website_settings = array();
foreach($temp_website_settings AS $value) {
	$website_settings[$value->variable_name] =  $value->variable_value;
}

//set default timezone
if(isset($website_settings['time_zone'])) 
	date_default_timezone_set($website_settings['time_zone']);
	
?>