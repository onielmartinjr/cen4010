<?php

abstract class Database_Object {

	//automatically sets the default fields on new object(s)
	function __construct() {
		//only do this if the table contains a field create_dt
		if(in_array("create_dt", static::$db_fields)) 
			$this->create_dt = current_timestamp();
			
		//only do this if the table contains a field is_deleted
		if(in_array("is_deleted", static::$db_fields)) 
			$this->is_deleted = 0;
		
		//only do this if the table contains a field last_update_dt
		if(in_array("last_update_dt", static::$db_fields)) 
			$this->last_update_dt = current_timestamp();
			
		//only do this if the table contains a field acquired_dt
		if(in_array("acquired_dt", static::$db_fields)) 
			$this->acquired_dt = current_timestamp();
			
		//only do this if the table contains a field requested_dt
		if(in_array("requested_dt", static::$db_fields)) 
			$this->acquired_dt = current_timestamp();
			
		//only do this if the table contains a field is_flagged
		if(in_array("is_flagged", static::$db_fields)) 
			$this->is_flagged = 0;
			
		//only do this if the table contains a field is_rescued
		if(in_array("is_rescued", static::$db_fields)) 
			$this->is_rescued = 0;
			
		//only do this if the table contains a field is_reset
		if(in_array("is_reset", static::$db_fields)) 
			$this->is_reset = 0;
			
		//only do this if the table contains a field role_wk
		if(in_array("role_wk", static::$db_fields)) 
			$this->role_wk = 1;
			
		//if we're in the Page class, set these default values
		if(get_class($this) == "Page") {
			//this is because these values will not be retained in the database
			//because all database stored pages will be public
			//so we are only going to track this manually for other pages
			$this->is_user_only = false;
			$this->is_admin_only = false;
		}
			
	}
	
	//returns count of all active rows
	public static function count_all() {
		global $database;
		$sql = "SELECT COUNT(*) FROM `".static::$table_name."`";
		//if the table contains is_deleted, make sure to include WHERE is_deleted = 0
		if(in_array("is_deleted", static::$db_fields)) 
			$sql .= " WHERE `is_deleted` = 0";
		$sql .= ";";
		
   		$result_set = $database->query($sql);
		//return the total # of rows
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}
	
	//find object by sql
	public static function find_by_sql($sql="") {
		global $database;
    	$result_set = $database->query($sql);
    	$object_array = array();
    	while ($row = $database->fetch_array($result_set)) {
    		$object_array[] = static::instantiate($row);
   		}
		return $object_array;
	}
	
	//find object by key
	public static function find_by_id($id=0) {
		global $session;
		global $database;
		
		// check that the id is an int
		if (!is_numeric($id))
		{
			$session->message("There is an error with the page you were trying to access.");
			redirect_head(ROOT_URL);
		}
		$sql = "SELECT `".static::$table_name."`.* FROM `".
			static::$table_name."` WHERE `".static::primary_key_field()."`={$id} LIMIT 1;";
		$result_array = static::find_by_sql($sql);
		
		if ($result_array != null)
			return array_shift($result_array);
		else
			return false;
	}
	
	//returns an array of all objects in the database
	public static function find_all() {
		$sql = "SELECT * FROM `".static::$table_name."`";
		//if the table contains is_deleted, make sure to include WHERE is_deleted = 0
		if(in_array("is_deleted", static::$db_fields)) 
			$sql .= " WHERE `is_deleted` = 0";
		$sql = $sql.";";

		return self::find_by_sql($sql);
    }
    
	//find object by name
	public static function find_by_name($name, $column_name="name") {
		//the default column to search is 'name' - this can be overriden (2nd parameter)
		
		global $database;
		$name = $database->escape_value($name);
		$column_name = $database->escape_value($column_name);
		$sql = "SELECT * FROM `".static::$table_name."` WHERE {$column_name} = '{$name}';";
		$result_array = static::find_by_sql($sql);
		
		if ($result_array != null)
			return array_shift($result_array);
		else
			return false;
	}
	
