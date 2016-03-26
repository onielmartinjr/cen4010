<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Pet Management";
	$page->is_admin_only = true;
	// this page allows ADMINs and STAFFs to view pets, add new pets,
	// update current pets and delete current pets
	
	/* 
		NOTE: if an ADMIN want to update a pet, they will have to go to the 
		view pets page, find the pet they want to edit and click the edit button.
		OR if they know the pet's page they can just go to that page and edit the
		information from there.
	*/
	
	
	/* Update Pets */
	
	
	/* Get the Pet Types and Breeds for the form */
	$pet_types = Pet_Type::find_all();
	foreach ($pet_types as $type)
	{
		$breeds = Breed::find_by_sql("SELECT * FROM `".Breed::$table_name."` WHERE `pet_type_wk` = ".$type->pet_type_wk.";");
		foreach ($breeds as $breed)
		{
			$pet_types_and_breeds["{$type->name}"]["{$breed->breed_wk}"] = $breed->name;
		}
	}
	
	// header
	require_once "requires/template/header.php";

?>
	
<script type="text/javascript">
	
	function add_breeds()
	{
		var pet_type = document.getElementById('pet_type');
		
		// populate the breeds select field with options
		

		// display the breeds select field
		document.getElementById('breed_label').style.display = '';
		var breeds = document.getElementById('breeds');
		breeds.style.display = '';
	}
	
</script>
	
	<h2>Add a Pet</h2>
	
	<form action="<?php echo file_name_with_get(); ?>" method="post">
		Name:<input type="text" name="name" value=""/><br />
		Type:<select id="pet_type" onchange="add_breeds()" >
			<option disabled selected value> -- select an option -- </option>
			<?php 
			$pet_types_array = Pet_Type::find_all();
			
			//debug
			echo "<pre>";
			print_r($pet_types_array);
			echo "</pre>";
			
			foreach ($pet_types_array as $type) 
			{
				echo "<option value=\"".$type->pet_type_wk."\">".$type->name."</option>";
			}
			?>
		</select><br />
		<label id="breed_label" style="display: none">Breed</label>
		<select id="breeds" style="display: none">
		</select><br />
		
		
	</form>
	

<?php
	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->remove_message();
	
	// footer
	require_once "requires/template/footer.php";
?>