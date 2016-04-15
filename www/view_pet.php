<?php
	
	//require the framework
	require_once "requires/initialize.php";
	
	//construct the page
	$page = new Page();
	$page->name = "View a Pet";
	//set the AJAX code
	$page->script = "<script>
		function wish_list(pet, clicked_id)
		{
			var doc_root = \"".ROOT_URL."\";
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (xhttp.readyState == 4 && xhttp.status == 200)
				{	
					var response = xhttp.responseText.trim();
					var msg_elem = document.getElementById(\"ajax_message\");
					var wl_button = document.getElementById(clicked_id);
					
					// display the message
					msg_elem.innerHTML = response;
					
					// change the text of the button
					if (wl_button.innerHTML == \"Add to Wish List!\") // if the pet was added, change to delete
					{
						wl_button.innerHTML = \"Remove from Wish List\";
					}
					else // if the pet was deleted, remove row from the table
					{
						wl_button.innerHTML = \"Add to Wish List!\";
					}
				}
			};
			xhttp.open(\"GET\", doc_root + \"ajax_wish_list.php?p=\" + pet, true);
			xhttp.send();
		};

		</script>";
	
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
	
	//FLAG NEW COMMENTS HERE
	if(isset($_GET['flag_comment_wk'])) {
		//make sure user has access to do this
		if(!$session->is_logged_in) {
			$session->message("You do not have sufficient rights to flag this comment.");
			redirect_head(ROOT_URL.file_name_without_get()."?pet_wk=".$_GET['pet_wk']);
		}
		
		//first, make sure the comment exists
		$comment_to_flag = Comment::find_by_id($_GET['flag_comment_wk']);
		if(!$comment_to_flag) {
			//if the item does not exist in the database
			$session->message("You must've clicked on a bad URL; please try again.");
			redirect_head(ROOT_URL.file_name_without_get()."?pet_wk=".$_GET['pet_wk']);
		}
		
		//now we make sure the comment is not already flagged
		if($comment_to_flag->is_flagged == '1') {
			$session->message("That comment is already flagged.");
			redirect_head(ROOT_URL.file_name_without_get()."?pet_wk=".$_GET['pet_wk']);
		}
		
		//if we're here, go ahead and flag the comment
		$comment_to_flag->is_flagged = 1;
		if($comment_to_flag->save()) {
			$session->message("The comment was successfully flagged.");
			redirect_head(ROOT_URL.file_name_without_get()."?pet_wk=".$_GET['pet_wk']);
		}
	}
	
	//since we're here - we're good to resume heavy processing
	//get all the vaccinations for the pet
	$pet->get_my_vaccinations();
	//get all the comments for the pet
	$pet->get_my_comments();

	require_once "requires/template/header.php";
	
?>	<section id="blog" class="container">
	<div class="blog">
	<div class="blog-item">
	<img class="img-responsive img-blog" width="100%" src="uploads/<?php echo $pet->image_wk->filename; ?>">
	<div class="blog-content">
	<div class=\"entry-meta\">
		<span><i class="icon-calendar">&nbsp; <?php echo date("m/d/Y h:i A", strtotime($pet->create_dt)); ?></i><span>
		<span>&nbsp;&nbsp;&nbsp;&nbsp;<i class="icon-comment">&nbsp; <?php echo count($pet->comment); ?></i><span>
	</div><br>
	<h3><?php echo $pet->name; ?></h3>
	<p id="ajax_message" style="color: red; font-family: courier;"></p>
	<div class="form-group"> <button id="add_wl" class="btn btn-success btn-md col-xs-4" onclick="wish_list(<?php echo $pet->pet_wk ?>, 'add_wl')">Add to Wish List!</button><br /></div><br>
	<strong>Pet Type:</strong> <?php echo $pet->breed_wk->pet_type_wk->name; ?><br />
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
	<strong>Date Added:</strong> <?php echo date('F d, Y h:i:s A', strtotime($pet->create_dt)); ?><br />
	<strong>Is it Rescued?:</strong> <?php echo ($pet->is_rescued == '1' ? 'Yes' : 'No'); ?><br />
	
<?php
	
	//display the links to update the pet for admins/staff
	if(is_admin_or_staff()) {
		echo "<br>";
		echo "<div class=\"form-group\"><a class=\"btn btn-success btn-md col-xs-4\" href=\"".ROOT_URL."admin/update_pet.php?pet_wk=" . $pet->pet_wk . "\">Update Pet</a></div><br><br>";
		echo "<div class=\"form-group\"><a class=\"btn btn-success btn-md col-xs-4\" href=\"".ROOT_URL."admin/delete_pet.php?pet_wk=" . $pet->pet_wk . "\">Delete Pet</a></div><br>";
	}
	

	//now we're doing the comments
	echo "<hr />";
	
		
	//we're going to display the comments here
	echo "<div id=\"comments\" >
			<div id=\"comments-list\"><h3> ".count($pet->comment)." Comment(s)</h3>";
	if(empty($pet->comment)) {
		//if there are no comments for this pet
		echo "<p><em>There are no comments.</em></p>";
	} else {
		//loop through each comment
		foreach($pet->comment AS $value) { 
		?>
			
			<div class="media"><div class="pull-left"><img class="avatar img-circle" src="<?php echo ROOT_URL;?>/UI-links/images/blog/avatar3.png" alt=""></div>
			<div class="media-body"><div class="well"><div class="media-heading"><strong><?php echo $value->user_wk->username; ?></strong>&nbsp; <small><?php echo date('m/d/y - h:i:s A', strtotime($value->create_dt)); ?> </small>
			<div class="pull-right">
			<?php if($session->is_logged_in){
				echo "<a href=\"".file_name_with_get()."&flag_comment_wk=".$value->comment_wk."\"><img src=\"".ROOT_URL."requires/template/flag.png\" atl=\"Flag\"></a>";
				} ?>
			</div></div><p><?php echo $value->body; ?></p></div></div></div>
		<?php
		}
	}
	echo "</div>";
	
	//now we're displaying the form to create new comments
	//only display this form if the user is logged in
	if($session->is_logged_in) {
		echo "
		
	<!-- form -->
	<div id=\"comment-form\" ><form action=\"".file_name_with_get()."\" method=\"post\">
		<div width=\"100%\" ><div class=\"form-group\" ><textarea name=\"body\" style=\"resize: none; width:100%;\" class=\"form-control\" rows=\"5\" placeholder=\"enter a new comment\"/></textarea></div>
		<input type=\"submit\" class=\"btn btn-danger\" value=\"submit\" name=\"submit\"/>
	</form></div>";
	
	}
	
	echo '</div></div></div></section>';
	
	//include the footer
	require_once "requires/template/footer.php";

?>