<?php

	/*
		Defines the Pet class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Pet extends Database_Object {
	
	protected static $table_name = 'pet';
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

?>