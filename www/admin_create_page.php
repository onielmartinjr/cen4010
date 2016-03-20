<?php

	//require the framework
	require_once "requires/initialize.php";
	
	$page = new Page();
	$page->name = "Create a New Page";
	$page->is_admin_only = true;
		
	// create the page if the form is submitted
	if(isset($_POST["submit"])) 
	{ 
		$new_page = new Page();
		$new_page->name = $_POST["page_name"];
		$new_page->body = $_POST["page_content"];

		// if the page is successfully created, go to the page
		if ($new_page->save())
		{
			$session->message("Your new page was created successfully!");
			redirect_head(ROOT_URL . "view_page.php?page_wk=" . $database->insert_id());
		}
		else {
			$session->message("There was an issue with your request. ".$database->last_error);
		}
	}
	
	require_once "requires/template/header.php";
?>
	
	<!-- Create a page form -->
	<form id="create_page" action="<?php echo file_name_without_get(); ?>" method="post">
		Page Name: <input type="text" name="page_name" value="<?php echo (isset($new_page) ? $new_page->name : ''); ?>" /> <br /> <br />
		Page Content: <textarea rows="5" cols="40" name="page_content"><?php echo (isset($new_page) ? $new_page->body : ''); ?></textarea> <br /> <br />
		<input type="submit" value="Submit" name="submit" />
	</form>
	

<?php
	
	//this is a special instance, remove the message, if it's set, since we set the messages in this form
	$session->remove_message();
	
	//footer
	require_once "requires/template/footer.php";

?>