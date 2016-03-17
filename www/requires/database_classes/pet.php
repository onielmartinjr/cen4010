<?php

	/*
		Defines the Pet class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Pet extends Database_Object {
	
	protected static $table_name = 'pet';
	protected static $db_fields = array('pet_wk', 'breed_wk', 'color_wk', 'status_wk', 'image_wk',
										'name', 'age', 'weight', 'acquired_dt', 'is_rescued', 'last_update_dt',
										'is_deleted', 'create_dt');
	
	public $pet_wk;
	public $breed_wk;
	public $color_wk;
	public $status_wk;
	public $image_wk;
	public $name;
	public $age;
	public $weight;
	public $acquired_dt;
	public $is_rescued;
	public $last_update_dt;
	public $is_deleted;
	public $create_dt;
	
}

?>