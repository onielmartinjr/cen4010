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


//function to display the pet table based on results
function display_pet_table($sql, $is_folder = false) {
	global $database;
	global $session;
	$return = "";
	
	$pets = Pet::find_by_sql($sql);
	$sql  = "SELECT * FROM `pet_wish_list` WHERE `user_wk` = ".$session->user_wk.";";
	$pwl  = Pet_Wish_List::find_by_sql($sql);
	
	// loop through all of the pet wish list elements (if any) and get their wk's
	$wish_array = array();
	foreach ($pwl as $wish_elem)
	{
		$wish_array[] = $wish_elem->pet_wk->pet_wk;
	}
	
	
	//only display the table with results if
	//there are more than 0 pets
	if(count($pets) > 0) {
		//there are pets to display
		$return = "<table style=\"width:100%\">
							<tr>
								<th></th>
								<th><a href=\"".file_name_without_get()."?toggle=name\">Name</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=pet_type\">Pet Type</a></th>		
								<th><a href=\"".file_name_without_get()."?toggle=breed\">Breed</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=color\">Color</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=status\">Status</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=age\">Age</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=weight\">Weight</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=date_added\">Date Added</a></th>";
		
		//if you're an admin or staff, display the ability to
		//immediately update the pet
		if(is_admin_or_staff())	{
			$return .= "<th>Update</th>";
		}
			
		$return .= "</tr>";
		
		
		//loop through all pets
		foreach($pets as $value) {
			$return .= "<tr id=\"".$value."_row\">
								<td><img src=\"";
			if($is_folder) 		$return .= 	"../";
			$return .= 			"uploads/".$value->image_wk->filename."\" style=\"width:75px;height:75px;\" ></td>
								<td><a href=\"".ROOT_URL."view_pet.php?pet_wk=".$value->pet_wk."\">".$value->name."</a></td>
								<td>".$value->breed_wk->pet_type_wk->name."</td>		
								<td>".$value->breed_wk->name."</td>
								<td>".$value->color_wk->name."</td>
								<td>".$value->status_wk->name."</td>		
								<td>".$value->age."</td>
								<td>".$value->weight."</td>
								<td>".date("m/d/Y h:i A", strtotime($value->create_dt))."</td>";
			
			// quick option to add/remove pet from wish list
			if ($session->is_logged_in)
			{	
				if (in_array($value->pet_wk, $wish_array)) // if the pet is already on the wish list
				{
					$return .= "<td><input id=\"".$value->pet_wk."\" type=\"button\" onclick=\"wish_list(".$value->pet_wk.", this.id)\" value=\"Remove from Wish List\" /></td>";
				}
				else // if the pet is not on the wish list
				{
					$return .= "<td><input id=\"".$value->pet_wk."\" type=\"button\" onclick=\"wish_list(".$value->pet_wk.", this.id)\" value=\"Add to Wish List!\" /></td>";
				}
			}
			
			//if you're an admin or staff, display the ability to
			//immediately update the pet
			if(is_admin_or_staff())	{
				$return .= "<td><a href=\"".ROOT_URL."admin/update_pet.php?pet_wk=".$value->pet_wk."\">Update</a></td>";
			}
								
			$return .= "</tr>";
		}
							
		$return .= "</table>";
	}
	$return .= "<p><em>Your search returned ".count($pets)." pet(s).</em></p>";
	
	return $return;
}

