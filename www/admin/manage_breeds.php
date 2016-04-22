<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Breed Management";
	$page->is_admin_only = true;
	// this page allows ADMINs and STAFFs to view breeds, create new breeds,
	// update current breeds and delete current breeds
	
	
	/* Update Fields */
	// Loop through each of the pet types and update the name or breeds
	// if any form was subitted
	$pet_types_array = Pet_Type::find_all();
	foreach ($pet_types_array as $type) 
	{
		$type_name = str_replace(' ', '_', $type->name); // clean the name
		
		/* if Pet_Type name is being updated */
		if (isset($_POST["submit_".$type_name."_name"]))
		{
			echo $type->name." is being updated.<br />";
			// check if empty
			if (empty($_POST["pet_type_name"]))
			{
				$session->message("The pet type must have a value to be updated!<br />");
				redirect_head(ROOT_URL."admin/manage_breeds.php");
			}
			
			// check if the Pet Type already exists
			if (Pet_Type::find_by_name($_POST["pet_type_name"], "name"))
			{
				$session->message("The pet type you are trying to update already exists!<br />");
				redirect_head(ROOT_URL."admin/manage_breeds.php");
			}
		
			// check if Pet Type name is being updated
			if ($type->name != $_POST["pet_type_name"])
			{
				$type->name = $_POST["pet_type_name"];
				if ($type->save())
				{
					$session->message("The pet type has been successfully updated to ".$type->name."!<br />");
					redirect_head(ROOT_URL."admin/manage_breeds.php");
				}
				else
				{
					$session->message("The pet type name cannot be updated at this time.<br />");
					redirect_head(ROOT_URL."admin/manage_breeds.php");
				}
			}
		}
		
		
		/* if Pet_Type is being deleted */
		if (isset($_POST["delete_".$type_name."_name"]))
		{
			$success = true; // track successful breed deletions
			
			// Reassign all of the pets associated with each of the breeds
			// associated with this pet type first. Then
			// delete all of the breeds associated to this pet type. Then
			// actually delete the pet type.
			$assoc_breeds = Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk.";");
			foreach ($assoc_breeds as $breed)
			{
				// get all pets associated with this breed
				$assoc_pets = Pet::find_by_sql("SELECT * FROM `".Pet::$table_name."` WHERE `breed_wk` = ".$breed->breed_wk.";");
				foreach ($assoc_pets as $pet)
				{
					// reassign the pet to undefined breed and undefined type
					$pet->breed_wk = 0;
					
					if ($pet->save())
					{
						$session->message($session->message.$pet->name." now has an undefined breed and type.<br />");
					}
					else
					{
						$success = false;
						$session->message($session->message.$pet->type." was not successfully redefined.<br />");
					}
				}
				
				// now delete the breed
				if ($breed->delete())
				{
					$session->message($session->message.$breed->name." has been successfully deleted.<br />");
				}
				else
				{
					$success = false;
					$session->message($session->message.$breed->name." was not successfully deleted.<br />");
				}
			}
			
			if ($success)
			{
				if ($type->delete())
				{
					$session->message($session->message.$type->name." was successfully deleted!<br />");
				}
				else
				{
					$session->message($session->message.$type->name." was not successfully deleted. Please try again.<br />");
				}
			}
			else
			{
				$session->message($session->message.$type->name." cannot be deleted due to remaining associated breeds and/or pets.<br />");
			}
			
			redirect_head(current_url());
		}
		
		
		/* if Pet_Type breed(s) is/are being updated or deleted*/
		if (isset($_POST["submit_".$type->name."_breeds"]))
		{
			$breeds_array = Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk.";");
			foreach ($breeds_array as $breed) 
			{
				// check if the breed is being deleted
				if (isset($_POST["delete_".$breed->breed_wk]))
				{
					$success = true; // tracks if pets are successfully saved
					// Reassign all pets associated with this breed to the undefined
					// breed associated with the pet type
					$undefined_breed = Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$breed->pet_type_wk." AND `name` = 'undefined' LIMIT 1;");
					if (!$undefined_breed) // if no undefined breed for this pet type
					{
						$undefined_breed = Breed::find_by_id(0);
					}
					
					$database->query("UPDATE `".Pet::$table_name."` SET `breed_wk` = ".$undefined_breed->breed_wk." WHERE `breed_wk` = ".$breed->breed_wk.";");					
					
					// now delete the actual breed
					if ($success)
					{
						if ($breed->delete())
						{
							$session->message($session->message."The breed {$breed->name} was successfully deleted!<br />");
						}
						else
						{
							$session->message($session->message."The breed {$breed->name} was not deleted. Please try again.<br />");
						}
					} 
					else
					{
						$session->message($session->message."Unable to delete the {$breed->name} breed because pets are still associated with it.<br />");
					}
				}
				// check if the name is being updated
				elseif ($breed->name != $_POST["{$breed->breed_wk}"])
				{
					// check if empty
					if (empty($_POST["{$breed->breed_wk}"]))
					{
						$session->message("The breed's updated name must have a text! ");
						redirect_head(ROOT_URL."admin/manage_breeds.php");
					}
					
					// check if the breed already exists for this pet type
					if (Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk." AND `name` = '".$_POST["{$breed->breed_wk}"]."';"))
					{
						$session->message($session->message."The breed ".$_POST["{$breed->breed_wk}"]." already exists and was not updated.<br />");
					}
					$breed->name = $_POST["{$breed->breed_wk}"];
					
					// update the breed name
					if ($breed->save())
					{
						$session->message($session->message."Breed was successfully updated to ".$breed->name."!<br />");
					}
					else
					{
						$session->message($session->message."Breed cannot be updated to ".$breed->name." at this time.<br />");
					}
				}
			}
			
			
			/* if breed is being added to a pet type*/
			if ($_POST["new_breed"] != "")
			{	
				// check if the breed already exists for this pet type
				if (Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk." AND `name` = '".$_POST["new_breed"]."';"))
				{
					$session->message($session->message."The breed ".$_POST["new_breed"]." already exists and was not added.<br />");
				}
				
				// add new breed
				$new_breed = new Breed();
				$new_breed->name = $_POST["new_breed"];
				$new_breed->pet_type_wk = $type->pet_type_wk;
				if ($new_breed->save())
				{
					$session->message($session->message."Breed ".$new_breed->name." was successfully added!<br />");
				}
				else
				{
					$session->message($session->message."Breed ".$new_breed->name." cannot be added at this time.<br />");
				}
			}
			
			redirect_head(ROOT_URL."admin/manage_breeds.php");
		}
		
		
		/* If Pet_Type is being added */
		if (isset($_POST["add_pet_type"]))
		{
			$new_pet_type = new Pet_Type();
			$new_pet_type->name = $_POST["name"];
			
			if ($new_pet_type->save())
			{
				// get the new pet type's wk
				$new_wk = $database->insert_id();
				
				$session->message($session->message."The new pet type {$new_pet_type->name} was successfully created!<br />");
				
				// create an undefined breed for the new pet type
				$undefined_breed = new Breed();
				$undefined_breed->name = "undefined";
				$undefined_breed->pet_type_wk = $new_wk;
				
				if ($undefined_breed->save())
				{
					$session->message($session->message."New undefined breed created for new pet type.<br />");
				}
				else
				{
					//die($database->last_error);
					$session->message($session->message."Creation of undefined breed for new pet type was unsuccessful.<br />");
				}
				
				redirect_head(current_url());
			}
			else
			{
				$session->message("The new pet type was not successfully created. Please try again.<br />");
				redirect_head(current_url());
			}
		}
	}
	
	
	
	//header template
	require_once ("../requires/template/header.php");
	

	// Add new pet type
	echo "<section class=\"container \"><form class=\"center\" role=\"form\" action=\"".file_name_with_get()."\" method=\"post\"><fieldset class=\"registration-form\">";
	echo "<label style=\"text-transform:capitalize; font-size:30px; font-weight:bold;\">Add New Pet Type: </label>"; 
	echo "<input type=\"text\" class=\"form-control\" name=\"name\"><br />";
	echo "<input type=\"submit\" class=\"btn btn-success btn-md btn-block\" value=\"Add\" name=\"add_pet_type\">";
	echo "</fieldset></form></section>";
	
	
	/* Form */
	
	// Loop through all of the breeds organized by pet type and create a form 
	// to update their names. No delete functionality yet. Admin must ensure
	// no pets are associated to a particular breed or pet type before hard delete.
	echo "<section class=\"container center\">";
	foreach ($pet_types_array as $type) 
	{
		// create an update form for each breed within this pet type
		
		echo "<div class=\"container center\" ><div class=\"row\" ><fieldset class=\"registration-form\"> <div class=\"col-xs-6\"><form class=\"center\" role=\"form\" action=\"".file_name_with_get()."\" method=\"post\"> ";
		echo "<label style=\"text-transform:capitalize; font-size:30px; font-weight:bold;\">{$type->name}</label><br>"; 
		echo "update to: <div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"pet_type_name\" value=\"".$type->name."\" /></div>";
		echo "<div class=\"form-group\"><input type=\"submit\" class=\"btn btn-success btn-md btn-block\" value=\"Update Pet Type\" name=\"submit_".$type->name."_name\"/></div>";
		echo "<div class=\"form-group\"><input type=\"submit\" class=\"btn btn-success btn-md btn-block\" value=\"Delete Pet Type\" name=\"delete_".$type->name."_name\"/></div>";
		echo "</form></div>";
		
		$breeds_array = Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk." ORDER BY `name` ASC;");
		
		// update/delete form
		echo "<div class=\"col-xs-6\" ><br><br><form action=\"".file_name_with_get()."\" method=\"post\"> <fieldset class=\"registration-form\">";
		$count = count($breeds_array);
		for ($i = 0; $i < $count; $i++)
		{
			echo "<div class=\"form-group row\"><input type=\"text\" class=\"form-control\" name=\"".$breeds_array[$i]->breed_wk."\" value=\"".$breeds_array[$i]->name."\">";
			echo "Delete:<input type=\"checkbox\" name=\"delete_".$breeds_array[$i]->breed_wk."\" value=\"delete\" /></div>";
		}
		echo "<div class=\"form-group\">Add new breed:<input type=\"text\" class=\"form-control\" name=\"new_breed\" value=\"\"></div>";
		echo "<div class=\"form-group\"><input type=\"submit\" class=\"btn btn-success btn-md btn-block\" value=\"save\" name=\"submit_".$type->name."_breeds\"/></div>";
		echo "</fieldset></form>";
		echo "</div></fieldset></div></div> <hr> ";
	}
	echo "</section>";
	
	

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>