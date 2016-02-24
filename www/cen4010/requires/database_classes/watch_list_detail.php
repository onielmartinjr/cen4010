<?php

	/*
		Defines the Watch_List_Detail class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Watch_List_Detail extends Database_Object {
	
	protected static $table_name = 'watch_list_detail';
	protected static $db_fields = array('watch_list_detail_wk', 'watch_list_wk', 'watch_list_column_wk',
										'value1', 'value2', 'create_dt');
	
	public $watch_list_detail_wk;
	public $watch_list_wk;
	public $watch_list_column_wk;
	public $value1;
	public $value2;
	public $create_dt;
	
}

?>