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
				$session->message("The pet type must have a value to be updated! ");
				redirect_head(ROOT_URL."admin/manage_breeds.php");
				die();
			}
			
			// check if the Pet Type already exists
			if (Pet_Type::find_by_name($_POST["pet_type_name"], "name"))
			{
				$session->message("The pet type you are trying to update already exists! ");
				redirect_head(ROOT_URL."admin/manage_breeds.php");
				die();
			}
		
			// check if Pet Type name is being updated
			if ($type->name != $_POST["pet_type_name"])
			{
				$type->name = $_POST["pet_type_name"];
				if ($type->save())
				{
					$session->message("The pet type has been successfully updated to ".$type->name."! ");
					redirect_head(ROOT_URL."admin/manage_breeds.php");
					die();
				}
				else
				{
					$session->message("The pet type name cannot be updated at this time. ");
					redirect_head(ROOT_URL."admin/manage_breeds.php");
					die();
				}
			}
		}
		
		/* if Pet_Type breed(s) is/are being updated */
		if (isset($_POST["submit_".$type->name."_breeds"]))
		{
			$breeds_array = Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk.";");
			foreach ($breeds_array as $breed) 
			{
				// check if the name is being updated
				if ($breed->name != $_POST["{$breed->breed_wk}"])
				{
					// check if empty
					if (empty($_POST["{$breed->breed_wk}"]))
					{
						$session->message("The breed's updated name must have a text! ");
						redirect_head(ROOT_URL."admin/manage_breeds.php");
						die();
					}
					
					// check if the breed already exists for this pet type
					if (Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk." AND `name` = '".$_POST["{$breed->breed_wk}"]."';"))
					{
						$session->message($session->message."The breed ".$_POST["{$breed->breed_wk}"]." already exists and was not updated. ");
					}
					$breed->name = $_POST["{$breed->breed_wk}"];
					
					// update the breed name
					if ($breed->save())
					{
						$session->message($session->message."Breed was successfully updated to ".$breed->name."! ");
					}
					else
					{
						$session->message($session->message."Breed cannot be updated to ".$breed->name." at this time. ");
					}
				}
			}
			
			/* if Pet_Type breed is being added */
			if ($_POST["new_breed"] != "")
			{	
				// check if the breed already exists for this pet type
				if (Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk." AND `name` = '".$_POST["new_breed"]."';"))
				{
					$session->message($session->message."The breed ".$_POST["new_breed"]." already exists and was not added. ");
				}
				
				// add new breed
				$new_breed = new Breed();
				$new_breed->name = $_POST["new_breed"];
				$new_breed->pet_type_wk = $type->pet_type_wk;
				if ($new_breed->save())
				{
					$session->message($session->message."Breed ".$new_breed->name." was successfully added! ");
				}
				else
				{
					$session->message($session->message."Breed ".$new_breed->name." cannot be added at this time. ");
				}
			}
			
			redirect_head(ROOT_URL."admin/manage_breeds.php");
			die();
		}
	}
	
	
	
	//header template
	require_once ("../requires/template/header.php");
	

	// Loop through all of the breeds organized by pet type and create a form 
	// to update their names. No delete functionality yet. Admin must ensure
	// no pets are associated to a particular breed or pet type before hard delete.
	foreach ($pet_types_array as $type) 
	{
		// create an update form for each breed within this pet type
		echo "<form action=\"".file_name_with_get()."\" method=\"post\">";
		echo "<label style=\"text-transform:capitalize; font-size:30px; font-weight:bold;\">{$type->name}</label>"; 
		echo "update to:<input type=\"text\" name=\"pet_type_name\" value=\"".$type->name."\" />";
		echo "<input type=\"submit\" value=\"Update Pet Type\" name=\"submit_".$type->name."_name\"/>";
		echo "</form><br /><br />";
		
		$breeds_array = Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk.";");
		
		// update form
		echo "<form action=\"".file_name_with_get()."\" method=\"post\">";
		$count = count($breeds_array);
		for ($i = 0; $i < $count; $i++)
		{
			echo $i+1 . ": <input type=\"text\" name=\"".$breeds_array[$i]->breed_wk."\" value=\"".$breeds_array[$i]->name."\"><br />";
		}
		echo "Add new breed:<input type=\"text\" name=\"new_breed\" value=\"\"><br />";
		echo "<input type=\"submit\" value=\"save\" name=\"submit_".$type->name."_breeds\"/>";
		echo "</form><br /><br />";
	}
	

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>