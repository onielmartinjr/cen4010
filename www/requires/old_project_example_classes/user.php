<?php


class User {
	
	protected static $table_name = 'users';
	protected static $db_fields = array('user_wk', 'email_address', 'username', 'hashed_password', 'business_name', 'role_wk', 'is_deleted', 'create_dt', 'role', 'email_notifications', 'ticket_column_order_by', 'ticket_column_order', 'user_column_order_by', 'get_name', 'user_column_order', 'last_ticket_view', 'last_user_view');
	
	public $user_wk;
	public $email_address;
	public $username;
	public $hashed_password;
	public $business_name;
	public $role_wk = 1; //default role_wk is for business
	public $role = "Business";
	public $is_deleted = 0;
	public $create_dt;
	public $email_notifications = 1;
	public $ticket_column_order_by = 'ticket_wk';
	public $ticket_column_order = 'ASC';
	public $user_column_order_by = 'user_wk';
	public $user_column_order = 'ASC';
	public $last_ticket_view = 'all_my_open';
	public $last_user_view = 'all_users';
	public $get_name;
	
	//checks if user is an administrator
	public function is_admin() {
		if ($this->role = 'Admin')
			return true;
		else
			return false;	
	}
	
	//for generating unique keys - ensure key is unsudes
	public static function is_key_unique($key) {
		global $database;
		$result = $database->query("SELECT * FROM `password_reset` WHERE `random_key` = '{$key}'");
		//if the # of rows returned is 0, then the key is unique
		if ($database->num_rows($result) == 0) 
			return true;
		else
			return false;
	}
	
	//for generating unique keys - ensure key is unsudes
	public static function does_key_exist($key) {
		global $database;
		$result = $database->query("SELECT * FROM `password_reset` WHERE `random_key` = '{$key}' LIMIT 1");
		//if the # of rows returned is 1, then the key does exist
		if ($database->num_rows($result) == 1) 
			return true;
		else
			return false;
	}
	
	
	//login script
	public static function login($username="", $password="") {
		//will retrieve user credentials if username and password are a match
		//if a match, it will spit out 1 user object
		//if not a match, it will return false
		global $database;
		global $session;
		global $page_file_name_with_get;
		$username = $database->escape_value($username);
		$password = $database->escape_value($password);
	
		$sql  = "SELECT * FROM ".self::$table_name." ";
		$sql .= "WHERE username = '{$username}' ";
		$sql .= "AND hashed_password = '{$password}' ";
		$sql .= "LIMIT 1";
		$result_array = self::find_by_sql($sql);
		//if soft deleted, display error message
		if (!empty($result_array)) {
			$user = array_shift($result_array);
			if ($user->is_deleted == 1) {
				$session->message($user->get_name().", your account has been disabled. If you feel this is an error please contact the administrator.");
				return false;
			} else
				//successfully logged in
				$session->message("Successfully logged in!");
				$session->login($user);
				return $user;
		}
		$session->message("The username and password combination does not exist.");
		//redirect_head(ROOT_URL); //do not re-direct - keep this commented out
		return false;
	}
	
	//find user by user_wk
	public static function find_by_id($id=0) {
		$result_array = self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE user_wk={$id} LIMIT 1");
		if ($result_array != null)
			return array_shift($result_array);
		else
			return false;
	}
	
	//for user dashboard
	public function find_dashboard($type = 'all_users') {
		if ($type == 'all_users')
			$result_array = self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk ORDER BY ".$this->user_column_order_by." ".$this->user_column_order);
		if ($type == 'all_businesses')
			$result_array = self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE roles.role = 'Business' ORDER BY ".$this->user_column_order_by." ".$this->user_column_order);
		if ($type == 'all_technicians')
			$result_array = self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE roles.role = 'Technician' ORDER BY ".$this->user_column_order_by." ".$this->user_column_order);
		if ($type == 'all_admins')
			$result_array = self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE roles.role = 'Admin' ORDER BY ".$this->user_column_order_by." ".$this->user_column_order);
		if ($type == 'all_disabled')
			$result_array = self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE users.is_deleted = 1 ORDER BY ".$this->user_column_order_by." ".$this->user_column_order);
		//return
		if ($result_array != null)
			return $result_array;
		else
			return false;
	}
	
	//find user by user_wk
	public static function find_by_username($username = '') {
		$result_array = self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE username='{$username}' LIMIT 1");
		return !empty($result_array) ? array_shift($result_array) : false;
	}
	
	//find user by sql
	public static function find_by_sql($sql="") {
		global $database;
    	$result_set = $database->query($sql);
    	$object_array = array();
    	while ($row = $database->fetch_array($result_set)) {
    		$object_array[] = self::instantiate($row);
   		}
		return $object_array;	
	}
	
	//find all users
	public static function find_all_users() {
		if (self::count_all() == 0)
			return false;
		else
			return self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk");
	}
	
	//find all techs & admins
	public static function find_all_techs_and_admins() {
		return self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE roles.role != 'Business' ORDER BY get_name ASC");
	}
	
	//find all users
	public static function find_all_admins_and_techs_for_email() {
		return self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE users.is_deleted = 0 AND roles.role != 'Business' AND email_notifications = 1");
	}
	
