<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	$page = new Page();
	$page->name = "Update Page";
	$page->is_admin_only = true;
	
	// check if page_wk is set
	if (!isset($_GET["page_wk"])) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	// grab the page so it's content can be pre-loaded into the form
	$update_page = Page::find_by_id($_GET["page_wk"]);
	
	// check that the page_wk exists
	if (!$update_page) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	// update the page if the form is submitted
	if(isset($_POST["submit"])) 
	{ 
		$update_page->name = $_POST["page_name"];
		$update_page->body = $_POST["page_content"];

		// if the page successfully updates, go to the page
		if ($update_page->save())
		{
			$session->message("Your page was updated successfully!");
			redirect_head(ROOT_URL . "view_page.php?page_wk=" . $update_page->page_wk);
		}
		else
		{
			$session->message("The page was not updated. ".$database->last_error);
		}
	}
	
	//header template
	require_once ("../requires/template/header.php");

?>
	<!-- form -->
	<section id="registration" class="container"><form class="center" role="form" id="update_page" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		Page Name: <br><div class="form-group"><input type="text" name="page_name" class="form-control" value="<?php echo $update_page->name; ?>"<?php
			//if looking at home page or about us page, disable this field
			if($update_page == '1' || $update_page == '2')
				echo ' readonly';
		?> required/> </div>
		Page Content: <br><div class="form-group"><textarea rows="5" cols="150" class="ckeditor" name="page_content" ><?php echo $update_page->body; ?></textarea> </div>
		<input type="hidden" value="<?php echo $update_page->page_wk; ?>" name="page_wk" />
		
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section>

<?php

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable('message');

	//footer template
	require_once "../requires/template/footer.php";
	
?>