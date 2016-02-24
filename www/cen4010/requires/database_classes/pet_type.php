<?php

	/*
		Defines the Pet_Type class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Pet_Type extends Database_Object {
	
	protected static $table_name = 'pet_type';
	protected static $db_fields = array('pet_type_wk', 'name', 'create_dt');
	
	public $pet_type_wk;
	public $name;
	public $create_dt;
	
}

?>