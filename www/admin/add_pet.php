<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Add a Pet";
	$page->is_admin_only = true;
	// this page allows ADMINs and STAFFs to add new pets
	
	
	/* Update Pets */
	
	
	// Add the pet if the form is submitted
	if(isset($_POST["submit"])) 
	{ 
		// create the new pet 
		$new_pet = new Pet();
		
		// check for name
		if (isset($_POST["name"]))
		{
			$new_pet->name = $_POST["name"];
		}
		else
		{
			$session->message("");
		}
		
		// check for breed/pet type
		if (isset($_POST["breed"]))
		{
			$new_pet->breed_wk = $_POST["breed"];
		}
		
		// check for color
		if (isset($_POST["color"]))
		{
			$new_pet->color_wk = $_POST["color"];
		}
		
		// check for status
		if (isset($_POST["status"]))
		{
			$new_pet->status_wk = $_POST["status"];
		}
		
		// check for age
		if (isset($_POST["age"]))
		{
			$new_pet->age = $_POST["age"];
		}
		
		// check for weight 
		if (isset($_POST["weight"]))
		{
			$new_pet->weight = $_POST["weight"];
		}
		
		// check for rescued
		if (isset($_POST["rescued"]))
		{
			$new_pet->is_rescued = $_POST["rescued"];
		}
		
		// insert the new pet into the database
		if ($new_pet->save())
		{
			$session->message($new_pet->name." has been successfully added! ");
		}
		else
		{
			$session->message("The new pet cannot be added at this time. ");
		}
		
		// add appropriate vaccinations to the pet
		if (isset($_POST["vaccination"]))
		{
			foreach ($_POST["vaccination"] as $vac)
			{
				$sql = "INSERT INTO `pet_to_vaccination` (`pet_to_vaccination_wk`, `pet_wk`, `vaccination_wk`, `create_dt`) ";
				$sql .= "VALUES (NULL, '".$database->insert_id()."', '".$value."', CURRENT_TIMESTAMP);";
			}
			
			//if there is an issue updating, immediately redirect
			if(!$database->query($sql)) {
				$session->message("There was an issue adding the pet; please try again.");
				redirect_head(ROOT_URL."admin/".file_name_with_get());
			}
		}
		
	}
	
	
	// header
	require_once "../requires/template/header.php";
	
?>
	
	
	<h2>Add a Pet</h2>
	
	<form action="<?php echo file_name_with_get(); ?>" method="post">
		Name:<input type="text" name="name" value=""/><br />
		<p><em>IMAGE UPLOADING COMING SOON</em></p>
		Breed: <select name="breed">
				  <option value="0">Undefined</option>
				  <?php
				  		
				  		//we need to display all available items
				  		//do a concatenation of the pet type and the breed
				  		$sql = "SELECT `b`.`breed_wk`, `b`.`pet_type_wk`, CONCAT(`p`.`name`,' - ',`b`.`name`) AS `name`, ";
						$sql .= "`b`.`create_dt` FROM `breed` AS `b` INNER JOIN `pet_type` AS `p` ON `p`.`pet_type_wk` = `b`.`pet_type_wk` ";
						$sql .= "WHERE `b`.`breed_wk` > 0 ORDER BY `p`.`name` ASC, `b`.`name` ASC";
				  		$to_display = Breed::find_by_sql($sql);
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<option value=\"".$value->breed_wk."\"";
				  			echo ">".$value->name."</option>";
				  		}
				  		
				  ?>
			   </select><br />
		Color: <select name="color">
					<option value="0">Undefined</option>
				  <?php
				  		
				  		//we need to display all available records
				  		$to_display = Color::find_all();
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<option value=\"".$value->color_wk."\"";
				  			echo ">".$value->name."</option>";
				  		}
				  		
				  ?>
			   </select><br />
		Status: <select name="status">
					<option value="0" >Undefined</option>
				  <?php
				  		
				  		//we need to display all available records
				  		$to_display = Status::find_all();
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<option value=\"".$value->status_wk."\"";
				  			echo ">".$value->name."</option>";
				  		}
				  		
				  ?>
			   </select><br />
		Age: <input type="text" name="age" value="0"><br />
		Weight: <input type="text" name="weight" value="0.0"><br />
		Vaccination(s): <br />
			<?php 
	
				//we need to display all available records
				$to_display = Vaccination::find_all();
				
		 		//loop through all items
		 		foreach($to_display AS $value) {
		  			echo "<input style=\"margin-left:1.70em;\" type=\"checkbox\" name=\"vaccination[]\" value=\"".$value->vaccination_wk."\"";
		  			echo "> ".$value->vaccination_name."<br />";
		  		}
	
			 ?>
		Is it Rescued?: 
			<input type="radio" name="rescued" value="1">Yes 
			<input type="radio" name="rescued" value="0" checked>No<br />

		<input type="submit" value="submit" name="submit" />
	</form>
	

<?php
	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable("message");
	
	// footer
	require_once "../requires/template/footer.php";
?>
																					