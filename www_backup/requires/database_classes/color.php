<?php

	/*
		Defines the Color class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Color extends Database_Object {
	
	protected static $table_name = 'color';
	protected static $db_fields = array('color_wk', 'name', 'create_dt');
	
	public $color_wk;
	public $name;
	public $create_dt;
	
}

?>