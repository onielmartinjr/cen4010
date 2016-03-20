<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Manage Web Site Settings";
	$page->is_admin_only = true;
	
	//process the form data
	if(isset($_POST['submit'])) {
		$changes = array();
		
		//loop through all POST fields
		foreach($_POST as $key => $value) {
			//exclude the submit field - ALWAYS
			if($key != 'submit') {
				//at this point, we are checking each field
				//so now we need to handle 2 basic scenarios:
				
				//1. The field in the form is blank.
					//delete the entry from the database IF it exists
				//2. The field in the form is not blank.
					//if the variable is not currently set
						//insert a new variable with the newly supplied value
					//if the value is currently set
						//update the existing database record with the newly supplied value
							//BUT only do this if the values are different, else, do nothing
						
				//scenario #1
				if(empty($value)) {
				
					//so we need to check if this setting is currently set
					if(isset($website_settings[$key])) {
						//this item does exist, delete it from the database
						
						$setting_item = Setting::find_by_name($key,"variable_name");
						$field_name = ucwords(str_replace('_', ' ', str_replace(' ', '_', $key)));
						if($setting_item->delete())
							//if the item was changed successfully, add to array
							$changes[] = "<strong>".$field_name."</strong> was deleted successfully.";
						else {
							//if the item was changed successfully, add to array
							$changes[] = "<strong>".$field_name."</strong> was not deleted successfully. ".$database->last_error;
						}
							
					} //else, it does not exist in the database, do nothing
				} else {
					//scenario #2
					
					//quick check
					//if populating the phone field, strip out all numeric characters
					if($key == 'phone_number')
						$value = return_numeric($value);
					
					//so we need to check if this setting is currently set
					if(isset($website_settings[$key])) {
						//it does currently exist in the database
						
						//now we need to confirm IF the values are different
						$setting_item = Setting::find_by_name($key,"variable_name");
						
						if($setting_item->variable_value != $value) {
							//if we're in here then the values are certainly different
							//update the database
							
							$setting_item->variable_value = $value;
							$field_name = ucwords(str_replace('_', ' ', str_replace(' ', '_', $key)));
							if($setting_item->save()) 
								//if the item was changed successfully, add to array
								$changes[] = "<strong>".$field_name."</strong> was updated successfully.";
							else {
								//if the item was changed successfully, add to array
								$changes[] = "<strong>".$field_name."</strong> was not updated successfully. ".$database->last_error;
							}
						} 
					} else {
						//it does not currently exist in the database
						//so we need to currently insert it
						
						$setting_item = new Setting();
						$setting_item->variable_name = $key;
						$setting_item->variable_value = $value;
						$field_name = ucwords(str_replace('_', ' ', str_replace(' ', '_', $key)));
							if($setting_item->save()) 
								//if the item was changed successfully, add to array
								$changes[] = "<strong>".$field_name."</strong> was updated successfully.";
							else {
								//if the item was changed successfully, add to array
								$changes[] = "<strong>".$field_name."</strong> was not updated successfully. ".$database->last_error;
							}
					}
				}
	
			}
		}	
		
		//at this point, we're done with all changes
		//check to see if there are any changes, if so, make them into messages
		if(count($changes) <> 0) 
			$session->message(implode("<br />", $changes));
		redirect_head(current_url());
		
	}
	
	// header
	require_once "requires/template/header.php";
	
?>
	
	<form action="<?php echo file_name_with_get(); ?>" method="post">
		Organization Name:<input type="text" name="organization_name" value="<?php echo isset($website_settings['organization_name']) ? $website_settings['organization_name'] : ''; ?>"><br />
		Time Zone:<select name="time_zone">
					  <option value="America/Anchorage" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'America/Anchorage') echo 'selected'; } ?>>Alaska</option>
					  <option value="America/Chicago" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'America/Chicago') echo 'selected'; } ?>>Central</option>
					  <option value="America/New_York" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'America/New_York') echo 'selected'; } else echo 'selected'; ?>>Eastern</option>
					  <option value="America/Adak" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'America/Adak') echo 'selected'; } ?>>Hawaii</option>
					  <option value="Pacific/Honolulu" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'Pacific/Honolulu') echo 'selected'; } ?>>Hawaii no DST</option>
					  <option value="America/Denver" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'America/Denver') echo 'selected'; } ?>>Mountain</option>
					  <option value="America/Phoenix" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'America/Phoenix') echo 'selected'; } ?>>Mountain no DST</option>
					  <option value="America/Los_Angeles" <?php if(isset($website_settings['time_zone'])) { if($website_settings['time_zone'] == 'America/Los_Angeles') echo 'selected'; } ?>>Pacific</option>
				  </select><br />
		E-mail Address:<input type="text" name="email_address" value="<?php echo isset($website_settings['email_address']) ? $website_settings['email_address'] : ''; ?>"><br />
		Phone Number:<input type="text" name="phone_number" value="<?php echo isset($website_settings['phone_number']) ? $website_settings['phone_number'] : ''; ?>"><br />
		FaceBook Link:<input type="text" name="facebook_link" value="<?php echo isset($website_settings['facebook_link']) ? $website_settings['facebook_link'] : ''; ?>"><br />
		Twitter Link:<input type="text" name="twitter_link" value="<?php echo isset($website_settings['twitter_link']) ? $website_settings['twitter_link'] : ''; ?>"><br />
		Instagram Link:<input type="text" name="instagram_link" value="<?php echo isset($website_settings['instagram_link']) ? $website_settings['instagram_link'] : ''; ?>"><br />
		YouTube Link:<input type="text" name="youtube_link" value="<?php echo isset($website_settings['youtube_link']) ? $website_settings['youtube_link'] : ''; ?>"><br />
		
		<input type="submit" value="submit" name="submit"/>
	</form>
	
	
<?php
	
	// footer
	require_once "requires/template/footer.php";

?>