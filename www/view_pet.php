<?php
	
	//require the framework
	require_once "requires/initialize.php";
	
	//construct the page
	$page = new Page();
	$page->name = "View a Pet";
	
	// check if pet_wk is set
	if (!isset($_GET["pet_wk"])) 
	{
		$session->message("There is an error with the pet you were trying to view.");
		redirect_head(ROOT_URL);
	}
	
	
	//get the pet info
	$pet = Pet::find_by_id($_GET["pet_wk"]);	

	
	// check that the pet_wk exists
	if (!$pet) 
	{
		$session->message("There is an error with the pet you were trying to view.");
		redirect_head(ROOT_URL);
	}
	
	// check if the pet is deleted
	if ($pet->is_deleted == "1") 
	{
		$session->message("The pet you are trying to view has been deleted.");
		redirect_head(ROOT_URL);
	}
	
	//PROCESS NEW COMMENTS HERE
	if(isset($_POST['submit'])) {
		//first of all, make sure user is logged in before we do any processing
		//just in case of hacking attempt
		if(!$session->is_logged_in) {
			$session->message("Your comment was not added successfully.");
			redirect_head(current_url()); //redirect back to itself
		}
		
		$new_comment = new Comment();
		$new_comment->user_wk = $user->user_wk;
		$new_comment->pet_wk = $pet->pet_wk;
		$new_comment->body = $_POST['body'];
		
		//attempt to save
		if($new_comment->save()) {
			$session->message("Your comment was added successfully!");
			redirect_head(current_url()); //redirect back to itself
		} else {
			//the comment did not save successfully, for whatever reason
			$session->message("Your comment was not added successfully.");
			redirect_head(current_url()); //redirect back to itself
		}
	}
	
	//since we're here - we're good to resume
	//get all the vaccinations for the pet
	$pet->get_my_vaccinations();
	//get all the comments for the pet
	$pet->get_my_comments();

	require_once "requires/template/header.php";
	
?>

	<h3><?php echo $pet->name; ?></h3>
	<p><em>IMAGE TO COME LATER</em><br /></p>
	<p><strong>Pet Type:</strong> <?php echo $pet->breed_wk->pet_type_wk->name; ?><br />
	<strong>Breed:</strong> <?php echo $pet->breed_wk->name; ?><br />
	<strong>Color:</strong> <?php echo $pet->color_wk->name; ?><br />
	<strong>Status:</strong> <?php echo $pet->status_wk->name; ?><br />
	<strong>Age:</strong> <?php echo $pet->age; ?><br />
	<strong>Weight:</strong> <?php echo $pet->weight; ?><br />
	<strong>Vaccination(s):</strong> <?php 
	
		if(empty($pet->vaccination)) {
			//if there are no vaccinations for this pet
			echo "No vaccinations";
		} else {
			$array_list = array();
			foreach($pet->vaccination AS $value)
				array_push($array_list, $value->vaccination_name);
			echo implode(", ", $array_list);
		}
	
	 ?><br />
	<strong>Date Acquired:</strong> <?php echo date('F d, Y h:i:s A', strtotime($pet->acquired_dt)); ?><br />
	<strong>Is it Rescued?:</strong> <?php echo ($pet->is_rescued == '1' ? 'Yes' : 'No'); ?><br />
	

<?php
	
	//display the links to update the pet for admins/staff
	if(isset($user)) {
		if ($user->role_wk == "2" || $user->role_wk == "3")
		{
			echo "<br /><br />";
			echo "<a href=\"admin_update_pet.php?pet_wk=" . $pet->pet_wk . "\">Edit Pet</a><br />";
			echo "<a href=\"admin_delete_pet.php?pet_wk=" . $pet->pet_wk . "\">Delete Pet</a>";
		}
	}
	

	//now we're doing the comments
	echo "<hr />";
	
		
	//we're going to display the comments here
	if(empty($pet->comment)) {
		//if there are no comments for this pet
		echo "<p><em>There are no comments.</em></p>";
	} else {
		foreach($pet->comment AS $value) {
			echo "<p><strong>".$value->user_wk->username."</strong> @ ".date('m/d/y - h:i:s A', strtotime($value->create_dt));
			echo "<br />".$value->body;
			echo "<p>";
		}
	}
		
	
	//now we're displaying the form to create new comments
	//only display this form if the user is logged in
	if($session->is_logged_in) {
		echo "<br />
	<!-- form -->
	<form action=\"".file_name_with_get()."\" method=\"post\">
		<textarea name=\"body\" cols=\"45\" rows=\"5\" placeholder=\"enter a new comment!\"/></textarea><br />
		<input type=\"submit\" value=\"submit\" name=\"submit\"/>
	</form>";
	}
	
	//include the footer
	require_once "requires/template/footer.php";

?>