	//find all users
	public static function find_all_users_for_ticket_notification($ticket, $user) {
		return self::find_by_sql("SELECT users.*, roles.role FROM users INNER JOIN roles ON roles.role_wk = users.role_wk WHERE users.is_deleted = 0 AND email_notifications = 1 AND ((users.user_wk IN (".$ticket->submitted_by_user_wk.",".$ticket->tech_assigned_user_wk.") OR roles.role = 'Admin') AND users.user_wk != ".$user->user_wk.")");
	}
	
	//find all active users
	public static function find_all_active_users() {
		if (self::count_all_active() == 0)
			return false;
		else
			return self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE is_deleted = 0");
	}
	
	//find all inactive users
	public static function find_all_inactive_users() {
		if (self::count_all_inactive() == 0)
			return false;
		else
			return self::find_by_sql("SELECT ".self::$table_name.".*, roles.role FROM ".self::$table_name." INNER JOIN roles ON roles.role_wk = ".self::$table_name.".role_wk WHERE is_deleted = 1");
	}
	
	//gets user_wk from password reset key
	public static function get_user_wk_from_key($key) {
		global $database;
		$sql = "SELECT user_wk FROM `password_reset` WHERE `random_key` = '{$key}'";
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	//returns if key as reset or not
	public static function get_key_is_reset($key) {
		global $database;
		$sql = "SELECT is_reset FROM `password_reset` WHERE `random_key` = '{$key}' LIMIT 1";
   		$result_set = $database->query($sql);
		//return field
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}
	
	//returns password_reset key's create_dt
	public static function get_key_create_dt($key) {
		global $database;
		$sql = "SELECT create_dt FROM `password_reset` WHERE `random_key` = '{$key}' LIMIT 1";
   		$result_set = $database->query($sql);
		//return the date
		$row = $database->fetch_array($result_set);
		return array_shift($row);
	}
	
	//returns most recent key for password reset for user
	public static function get_users_most_recent_key($user_wk) {
		global $database;
		$sql = "SELECT random_key FROM `password_reset` WHERE `user_wk` = {$user_wk} ORDER BY create_dt DESC LIMIT 1";
   		$result_set = $database->query($sql);
		//if only 1 record, return the key
		if ($database->num_rows($result_set) == 1) {
			$row = $database->fetch_array($result_set);
			return array_shift($row);
		} else {
			return false;	
		}
	}
	
	//returns count of all users
	public static function count_all() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$table_name;
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	//returns count of all active users
	public static function count_all_active() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$table_name." WHERE is_deleted = 0";
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	//returns count of all inactive users
	public static function count_all_inactive() {
		global $database;
		$sql = "SELECT COUNT(*) FROM ".self::$table_name." WHERE is_deleted = 1";
   		$result_set = $database->query($sql);
		$row = $database->fetch_array($result_set);
    	return array_shift($row);
	}
	
	//get name depending on wether technician, admin or business
	public function get_name() {
		if (empty($this))
			echo 'true';
		if($this->role == 'Business') {
			//if business, return business name
			if ($this->business_name != NULL && space_clean($this->business_name) != ' ' && $this->business_name != '')
				return space_clean($this->business_name);
			else
				return 'Business';
		} else {
			//if technician or admin
			return $this->username;
		}
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
			if ($key != 'role')
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
		// if we have an object without an ID, create a new record - else, update current record
		//validate e-mail

		//trim
		$this->email_address = trim($this->email_address);
		$this->business_name = trim($this->business_name);
		return isset($this->user_wk) ? $this->update() : $this->create();
	}
	
	public function create() {
		//creates a user object to database
		global $database;
		//if user does not have a role_wk & username set, cannot create new record
		if($this->username != NULL && $this->role_wk != NULL) {
			$attributes = $this->sanitized_attributes();
			$sql = "INSERT INTO ".self::$table_name." (";
			$sql .= join(", ", array_keys($attributes));
			$sql .= ") VALUES ('";
			$sql .= join("', '", array_values($attributes));
			$sql .= "')";
			if($database->query($sql)) {
				$this->user_wk = $database->insert_id();
				//update timestamp too
				$database->query("UPDATE ".self::$table_name." SET create_dt = CURRENT_TIMESTAMP WHERE user_wk = ".$this->user_wk);
				return true;
			} else {
				return false;
			}
		} else
			return false;
	}

	public function update() {
		global $database;
		//updates user to database
		$attributes = $this->sanitized_attributes();
		$attribute_pairs = array();
		foreach($attributes as $key => $value) {
			if ($key != 'role')
				$attribute_pairs[] = "{$key}='{$value}'";
		}
		$sql = "UPDATE ".self::$table_name." SET ";
		$sql .= join(", ", $attribute_pairs);
		$sql .= " WHERE user_wk=". $database->escape_value($this->user_wk);
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}

	public function delete() {
		global $database;
		//deletes user from database
		$sql = "UPDATE ".self::$table_name;
		$sql .= " SET is_deleted = b'1'";
	 	$sql .= " WHERE user_wk=". $database->escape_value($this->user_wk);
		$sql .= " LIMIT 1";
		$database->query($sql);
		return ($database->affected_rows() == 1) ? true : false;
	}
	
	
}

	//if you're logged in, make user object
	if ($session->logged_in)
		$user = User::find_by_id($session->user_wk);

?>