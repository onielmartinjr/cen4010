<?php

	/*
		Defines the Comment class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Comment extends Database_Object {
	
	protected static $table_name = 'comment';
	protected static $db_fields = array('comment_wk', 'user_wk', 'pet_wk', 'body', 'is_flagged', 'create_dt');
	
	public $comment_wk;
	public $user_wk;
	public $pet_wk;
	public $body;
	public $is_flagged;
	public $create_dt;
	
}

?>