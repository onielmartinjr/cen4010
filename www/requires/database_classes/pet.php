<?php

	/*
		Defines the Pet class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Pet extends Database_Object {
	
	public static $table_name = 'pet';
	protected static $db_fields = array('pet_wk', 'breed_wk', 'color_wk', 'status_wk', 'image_wk',
										'name', 'age', 'weight', 'is_rescued', 'last_update_dt',
										'is_deleted', 'create_dt');
	
	public $pet_wk;
	public $breed_wk;
	public $color_wk;
	public $status_wk;
	public $image_wk;
	public $name;
	public $age;
	public $weight;
	public $is_rescued;
	public $last_update_dt;
	public $is_deleted;
	public $create_dt;
	//additional fields
	public $vaccination;
	public $comment;
	
	//get all my vaccinations
	public function get_my_vaccinations() {
		global $database;
		
		$sql = "SELECT `v`.* FROM `pet` AS `p` ";
		$sql .= "INNER JOIN `pet_to_vaccination` AS `pv` ON `pv`.`pet_wk` = `p`.`pet_wk` ";
		$sql .= "INNER JOIN `vaccination` AS `v` ON `v`.`vaccination_wk` = `pv`.`vaccination_wk` ";
		$sql .= "WHERE `p`.`pet_wk` = ".$this->pet_wk." ";
		$sql .= "ORDER BY `v`.`vaccination_name` ASC;";

		//get the resultset
		$this->vaccination = Vaccination::find_by_sql($sql);
	}
	
	//get all my comments
	public function get_my_comments() {
		global $database;
		
		$sql = "SELECT `c`.* FROM `pet` AS `p` ";
		$sql .= "INNER JOIN `comment` AS `c` ON `c`.pet_wk = `p`.pet_wk ";
		$sql .= "WHERE `p`.pet_wk = ".$this->pet_wk." AND `c`.`is_flagged` = 0 ";
		$sql .= "ORDER BY `c`.`create_dt` ASC;";

		//get the resultset
		$this->comment = Comment::find_by_sql($sql);
	}
	
}

//function to create the SQL where statement
function generate_pet_where() {
	global $session;
	
	//if the where clause is not set, return all
	if(!isset($session->pet_where))
		return "";
	
	//if the where clause is set, but empty, return all
	if(empty($session->pet_where))
		return "";
	
	//if we're here, then we are actually
	//going to process it and generate SQL code
	$sql = "";
	
	//first, do the pet type
	if(isset($session->pet_where['pet_type'])) {
		if(!empty($session->pet_where['pet_type'])) {
			$sql .= "AND `pt`.`pet_type_wk` IN (";
			$sql .= implode (',', $session->pet_where['pet_type']);
			$sql .= ") ";
		}
	}
	
	//then the breed
	if(isset($session->pet_where['breed'])) {
		if(!empty($session->pet_where['breed'])) {
			$sql .= "AND `b`.`breed_wk` IN (";
			$sql .= implode (',', $session->pet_where['breed']);
			$sql .= ") ";
		}
	}
	
	//then the color
	if(isset($session->pet_where['color'])) {
		if(!empty($session->pet_where['color'])) {
			$sql .= "AND `c`.`color_wk` IN (";
			$sql .= implode (',', $session->pet_where['color']);
			$sql .= ") ";
		}
	}
	
	//then the status
	if(isset($session->pet_where['status'])) {
		if(!empty($session->pet_where['status'])) {
			$sql .= "AND `s`.`status_wk` IN (";
			$sql .= implode (',', $session->pet_where['status']);
			$sql .= ") ";
		}
	}
	
	//then the weight minimum
	if(isset($session->pet_where['age_min'])) {
		if(!empty($session->pet_where['age_min'])) {
			$sql .= "AND `p`.`age` >= ".$session->pet_where['age_min']." ";
		}
	}
	
	//then the weight maximum
	if(isset($session->pet_where['age_max'])) {
		if(!empty($session->pet_where['age_max'])) {
			$sql .= "AND `p`.`age` <= ".$session->pet_where['age_max']." ";
		}
	}
	
	//then the weight minimum
	if(isset($session->pet_where['weight_min'])) {
		if(!empty($session->pet_where['weight_min'])) {
			$sql .= "AND `p`.`weight` >= ".$session->pet_where['weight_min']." ";
		}
	}
	
	//then the weight maximum
	if(isset($session->pet_where['weight_max'])) {
		if(!empty($session->pet_where['weight_max'])) {
			$sql .= "AND `p`.`weight` <= ".$session->pet_where['weight_max']." ";
		}
	}
	
	return $sql;
}

//function to create the SQL order by statement
function generate_pet_order_by() {
	global $session;
	$default = "ORDER BY `p`.`name` ";
	
	//if the order by condition is not set, return default
	if(!isset($session->pet_order_by))
		return $default;
	
	//if the order by condition is set, but empty, return default
	if(empty($session->pet_order_by))
		return $default;
	
	//if we're here, then we are actually
	//going to process it and generate SQL code
	$sql = "ORDER BY ";
	
	//get the column name
	if($session->pet_order_by['column'] == 'name') 
		$sql .= "`p`.`name` ";
	else if($session->pet_order_by['column'] == 'pet_type') 
		$sql .= "`pt`.`name` ";
	else if($session->pet_order_by['column'] == 'breed') 
		$sql .= "`b`.`name` ";
	else if($session->pet_order_by['column'] == 'color') 
		$sql .= "`c`.`name` ";
	else if($session->pet_order_by['column'] == 'status') 
		$sql .= "`s`.`name` ";
	else if($session->pet_order_by['column'] == 'age') 
		$sql .= "`p`.`age` ";
	else if($session->pet_order_by['column'] == 'weight') 
		$sql .= "`p`.`weight` ";
	else if($session->pet_order_by['column'] == 'date_added') 
		$sql .= "`p`.`create_dt` ";
		
	//get the order
	$sql .= $session->pet_order_by['order']." ";
	
	return $sql;
}
?>