<?php


class Status {
	
	protected static $table_name = 'ticket_status';
	protected static $db_fields = array('status_wk', 'name', 'is_deleted');
	
	public $status_wk;
	public $name;
	public $is_deleted = 0;
	
	
	
	//get list of all active statuss
	public static function get_all_active_status() {
		global $database;
		return self::find_by_sql("SELECT ".self::$table_name.".* FROM ".self::$table_name." WHERE is_deleted = 0 ORDER BY name ASC");
	}
	
	//find object by sql
	public static function find_by_sql($sql="") {
		global $database;
    	$result_set = $database->query($sql);
    	$object_array = array();
    	while ($row = $database->fetch_array($result_set)) {
    		$object_array[] = self::instantiate($row);
   		}
		return $object_array;	
	}
	
	//find status by name
	public static function find_active_by_name($name) {
		global $database;
		$name = $database->escape_value($name);
		$result_array = $database->query("SELECT COUNT(*) FROM ".self::$table_name." WHERE name = '{$name}' AND is_deleted = 0 LIMIT 1");
		$row = $database->fetch_array($result_array);
		return array_shift($row);
	}
	
	//find status by status_wk
	public static function find_by_id($id=0) {
		global $database;
		$result_array = self::find_by_sql("SELECT ".self::$table_name.".* FROM ".self::$table_name." WHERE status_wk={$id} LIMIT 1");
		if ($result_array != null)
			return array_shift($result_array);
		else
			return false;
	}
	
	private static function instantiate($record) {
    	$object = new self;		
		// More dynamic, short-form approach:
		foreach($record as $attribute=>$value){
		  if($object->has_attribute($attribute)) {
		    $object->$attribute = $value;
		  }
		}
		return $object;
	}
	
	protected function attributes() { 
		// return an array of attribute names and their values
		$attributes = array();
		foreach(self::$db_fields as $field) {
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
	
	private function has_attribute($attribute) {
		// We don't care about the value, we just want to know if the key exists
	 	// Will return true or false
	 	return array_key_exists($attribute, $this->attributes());
	}
	
	public function save() {
		global $session;
		
		//validate ticket reason
		if (!validate($this->name, 50)) {
			redirect_head("view_status.php");
		}
		//trim
		$this->name = trim($this->name);
		
		$this->create();
	}
	
	public function create() {
		//creates a status object to database
		global $database;
		global $session;
		
		//if the status already exsists, do not create
		if (self::find_active_by_name($this->name) >= 1) {
			$session->message("That status already exists.");
			redirect_head("view_status.php");
			die();
		}

		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		$database->query($sql);
		return ($database->insert_id()) ? true : false;
	}

	public function delete() {
		global $database;
		//deletes status from database
		$sql = "UPDATE ".self::$table_name;
		$sql .= " SET is_deleted = b'1'";
	 	$sql .= " WHERE status_wk=". $database->escape_value($this->status_wk);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	
}

?>