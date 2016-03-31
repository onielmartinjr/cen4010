<?php

	/*
		Defines the Page class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Page extends Database_Object {
	
	protected static $table_name = 'page';
	protected static $db_fields = array('page_wk', 'name', 'body', 'last_update_dt', 'create_dt');
	
	public $page_wk;
	public $name;
	public $body;
	public $last_update_dt;
	public $create_dt;
	
	//these values are not retained in the database
	//they default to 0 in the __constructor in the Database_Object class
	//we do this to help keep track of user-only pages
	public $is_user_only;
	public $is_admin_only;
	
	//this is solely used to keep track of additional code needed per page
	public $style;
	public $script;
	
}

?>