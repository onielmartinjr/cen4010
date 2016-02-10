<?php


class Message {
	
	protected static $table_name = 'ticket_message';
	protected static $db_fields = array('ticket_message_wk', 'ticket_wk', 'user_wk', 'message', 'create_dt');
	
	public $ticket_message_wk;
	public $ticket_wk;
	public $user_wk;
	public $message;
	public $create_dt;
	
	
	
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
	
	//find ticket message by ticket_wk
	public static function find_by_ticket_id($id=0) {
		$result_array = self::find_by_sql("SELECT
	tm.*
FROM
	ticket_message tm
    INNER JOIN users u
    	ON u.user_wk = tm.user_wk
    INNER JOIN tickets t
    	ON t.ticket_wk = tm.ticket_wk
WHERE
	tm.ticket_wk = {$id}
ORDER BY
	tm.create_dt DESC");
		if ($result_array != null) {
			return $result_array;
		}
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
		// if we have an object without an ID, create a new record 
		$this->create();
	}
	
	public function create() {
		//creates a ticket message object to database
		global $database;

		$attributes = $this->sanitized_attributes();
		$sql = "INSERT INTO ".self::$table_name." (";
		$sql .= join(", ", array_keys($attributes));
		$sql .= ") VALUES ('";
		$sql .= join("', '", array_values($attributes));
		$sql .= "')";
		$database->query($sql);
		$id = $database->insert_id();
		$database->query("UPDATE `ticket_message` SET `create_dt` = NOW() WHERE `ticket_message_wk` = {$id};");
		return ($id) ? true : false;
	}	
	
}

?>