//function to display the pet blog like section based on results
//we should return object and do this outside of the class - maybe the view side
function display_pet_blog($sql, $is_folder = false) {
	global $database;
	global $session;
	$return = "";
	
	//get all the pets
	$pets = Pet::find_by_sql($sql);
	
	//get all the wish list items
	//only do this if the user is logged in
	if($session->is_logged_in) {
		$sql = "SELECT * FROM `pet_wish_list` WHERE `user_wk` = ".$session->user_wk.";";
		$pwl = Pet_Wish_List::find_by_sql($sql);
	} else
		$pwl = array();
	
	// loop through all of the pet wish list elements (if any) and get their wk's
	$wish_array = array();
	foreach ($pwl as $wish_elem)
	{
		$wish_array[] = $wish_elem->pet_wk->pet_wk;
	}
	
	
	//only display the table with results if
	//there are more than 0 pets
	if(count($pets) > 0) {
		//there are pets to display
		$return = "<div>
								Sort by:&nbsp;&nbsp;&nbsp;<a href=\"".file_name_without_get()."?toggle=name\">Name</a> &nbsp;&nbsp;|&nbsp;&nbsp;
								<a href=\"".file_name_without_get()."?toggle=pet_type\">Pet Type</a>&nbsp;&nbsp;|&nbsp;&nbsp;		
								<a href=\"".file_name_without_get()."?toggle=breed\">Breed</a>&nbsp;&nbsp;|&nbsp;&nbsp;
								<a href=\"".file_name_without_get()."?toggle=color\">Color</a>&nbsp;&nbsp;|&nbsp;&nbsp;
								<a href=\"".file_name_without_get()."?toggle=status\">Status</a>&nbsp;&nbsp;|&nbsp;&nbsp;
								<a href=\"".file_name_without_get()."?toggle=age\">Age</a>&nbsp;&nbsp;|&nbsp;&nbsp;
								<a href=\"".file_name_without_get()."?toggle=weight\">Weight</a>&nbsp;&nbsp;|&nbsp;&nbsp;
							    <a href=\"".file_name_without_get()."?toggle=date_added\">Date Added</a>&nbsp;&nbsp;|&nbsp;&nbsp;";
		
		//if you're an admin or staff, display the ability to
		//immediately update the pet
		if(is_admin_or_staff())	{
			//$return .= "Update";
		}
			
		$return .= "</div><br><br>";
		
		
		//loop through all pets
		$rowCutter = 0;
		$return .= "<section class=\"blog\" style=\"width:100%\"><div class=\"row\">";
		foreach($pets as $value) {
			$value->get_my_comments();
			$return .= "	<div class=\"";
			if(is_mobile())
				$return .= "col-xs-11";
			else
				$return .= "col-xs-6";
			$return .= "\">
							<div id=\"".$value."_row\" class=\"blog-item\">
								<a href=\"".ROOT_URL."view_pet.php?pet_wk=".$value->pet_wk."\"><img class=\"img-responsive img-blog\" src=\"";
			if($is_folder) 		$return .= 	"../";
			$return .= 			"uploads/".$value->image_wk->filename."\" ></a>
								<div class=\"blog-content\">
								<div class=\"entry-meta\">
								<span><i class=\"icon-calendar\">&nbsp;".date("m/d/Y h:i A", strtotime($value->create_dt))."</i><span>
								<span>&nbsp;&nbsp;&nbsp;&nbsp;<i class=\"icon-comment\">&nbsp;".count($value->comment)."</i><span>
								</div>
								<h3><a href=\"".ROOT_URL."view_pet.php?pet_wk=".$value->pet_wk."\">".$value->name."</a></h3>
								Pet Type: ".$value->breed_wk->pet_type_wk->name."	
								<br>Pet Breed: ".$value->breed_wk->name."
								<br>Pet Color: ".$value->color_wk->name."
								<br>Pet Status: ".$value->status_wk->name."		
								<br>Pet Age: ".$value->age."
								<br>Pet Weight: ".$value->weight."<br>";
			
			// quick option to add/remove pet from wish list
			if ($session->is_logged_in)
			{	
				if (in_array($value->pet_wk, $wish_array)) // if the pet is already on the wish list
				{
					$return .= "<br><input id=\"".$value->pet_wk."\" type=\"button\" class=\"btn btn-success btn-md btn-block\" onclick=\"wish_list(".$value->pet_wk.", this.id)\" value=\"Remove from Wish List\" />";
				}
				else // if the pet is not on the wish list
				{
					$return .= "<br><input id=\"".$value->pet_wk."\" type=\"button\" class=\"btn btn-success btn-md btn-block\" onclick=\"wish_list(".$value->pet_wk.", this.id)\" value=\"Add to Wish List!\" />";
				}
			}
			
			//if you're an admin or staff, display the ability to
			//immediately update the pet
			if(is_admin_or_staff())	{
				$return .= "<a href=\"".ROOT_URL."admin/update_pet.php?pet_wk=".$value->pet_wk."\" class=\"btn btn-success btn-md btn-block\">Update</a>";
			}
			$return .= "</div></div></div>";
			
			$rowCutter++;
			//end current row and start new - visually if content = 2
			if(is_mobile())
				$mod_by = 1;
			else
				$mod_by = 2;
				
			if($rowCutter%$mod_by == 0)
				$return .= "</div><div class=\"row\">";
		}
							
		$return .= "</div>";	
	}
	
	$return .= "<p><em>Your search returned ".count($pets)." pet(s).</em></p>";
	$return .= "</section>";
	
	return $return;
}


//function to return list of 6 latest pet objects for homepage slider
function get_slider_pets(){
	
	$six_pets = Pet::find_by_sql("SELECT * FROM pet WHERE is_deleted = 0 ORDER BY create_dt DESC LIMIT 17;");
	return $six_pets;
}

?>