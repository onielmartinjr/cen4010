<?php
	
	//require the framework
	require_once "requires/initialize.php";
	
	//construct the page
	$page = new Page();
	$page->name = "Search Pets";
	//set the style for the table
	$page->style = "<style>
						table, th, td {
							border: 1px solid black;
							border-collapse: collapse;
						}
						th, td {
							padding: 5px;
						}
						tr:nth-child(even) {background: #DDD}
						tr:nth-child(odd) {background: #FFF}
					</style>";
					
	//set the AJAX code
	$page->script = "<script>
function wish_list(pet, clicked_id)
{
	var doc_root = \"".ROOT_URL."\";
	var xhttp = new XMLHttpRequest();
	xhttp.onreadystatechange = function() {
		if (xhttp.readyState == 4 && xhttp.status == 200)
		{	
			var response = xhttp.responseText.trim();
			var msg_elem = document.getElementById(\"ajax_message\");
			var wl_button = document.getElementById(clicked_id);
			
			// display the message
			msg_elem.innerHTML = response;
			
			// change the text of the button
			if (wl_button.value == \"Add to Wish List!\") // if the pet was added, change to delete
			{
				wl_button.value = \"Remove from Wish List\";
			}
			else // if the pet was deleted, change to add
			{
				wl_button.value = \"Add to Wish List!\";
			}
		}
	};
	xhttp.open(\"GET\", doc_root + \"ajax_wish_list.php?p=\" + pet, true);
	xhttp.send();
};

</script>";
	
	//if the filtering criteria is changed, process it here
	if(isset($_POST['submit'])) {
		//the form was submitted, so we need to reset it
		$session->unset_variable('pet_where');
		
		//if the submit button is clicked, process it
		//if reset was clicked, this will skip - no issues
		if($_POST['submit'] == 'submit') {
		
			$temp = array();
			foreach($_POST AS $key => $value) {
				//ignore the submit item and all empty fields
				if($key != 'submit' && !empty($value)) {
					$temp[$key] = $value;
				}
			}
		
			//replace the filters in the session with the new items
			$session->set_variable('pet_where', $temp);
		}
	}
	
	//by this point, we know what the filter variables are
	//so we need to create the SQL that will reflect those changes
	//these are the function calls to generate the SQL
	//generate_pet_where();
	//generate_pet_order_by();
	
	//if the sorting method for the pets resultset changed, process it here
	if(isset($_GET['toggle'])) {
		//we need to process this change
		//so first we need to see what the current sorting method is
		if(isset($session->pet_order_by)) 
			$current_sort = $session->pet_order_by;
		else {
			$current_sort = array();
			$current_sort['column'] = 'name';
			$current_sort['order'] = 'ASC';
		}
		
		//so now we need to set the new sort
		$new_sort = array();
		//it is the new column item from the $_GET variable
		$new_sort['column'] = $_GET['toggle'];
		
		//now we need to determine the column sort order
		if($_GET['toggle'] == $current_sort['column']) {
			//the values are equivalent, simply switch from ASC to DESC and vice-versa
			if($current_sort['order'] == 'ASC')
				$new_sort['order'] = 'DESC';
			else
				$new_sort['order'] = 'ASC';
		} else {
			//the values are not equivalent, force set to ASC
			$new_sort['order'] = 'ASC';
		}
				
		//set the new sort mechanism
		$session->set_variable('pet_order_by', $new_sort);
		//redirect back
		redirect_head(file_name_without_get());
		
	}

	//grab the set of pets to display
	$sql = "SELECT `p`.* FROM `pet` AS `p` ";
	$sql .= "INNER JOIN `breed` AS `b` ON `b`.`breed_wk` = `p`.`breed_wk` ";
	$sql .= "INNER JOIN `pet_type` AS `pt` ON `pt`.`pet_type_wk` = `b`.`pet_type_wk` ";
	$sql .= "INNER JOIN `status` AS `s` ON `s`.`status_wk` = `p`.`status_wk` ";
	$sql .= "INNER JOIN `color` AS `c` ON `c`.`color_wk` = `p`.`color_wk` ";
	$sql .= "WHERE `p`.`is_deleted` = 0 ";
	$sql .= generate_pet_where()." ";
	$sql .= generate_pet_order_by(). " ";
	$sql .= ";";
	
	//grab the body of pets
	$page->body = display_pet_blog($sql);
	
	//page body object
	//$pageObj = find_by_sql($sql);
	
	//include the header
	require_once "requires/template/header.php";
	
	// temporary messages section
	 echo "<p id=\"ajax_message\" style=\"color: red; font-family: courier;\"></p>";

?>

<div class="container"><div class="row"><div class="col-xs-3">
<!-- form to limit search criteria -->
<form action="<?php echo file_name_without_get(); ?>" method="post">
	<fieldset>
		<legend>Filter</legend>
		Pet Type <br /><?php
				  		
				  		//we need to display all available items
				  		//do a concatenation of the pet type and the breed
				  		$sql = "SELECT DISTINCT `pt`.* FROM `breed` AS `b` ";
				  		$sql .= "INNER JOIN `pet_type` AS `pt` ON `pt`.`pet_type_wk` = `b`.`pet_type_wk` ";
				  		$sql .= "INNER JOIN `pet` AS `p` ON `p`.`breed_wk` = `b`.`breed_wk` AND `p`.`is_deleted` = 0 ";
				  		$sql .= "ORDER BY `pt`.`name` ASC;";

				  		$to_display = Pet_Type::find_by_sql($sql);
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<input style=\"margin-left: 1.5em;\" type=\"checkbox\" name=\"pet_type[]\" value=\"".$value->pet_type_wk."\"";
				  			
				  			
				  			//this will determine whether or not this item is checked
				  			if(isset($session->pet_where)) {
				  				if(isset($session->pet_where['pet_type'])) {
				  					if(in_array($value->pet_type_wk, $session->pet_where['pet_type']))
				  						echo ' checked';
				  				}
				  			}
				  			
				  			
				  			echo ">&nbsp;&nbsp;".$value->name."<br />";
				  		}
				  		echo '</select>';
				  		
				  ?>
		Breed <br /><?php
				  		
				  		//we need to display all available items
				  		//do a concatenation of the pet type and the breed
				  		$sql = "SELECT DISTINCT `b`.`breed_wk`, `b`.`pet_type_wk`, CONCAT(`pt`.`name`, ' - ', `b`.`name`) AS `name`, `b`.`create_dt` ";
				  		$sql .= "FROM `breed` AS `b` INNER JOIN `pet_type` AS `pt` ON `pt`.`pet_type_wk` = `b`.`pet_type_wk` ";
						$sql .= "INNER JOIN `pet` AS `p` ON `p`.`breed_wk` = `b`.`breed_wk` AND `p`.`is_deleted` = 0 ";
						$sql .= "ORDER BY `pt`.`name` ASC, `b`.`name` ASC;";

				  		$to_display = Breed::find_by_sql($sql);
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<input style=\"margin-left: 1.5em;\" type=\"checkbox\" name=\"breed[]\" value=\"".$value->breed_wk."\"";
				  			
				  			
				  			//this will determine whether or not this item is checked
				  			if(isset($session->pet_where)) {
				  				if(isset($session->pet_where['breed'])) {
				  					if(in_array($value->breed_wk, $session->pet_where['breed']))
				  						echo ' checked';
				  				}
				  			}
				  				
				  			
				  			echo ">&nbsp;".$value->name."<br />";
				  		}
				  		echo '</select>';
				  		
				  ?>
		Color <br /><?php
				  		
				  		//we need to display all available items
				  		//do a concatenation of the pet type and the breed
				  		$sql = "SELECT DISTINCT `c`.* FROM `color` AS `c` ";
				  		$sql .= "INNER JOIN `pet` AS `p` ON `p`.`color_wk` = `c`.`color_wk` AND `p`.`is_deleted` = 0 ";
				  		$sql .= "ORDER BY `c`.`name` ASC;";

				  		$to_display = Color::find_by_sql($sql);
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<input style=\"margin-left: 1.5em;\" type=\"checkbox\" name=\"color[]\" value=\"".$value->color_wk."\"";
				  			
				  			
				  			//this will determine whether or not this item is checked
				  			if(isset($session->pet_where)) {
				  				if(isset($session->pet_where['color'])) {
				  					if(in_array($value->color_wk, $session->pet_where['color']))
				  						echo ' checked';
				  				}
				  			}
				  			
				  			
				  			echo ">&nbsp;&nbsp;".$value->name."<br />";
				  		}
				  		echo '</select>';
				  		
				  ?>
		Status <br /><?php
				  		
				  		//we need to display all available items
				  		//do a concatenation of the pet type and the breed
				  		$sql = "SELECT DISTINCT `s`.* FROM `status` AS `s` ";
				  		$sql .= "INNER JOIN `pet` AS `p` ON `p`.`status_wk` = `s`.`status_wk` AND `p`.`is_deleted` = 0 ";
				  		$sql .= "ORDER BY `s`.`name` ASC;";

				  		$to_display = Status::find_by_sql($sql);
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<input style=\"margin-left: 1.5em;\" type=\"checkbox\" name=\"status[]\" value=\"".$value->status_wk."\"";
				  			
				  			
				  			//this will determine whether or not this item is checked
				  			if(isset($session->pet_where)) {
				  				if(isset($session->pet_where['status'])) {
				  					if(in_array($value->status_wk, $session->pet_where['status']))
				  						echo ' checked';
				  				}
				  			}
				  				
				  			
				  			echo ">&nbsp;&nbsp;".$value->name."<br />";
				  		}
				  		echo '</select>';
				  		
				  ?>
		
		<br>
		Age (years) <br><div class="row"><div class="col-xs-4"><input type="text" class="form-control" name="age_min" maxlength="4" size="5" value="<?php
		//this will determine whether or not this item is checked
		if(isset($session->pet_where)) {
			if(isset($session->pet_where['age_min'])) {
				echo $session->pet_where['age_min'];
			}
		}
		?>" ></div><div class="col-xs-1" style="padding-top:5px !important;"><span>to&nbsp;&nbsp;</span></div><div class="col-xs-4"><input type="text" class="form-control" name="age_max" maxlength="4" size="5" value="<?php
		//this will determine whether or not this item is checked
		if(isset($session->pet_where)) {
			if(isset($session->pet_where['age_max'])) {
				echo $session->pet_where['age_max'];
			}
		}
		?>" ></div>
		</div>
		Weight (lbs) <br><div class="row"><div class="col-xs-4"><input type="text" class="form-control" name="weight_min" maxlength="5" size="5" value="<?php
		//this will determine whether or not this item is checked
		if(isset($session->pet_where)) {
			if(isset($session->pet_where['weight_min'])) {
				echo $session->pet_where['weight_min'];
			}
		}
		?>" ></div><div class="col-xs-1" style="padding-top:5px !important;"><span>to&nbsp;&nbsp;</span></div><div class="col-xs-4"><input type="text" class="form-control" name="weight_max" maxlength="5" size="5" value="<?php
		//this will determine whether or not this item is checked
		if(isset($session->pet_where)) {
			if(isset($session->pet_where['weight_max'])) {
				echo $session->pet_where['weight_max'];
			}
		}
		?>" ></div>
		</div>
		<br><input type="submit" name="submit" class="btn btn-success btn-md btn-block" value="submit">
		<br><input type="submit" name="submit" class="btn btn-success btn-md btn-block" value="reset">
	</fieldset>
	
</form>


</div><div <div class="col-xs-9">
<legend>Results</legend><br>

<?php echo $page->body; 

//var_dump($pageObj); //display the page
?>
	
</div></div></div>
	
<?php require_once "requires/template/footer.php"; ?>
	
