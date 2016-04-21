<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Delete Page";
	$page->is_admin_only = true;
	
	// check if page_wk is set
	if (!isset($_GET["page_wk"])) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	$page_wk = $_GET["page_wk"];
	$page_found = Page::find_by_id($page_wk);	
	
	// check that the page_wk exists
	if (!$page_found) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	//make sure we're not deleting the home page or about us page
	if ($page_found == '1' || $page_found == '2') {
		$session->message("You cannot delete the following page: ".$page_found->name.".");
		redirect_head(ROOT_URL."view_page.php?page_wk=".$page_found);
	}
	
	// if the user confirmd we're deleting the page
	if (isset($_POST["confirm"]))
	{	
		// delete the page
		$page_found->delete();
		$session->message("The page was successfully deleted!");
		redirect_head(ROOT_URL . "index.php");
	}
	else if (isset($_POST["deny"]))
	{
		//do not delete the page
		$session->message("The page was not deleted.");
		redirect_head(ROOT_URL . "view_page.php?page_wk={$page_found}");
	}
	
	//header template
	require_once ("../requires/template/header.php");
	
?>	
	<section class="container"><form class="center" role="form" id="confirm_delete" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		<label>Are you sure you want to delete the <strong><?php echo $page_found->name; ?></strong> page?</label> <br />
		<input type="submit" value="No, this was a mistake!" class="btn btn-success btn-md btn-block" name="deny" />
		<input type="submit" value="Yes, delete the page." class="btn btn-success btn-md btn-block" name="confirm" />
	</fieldset></form></section>
	
<?php
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>