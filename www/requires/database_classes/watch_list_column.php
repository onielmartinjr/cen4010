<?php

	/*
		Defines the Watch_List_Column class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Watch_List_Column extends Database_Object {
	
	protected static $table_name = 'watch_list_column';
	protected static $db_fields = array('watch_list_column_entry_wk', 'display_name', 'column_name',
										'create_dt', 'is_deleted');
	
	public $watch_list_column_entry_wk;
	public $display_name;
	public $column_name;
	public $create_dt;
	public $is_deleted;
	
}

?>