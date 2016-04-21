<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Delete Pet";
	$page->is_admin_only = true;
	
	// check if pet_wk is set
	if (!isset($_GET["pet_wk"])) 
	{
		$session->message("There is an error with the pet you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	$pet_wk = $_GET["pet_wk"];
	$pet_found = Pet::find_by_id($pet_wk);	
	
	// check that the pet_wk exists
	if (!$pet_found) 
	{
		$session->message("There is an error with the pet you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	// check if the pet is deleted
	if ($pet_found->is_deleted == "1") 
	{
		$session->message("The pet you are trying to delete has already been deleted.");
		redirect_head(ROOT_URL);
	}
	
	// if the user confirmd we're deleting the pet
	if (isset($_POST["confirm"]))
	{	
		// delete the pet
		$pet_found->delete();
		$session->message("The pet was successfully deleted!");
		redirect_head(ROOT_URL . "search_pets.php");
	}
	else if (isset($_POST["deny"]))
	{
		//do not delete the pet
		$session->message("The pet was not deleted.");
		redirect_head(ROOT_URL . "view_pet.php?pet_wk={$pet_found}");
	}
	
	//header template
	require_once ("../requires/template/header.php");
	
?>	

	
	<section class="container"><form class="center" role="form" id="confirm_delete" id="confirm_delete" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		<label>Are you sure you want to delete the <strong><?php echo $pet_found->name; ?></strong> pet?</label> <br />
		<input type="submit" value="No, this was a mistake!" class="btn btn-success btn-md btn-block" name="deny" />
		<input type="submit" value="Yes, delete the pet." class="btn btn-success btn-md btn-block" name="confirm" />
	</fieldset></form></section>
	
<?php
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>