	//returns an instantiated object of itself from the database
	public static function instantiate($record) {
    	$object = new static;		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		    
		    //if we see an attribute that is greater than 3 characters long
		    //AND the attribute's last 3 characters are '_wk'
		    //AND that attribute is not the primary key 
		    //THEN we are going to instantiate the sub-objects automatically
		    //this will support our relational DB model in PHP
		    if(strlen($attribute) > 3) {
				if(substr($attribute,-3) == '_wk' && 
					$attribute != static::primary_key_field()) {
					//the class name will always be the same as the attribute name
					//with no '_wk', also, with capital letters for new words
					//this is an architect of the program
					$class_name = str_replace(' ', '_', ucwords(str_replace('_', ' ', substr($attribute, 0, -3))));
					
					//at this point, we are going to instantiate the sub-object
					$object->$attribute = $class_name::find_by_id($object->$attribute);
				}
		    }
		  }
		}
		return $object;
	}
	
	protected function attributes() { 
		// return an array of attribute names and their values
		$attributes = array();
		foreach(static::$db_fields as $field) {
	    	if(property_exists($this, $field)) {
	    		$attributes[$field] = $this->$field;
	    	}
		}
		return $attributes;
	}
	
	protected function sanitized_attributes() {
		global $database;
		$clean_attributes = array();
		// sanitize the values before submitting
		// Note: does not alter the actual value of each attribute
		foreach($this->attributes() as $key => $value){
	    	$clean_attributes[$key] = $database->escape_value($value);
		}
		return $clean_attributes;
	}
	
	public function has_attribute($attribute) {
		// We don't care about the value, we just want to know if the key exists
	 	// Will return true or false
	 	return array_key_exists($attribute, $this->attributes());
	}
	
	//saves an object to the database
	public function save() {
		return isset($this->{static::primary_key_field()}) ? $this->update() : $this->create();
	}
	
	public function create() {
		//creates a status object to database
		global $database;
		
		//first check to make sure that status doesn't exist in the database
		$attributes = $this->sanitized_attributes();
	
		$sql = "INSERT INTO `".static::$table_name."` (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "');";
		$database->query($sql);
		return ($database->insert_id()) ? true : false;
	}
	
	public function update() {
		//updates status to database
		global $database;
		
		//cleanse the attributes
		$attributes = $this->sanitized_attributes();
		
		//update the last_update_dt if the database has that field
		if(in_array("last_update_dt", static::$db_fields)) 
			$this->last_update_dt = current_timestamp();
		
		//form everything into a string
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
			$attribute_pairs[] = "{$key}='{$value}'";
		}
		
		//dynamically create the query
		$sql = "UPDATE `".static::$table_name."` SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE `".static::primary_key_field()."`=".$database->escape_value($this->{static::primary_key_field()}).";";
		$database->query($sql);
		
		return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		//deletes the record from the database
		
		//if the item contains an is_deleted field
		if(in_array("is_deleted", static::$db_fields)) {
			//cleanse the attributes
			$attributes = $this->sanitized_attributes();				
			
			//if we're here, then the table does contain and is_deleted field
			//so we simply need to update the is_deleted flag
			$sql = "UPDATE `".static::$table_name."` SET `is_deleted` = 1";
			
			//also - update last_update_dt if the object contains it
			if(in_array("last_update_dt", static::$db_fields)) {
				$this->last_update_dt = current_timestamp();
				$sql .= ", `last_update_dt` = '".$database->escape_value($this->last_update_dt)."'";
			}
			
			//also - update deleted_dt if the object contains it
			if(in_array("deleted_dt", static::$db_fields)) {
				$this->deleted_dt = current_timestamp();
				$sql .= ", `deleted_dt` = '".$database->escape_value($this->deleted_dt)."'";
			}
			
			$sql .= " WHERE `".static::primary_key_field()."`=". $database->escape_value($this->{static::primary_key_field()});
			$sql .= " LIMIT 1;";
		} 
		else 
		{
			//if we're here, then the table does not have an is_deleted field
			//so we have to hard delete it
			$sql = "DELETE FROM `".static::$table_name."`";
			$sql .= " WHERE `".static::primary_key_field()."`=". $database->escape_value($this->{static::primary_key_field()});
			$sql .= " LIMIT 1;";
		}
		
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	//returns a string containing the name of the primary key
	public static function primary_key_field() {
		return static::$db_fields[0];
	}
	
	//the to_string method of all of these objects
	public function __toString() {
		return $this->{static::primary_key_field()};
	}
	
}

?>