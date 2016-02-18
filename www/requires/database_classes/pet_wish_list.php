<?php

	/*
		Defines the Pet_Wish_List class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Pet_Wish_List extends Database_Object {
	
	protected static $table_name = 'pet_wish_list';
	protected static $db_fields = array('pet_wish_list_wk', 'user_wk', 'pet_wk', 'create_dt', 'is_deleted');
	
	public $pet_wish_list_wk;
	public $user_wk;
	public $pet_wk;
	public $create_dt;
	public $is_deleted;
	
}

?>