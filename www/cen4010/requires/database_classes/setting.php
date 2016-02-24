<?php

	/*
		Defines the Setting class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
		
		
		*****IMPORTANT*****
		-Whenever we code new settings, we NEED to make sure we define the default values.
			-This is in case the user decided not to set any value, we need to have an override.
		-The default values are stored in a static array called $default_values.
	*/

class Setting extends Database_Object {
	
	protected static $table_name = 'setting';
	protected static $db_fields = array('setting_wk', 'variable_name', 'variable_value', 'create_dt');
	
	public $setting_wk;
	public $variable_name;
	public $variable_value;
	public $create_dt;
	
	//define the default variable_values
	public static $default_values = array('site_name' => 'Default Thing', 'time_zone' => 'US/Eastern');
	
	//function to return variable_value by variable_name
	public static function find_by_variable_name($variable_name) {
		global $database;
		
		//first we check to make sure there is a default value for the item being passed
		//if there's not, return false
		if(!array_key_exists($variable_name, self::$default_values))
			return false;
		
		$variable_name = $database->escape_value($variable_name);
		$sql = "SELECT `".static::$table_name."`.* FROM `".
			static::$table_name."` WHERE `variable_name`='{$variable_name}' LIMIT 1;";
		$object = array_shift(self::find_by_sql($sql));
		
		//if we found a setting, return the variable value
		if(isset($object))
			return $object->variable_value;
		else {
			//if we're in here, then we did not find a value
			//in the database - override the output
			//with the pre-defined default value
			
			//return the default
			return self::$default_values[$variable_name];
		}
	}	
}

//get the site name
$site_name = Setting::find_by_variable_name('site_name');

//set default timezone
date_default_timezone_set(Setting::find_by_variable_name('time_zone'));


?>