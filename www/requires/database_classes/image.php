<?php

	/*
		Defines the Image class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Image extends Database_Object {
	
	protected static $table_name = 'image';
	protected static $db_fields = array('image_wk', 'pet_wk', 'file_name', 'create_dt', 'is_deleted');
	
	public $image_wk;
	public $pet_wk;
	public $file_name;
	public $create_dt;
	public $is_deleted;
	
}

?>