<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Manage Pet Status";
	$page->is_admin_only = true;
	
	//for efficiencie's sake, here's the order of execution for this page:
	//1. Delete the record if it needs to be deleted. 
		//we need to delete all pets referencing the status before updating/inserting
	//2. Grab all statuses for comparison.
	//3. Update the statuses as needed
	
	//process the delete
	if(isset($_GET['delete_pet_status_wk'])) {
		$to_delete_key = $_GET['delete_pet_status_wk'];
		
		//update all pets to use a status of undefined if they're using the one we're deleting
		$sql = "UPDATE `pet` SET `status_wk` = 0, `last_update_dt` = CURRENT_TIMESTAMP() WHERE `status_wk` = {$to_delete_key};";
		$database->query($sql);
		
		//now that no pets are using that status, we delete the record
		$to_delete = Status::find_by_id($to_delete_key);
		if($to_delete->delete()) 
			$session->message("<strong>".$to_delete->name."</strong> was deleted successfully.");
		else
			$session->message("<strong>".$to_delete->name."</strong> was not deleted successfully.<br />".$database->last_error);
			
		//redirect back to itself without the ?delete_pet_status_wk in the URL
		//so the system does not try to delete something again
		redirect_head(file_name_without_get());
	}
		
	//get all the statues
	$all_statuses = Status::find_all();
	
	//process the form data
	if(isset($_POST['submit'])) {
		//an array to keep track of all changes
		$changes = array();
	
		//flatten all statuses into an associated array
		//where the keys are the indexes
		//this makes searching 100% easier
		$flat_all_statuses = array();
		foreach($all_statuses AS $value) {
			$flat_all_statuses[$value->status_wk] =  $value->name;
		}

		//loop through all POST fields
		foreach($_POST as $key => $value) {
		
			//exclude the submit and new_status fields
			if(!in_array($key,array('submit','new_status'))) {
				//at this point, we are checking each field
				//so now we essentially need to update all fields
				//but only do so if there is a difference between the database and the form value
				if($flat_all_statuses[$key] != $value) {
					$row_to_update = Status::find_by_id($key);
					$row_to_update->name = $value;
					
					//try to save
					if($row_to_update->save()) 
						//if the item was changed successfully, add to array
						$changes[] = "<strong>".$value."</strong> was updated successfully.";
					else {
						//if the item was changed successfully, add to array
						$changes[] = "<strong>".$value."</strong> was not updated successfully.";
						$changes[] = $database->last_error;
					}
				}
			}
		}	
		
		
		//now we're done with all updates
		//check to see if we need to create a new status record
		//only if it's not blank
		if(!empty($_POST['new_status'])) {
			$new_status = new Status();
			$new_status->name = $_POST['new_status'];
		
			//try to save
			if($new_status->save()) 
				$changes[] = "<strong>".$new_status->name."</strong> was created successfully!";
			else
				$changes[] = "<strong>".$new_status->name."</strong> was not created successfully!";
				$changes[] = $database->last_error;
		}
		
		
		//at this point, we're done with all changes
		//check to see if there are any changes, if so, make them into messages
		if(count($changes) <> 0) 
			$session->message(implode("<br />", $changes));
			
		//lastly, redirect back to itself
		redirect_head(current_url());	

	}
	
	//header template
	require_once ("../requires/template/header.php");
	
?>
	<section id="registration" class="container"><form class="center" role="form" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		<?php
			//loop through all statuses, display them to the UI
			for($i = 0; $i < count($all_statuses); $i++) {
				echo "<div class=\"form-group\">";
					echo "<input type=\"text\" class=\"form-control\" name=\"". $all_statuses[$i]->status_wk . "\" value=\"". $all_statuses[$i]->name ."\">";
					echo "<a href=\"". file_name_with_get() ."?delete_pet_status_wk=". $all_statuses[$i]->status_wk ."\">Delete</a>";
				echo "</div>";
			}
		?>
		<br>
		<div class="form-group">New Status: <input type="text" name="new_status" class="form-control" value=""></div>
		
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section>
	
	
<?php
	
	//footer template
	require_once "../requires/template/footer.php";

?>