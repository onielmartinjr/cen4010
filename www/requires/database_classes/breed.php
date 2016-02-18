<?php

	/*
		Defines the Breed class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Breed extends Database_Object {
	
	protected static $table_name = 'breed';
	protected static $db_fields = array('breed_wk', 'pet_type_wk', 'name', 'create_dt');
	
	public $breed_wk;
	public $pet_type_wk;
	public $name;
	public $create_dt;
	
}

?>