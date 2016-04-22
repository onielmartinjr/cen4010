<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Add a Pet";
	$page->is_admin_only = true;
	// this page allows ADMINs and STAFFs to add new pets
	
	
	/* Add Pet */
	
	
	// Add the pet if the form is submitted
	if(isset($_POST["submit"])) 
	{ 
		// create the new pet 
		$new_pet = new Pet();
		
		// check for name
		if (isset($_POST["name"]))
		$new_pet->name = $_POST["name"];
		
		// check for breed/pet type
		if (isset($_POST["breed"]))
		$new_pet->breed_wk = $_POST["breed"];
		
		// check for color
		$new_pet->color_wk = $_POST["color"];
		
		// check for status
		$new_pet->status_wk = $_POST["status"];
		
		// check for age
		$new_pet->age = $_POST["age"];
		
		// check for weight 
		$new_pet->weight = $_POST["weight"];
		
		// check for rescued
		$new_pet->is_rescued = $_POST["rescued"];
		
		
		
		//proces the image
		$default_image = new Image();
		$default_image->get_form_data($_FILES['file_upload']);
		
		//if there is no file, that is fine - we can set to 0 for default
		if(!(Image::$error_dictionary[$default_image->error] == 'No file.')) {
			$default_image->check_errors(true);
			//save the image record to the database
			if(!$default_image->save()) {
				$session->message("There was an issue: ".$database->last_error);
				redirect_head(current_url());
			}
			//get the key, and associate it to the image record
			$default_image->image_wk = $database->insert_id();
			//now we move the file and save
			$default_image->move_file();
		
			//if the image changed, set it
			if($default_image) {
				$new_pet->image_wk = $default_image->image_wk;
			}
		} else
			$new_pet->image_wk = 0;
		
		
		
		// insert the new pet into the database
		if ($new_pet->save())
		{	
			initiate_watch_list($database->insert_id(), "added");
			$session->message($new_pet->name." has been successfully added! ");
			$new_pet_wk = $database->insert_id();
		}
		else
		{
			$session->message("The new pet cannot be added at this time. ");
		}
		
		//do the vaccinations if there are some being set
		if(count($_POST['vaccination']) > 0) {
			// add appropriate vaccinations to the pet
			$sql = "INSERT INTO `pet_to_vaccination` (`pet_to_vaccination_wk`, `pet_wk`, `vaccination_wk`, `create_dt`) VALUES ";
			$vacs = "";
			foreach ($_POST["vaccination"] as $vac)
			{
				$vacs["{$vac}"] = "(NULL, '".$new_pet_wk."', '".$vac."', CURRENT_TIMESTAMP)";
			}
			$all_vacs = implode(",", $vacs);
			$sql .= $all_vacs.";";
		
			//if there is an issue updating, immediately redirect
			if(!$database->query($sql)) {
				$session->message("There was an issue adding the pet; please try again.");
				redirect_head(ROOT_URL."admin/".file_name_with_get());
			}
		}
		
		//redirect to see new animal
		redirect_head(ROOT_URL."view_pet.php?pet_wk={$new_pet_wk}");
	}
	
	
	// header
	require_once "../requires/template/header.php";
	
?>
	
	
	
	<section id="registration" class="container"><form class="center" role="form" action="<?php echo file_name_with_get(); ?>" enctype="multipart/form-data" method="post" ><fieldset class="registration-form">
		Name: <br><div class="form-group"><input type="text" class="form-control" name="name" value="" required/></div>
		<!-- default value needed for form -->
			<input type="hidden" class="form-control" name="MAX_FILE_SIZE" value="10000000" />
		Image:  <br><div class="form-group"><input type="file" class="btn btn-default btn-file btn-md" name="file_upload" /></div>
		Breed:  <br><div class="form-group"><select name="breed" class="form-control"></div>
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
			   </select></div>
		Color:  <br><div class="form-group"><select name="color" class="form-control">
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
			   </select></div>
		Status:  <br><div class="form-group"><select name="status" class="form-control">
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
			   </select></div>
		Age:  <br><div class="form-group"><input type="text" name="age" class="form-control" value="0"></div>
		Weight:  <br><div class="form-group"><input type="text" name="weight" class="form-control" value="0.0"></div>
		Vaccination(s): <br>
			<?php 
	
				//we need to display all available records
				$to_display = Vaccination::find_all();
				
		 		//loop through all items
		 		foreach($to_display AS $value) {
		  			echo " <br><div class=\"form-group center\"><input style=\"margin-left:1.70em;\" type=\"checkbox\" class=\"form-control\" name=\"vaccination[]\" value=\"".$value->vaccination_wk."\"";
		  			echo "> ".$value->vaccination_name."</div>";
		  		}
	
			 ?>
		Is it Rescued?:  <br><div class="form-group">
			<input type="radio" name="rescued" value="1">&nbsp;&nbsp;Yes &nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" name="rescued" value="0" checked>&nbsp;&nbsp;No</div>

		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section>
	

<?php
	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable("message");
	
	// footer
	require_once "../requires/template/footer.php";
?>
																					
