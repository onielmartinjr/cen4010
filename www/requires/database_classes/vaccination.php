<?php

	/*
		Defines the Vaccination class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Vaccination extends Database_Object {
	
	protected static $table_name = 'vaccination';
	protected static $db_fields = array('vaccination_wk', 'vaccination_name', 'create_dt');
	
	public $vaccination_wk;
	public $vaccination_name;
	public $create_dt;

	
}

?>