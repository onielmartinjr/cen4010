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
					
	//grab the set of pets to display
	$sql = "SELECT `p`.* FROM `pet` AS `p` ";
	$sql .= "INNER JOIN `breed` AS `b` ON `b`.`breed_wk` = `p`.`breed_wk` ";
	$sql .= "INNER JOIN `pet_type` AS `pt` ON `pt`.`pet_type_wk` = `b`.`pet_type_wk` ";
	$sql .= "INNER JOIN `status` AS `s` ON `s`.`status_wk` = `p`.`status_wk` ";
	$sql .= "INNER JOIN `color` AS `c` ON `c`.`color_wk` = `p`.`color_wk` ";
	$sql .= "ORDER BY `p`.`name` ASC";
	$sql .= ";";
	$pets = Pet::find_by_sql($sql);
	
	//only display the table with results if
	//there are more than 0 pets
	if(count($pets) > 0) {
		//there are pets to display
		$page->body = "<table style=\"width:100%\">
							<tr>
								<th>Name</th>
								<th>Pet Type</th>		
								<th>Breed</th>
								<th>Color</th>
								<th>Status</th>
								<th>Age</th>
								<th>Weight</th>
								<th>Date Added</th>
							</tr>";
							
		//loop through all pets
		foreach($pets as $value) {
			$page->body .= "<tr>
								<td><a href=\"".ROOT_URL."view_pet.php?pet_wk=".$value->pet_wk."\">".$value->name."</a></td>
								<td>".$value->breed_wk->pet_type_wk->name."</td>		
								<td>".$value->breed_wk->name."</td>
								<td>".$value->color_wk->name."</td>
								<td>".$value->status_wk->name."</td>		
								<td>".$value->age."</td>
								<td>".$value->weight."</td>
								<td>".date("m/d/Y h:i A", strtotime($value->create_dt))."</td>
							</tr>";
		}
							
		$page->body .= "</table>";
	}
	$page->body .= "<p><em>Your search returned ".count($pets)." pet(s).</em></p>";
	
	//include the header
	require_once "requires/template/header.php";

?>

<!-- form to limit search criteria -->
<form action="<?php echo file_name_with_get(); ?>" method="post">
	<fieldset>
		<legend>Filter</legend>
		Pet Type: <input type="text"><br>
		Breed: <input type="text"><br>
		Color: <input type="text"><br>
		Status: <input type="text"><br>
		Age: <input type="text"><br>
		Weight: <input type="text"><br>
		<input type="submit" name="submit" value="submit"<br />
	</fieldset>
</form><br />

<?php
	
	//display the page
	echo $page->body;

	//include the footer
	require_once "requires/template/footer.php";

?>