<?php

	//require the framework
	require_once "../requires/initialize.php";

	$page = new Page();
	$page->name = "Update Pet";
	$page->is_admin_only = true;
	
	// check if pet_wk is set
	if (!isset($_GET["pet_wk"])) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	// grab the pet so it's content can be pre-loaded into the form
	$update_pet = Pet::find_by_id($_GET["pet_wk"]);
	
	// check that the pet_wk exists
	if (!$update_pet) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	//make sure the pet is not deleted
	if ($update_pet->is_deleted == '1') 
	{
		$session->message("The pet you are trying to update has been deleted.");
		redirect_head(ROOT_URL);
	}
	
	//get all the vaccinations for the pet
	$update_pet->get_my_vaccinations();
	
	//now we loop through all the vaccinations and put all the keys
	//into a 1D array so we can easily keep track of which ones this pet has
	$pets_vaccinations = array();
	foreach($update_pet->vaccination AS $value) {
		$pets_vaccinations[$value->vaccination_wk] = $value->vaccination_name;
	}
	
	//check to see if they wish to use the default image
	if(isset($_GET['use_default_image'])) {
		if($_GET['use_default_image'] == 'true') {
			$update_pet->image_wk = 0;
			$update_pet->save();
			
			$session->message("The image was successfuly reset to the default.");
			redirect_head(ROOT_URL."view_pet.php?pet_wk=".$update_pet->pet_wk);
		}
	}
	
	//with the form submission, basic process is:
	//1. Update the Vaccinations first (since it's a separate table)
	//2. Update the Pet 2nd
	//**if vaccination update failed, immediately fail and don't process the pet
	
	// update the pet if the form is submitted
	if(isset($_POST["submit"])) 
	{ 
	
		//we need to grab all the curren't pet's vaccinations
		$update_pet->get_my_vaccinations();
		
		//let's see if any vaccinations were selected
		if(isset($_POST["vaccination"])) {
			//if we're in here, then there is at least one vaccination selected in the form
			//we need to essentially loop through all the current vaccinations
			//and update them against the new form submissions
			
			//take the array of all current vaccinations and also take
			//the array of all form-selected vaccinations, merge the
			//2 arrays into 1 array, and remove all duplicate items
			//this is the array we're going to loop through
			$array_to_loop = array_unique(array_merge($update_pet->vaccination,$_POST["vaccination"]));
			
			//loop through each array so we can update accordingly
			foreach($array_to_loop AS $value) {
				//so now, one of 2 things can happen:
				//1. The vaccination should apply to the pet
					//if it's already applied, do nothing
					//if it's not already applied, add it
				//2. The vaccination should not apply to the pet
					//if it's already applied, delete it
					//if it's not already applied, do nothing
					
				if(in_array($value, $_POST["vaccination"])) {
					//if we're in here, then the value we're checking should be applied to the pet
					
					//so now, we need to see if the vaccination is not yet applied
					if(!in_array($value, $update_pet->vaccination)) {
						// if we're in here, then that vaccination is not already applied to the pet
						// add it
						$sql = "INSERT INTO `pet_to_vaccination` (`pet_to_vaccination_wk`, `pet_wk`, `vaccination_wk`, `create_dt`) ";
						$sql .= "VALUES (NULL, '".$update_pet->pet_wk."', '".$value."', CURRENT_TIMESTAMP);";
						
						//if there is an issue updating, immediately redirect
						if(!$database->query($sql)) {
							$session->message("There was an issue updating the pet; please try again.");
							redirect_head(ROOT_URL."admin/".file_name_with_get());
						}
					} //else, do nothing
				} else {
					//if we're in here, then the value we're checking should not be applied to the pet
					
					//so now, we need to see if the vaccination is already applied
					if(in_array($value, $update_pet->vaccination)) {
						// if we're in here, then that vaccination is already applied to the pet
						// delete it
						$sql = "DELETE FROM `pet_to_vaccination` ";
						$sql .= "WHERE `pet_to_vaccination`.`pet_wk` = ".$update_pet->pet_wk." ";
						$sql .= "AND `pet_to_vaccination`.`vaccination_wk` = ".$value.";";
						
						//if there is an issue updating, immediately redirect
						if(!$database->query($sql)) {
							$session->message("There was an issue updating the pet; please try again.");
							redirect_head(ROOT_URL."admin/".file_name_with_get());
						}
					} //else, do nothing
				}
			}
			
		} else {
			//if we're in here, then no vaccinations were selected in the form
			//so let's just delete all vaccinations tied to the pet in the database
			
			//only do this if the pet has no new vaccinations
			if(count($update_pet->vaccination) != 0) {
				$sql = "DELETE FROM `pet_to_vaccination` ";
				$sql .= "WHERE `pet_to_vaccination`.`pet_wk` = {$update_pet->pet_wk};";
				
				//if there is an issue deleting the vaccinations, redirect to myself
				//and immediately stop processing
				if(!$database->query($sql)) {
					$session->message("There was an issue updating the pet; please try again.");
					redirect_head(ROOT_URL."admin/".file_name_with_get());
				}
			}
		}
		
		//now that we're done with the vaccinations, we need to proces the image
		$default_image = new Image();
		$default_image->get_form_data($_FILES['file_upload']);
		
		//if there is no file, that is fine - we can skip since we're updating
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
				$update_pet->image_wk = $default_image->image_wk;
			}
		}
		
		//update all form fields
		$update_pet->name = $_POST["name"];
		$update_pet->breed_wk = $_POST["breed"];
		$update_pet->color_wk = $_POST["color"];
		$update_pet->status_wk = $_POST["status"];
		$update_pet->age = $_POST["age"];
		$update_pet->weight = $_POST["weight"];
		$update_pet->create_dt = date("Y-m-d H:i:s", strtotime($_POST["create_dt"]));
		$update_pet->is_rescued = $_POST["rescued"];
		
		// if the object successfully updates, go to view it
		if ($update_pet->save())
		{
			initiate_watch_list($update_pet->pet_wk, "updated");
			$session->message("The pet was updated successfully!");
			redirect_head(ROOT_URL . "view_pet.php?pet_wk=" . $update_pet->pet_wk);
		}
		else
		{
			$session->message("The pet was not updated. ".$database->last_error);
		}
		
	}
	
	//header template
	require_once ("../requires/template/header.php");
	
