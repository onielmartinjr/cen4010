<?php

	/*
		Defines the Watch_List class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Watch_List extends Database_Object {
	
	protected static $table_name = 'watch_list';
	protected static $db_fields = array('watch_list_wk', 'user_wk', 'name',
										'last_update_dt', 'create_dt');
	
	public $watch_list_wk;
	public $user_wk;
	public $name;
	public $last_update_dt;
	public $create_dt;
	
}

?>