?>

	<!-- form -->
	<section id="registration" class="container"><form class="center" role="form" action="<?php echo file_name_with_get(); ?>" enctype="multipart/form-data" method="post" ><fieldset class="registration-form">
		Name: <br><div class="form-group"><input type="text" class="form-control" name="name" value="<?php echo $update_pet->name; ?>" required></div>
			<!-- default value needed for form -->
			<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
		Image: <br><div class="form-group"><input type="file" name="file_upload" class="btn btn-default btn-file btn-md" /><br />
			<a href="<?php echo current_url(); ?>&use_default_image=true" style="margin-left:3em;" >Use Default Image</a></div>
		Breed: <br><div class="form-group"><select name="breed" class="form-control">
				  <option value="0" <?php if($update_pet->breed_wk == '0') echo 'selected'; ?>>Undefined</option>
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
				  			
				  			//if the value is selected
				  			if($value->breed_wk == $update_pet->breed_wk)
				  				echo " selected";
				  			
				  			echo ">".$value->name."</option>";
				  		}
				  		
				  ?>
			   </select></div>
		Color: <br><div class="form-group"><select name="color" class="form-control">
					<option value="0" <?php if($update_pet->color_wk == '0') echo 'selected'; ?>>Undefined</option>
				  <?php
				  		
				  		//we need to display all available records
				  		$to_display = Color::find_all();
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<option value=\"".$value->color_wk."\"";
				  			
				  			//if the value is selected
				  			if($value->color_wk == $update_pet->color_wk)
				  				echo " selected";
				  			
				  			echo ">".$value->name."</option>";
				  		}
				  		
				  ?>
			   </select></div>
		Status: <br><div class="form-group"><select name="status" class="form-control">
					<option value="0" <?php if($update_pet->status_wk == '0') echo 'selected'; ?>>Undefined</option>
				  <?php
				  		
				  		//we need to display all available records
				  		$to_display = Status::find_all();
				  		
				  		//loop through all items
				  		foreach($to_display AS $value) {
				  			echo "<option value=\"".$value->status_wk."\"";
				  			
				  			//if the value is selected
				  			if($value->status_wk == $update_pet->status_wk)
				  				echo " selected";
				  			
				  			echo ">".$value->name."</option>";
				  		}
				  		
				  ?>
			   </select></div>
		Age: <br><div class="form-group"><input type="text" name="age" class="form-control" value="<?php echo $update_pet->age; ?>"></div>
		Weight: <br><div class="form-group"><input type="text" name="weight" class="form-control" value="<?php echo $update_pet->weight; ?>"></div>
		Vaccination(s): <br />
			<?php 
	
				//we need to display all available records
				$to_display = Vaccination::find_all();
				
		 		//loop through all items
		 		foreach($to_display AS $value) {
		  			echo "<input style=\"margin-left:1.70em;\" type=\"checkbox\" name=\"vaccination[]\" value=\"".$value->vaccination_wk."\"";
		  			
		  			//check to see if the pet has this item select
		  			if(isset($pets_vaccinations[$value->vaccination_wk]))
		  				echo ' checked';
		  			
		  			echo "> ".$value->vaccination_name."<br><br>";
		  		}
	
			 ?>
		Date Added:<br><div class="form-group"><input type="text" name="create_dt" class="form-control" value="<?php echo date('m/d/Y h:i:s A', strtotime($update_pet->create_dt)); ?>"></div>
		Is it Rescued?: &nbsp;&nbsp;
			<input type="radio" name="rescued" value="1"<?php if($update_pet->is_rescued == '1') echo 'checked="checked"'; ?>> Yes &nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" name="rescued" value="0"<?php if($update_pet->is_rescued == '0') echo 'checked="checked"'; ?>> No<br><br>

		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section>

<?php

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable('message');

	//footer template
	require_once "../requires/template/footer.php";
	
?>