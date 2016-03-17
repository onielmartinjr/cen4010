<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Delete Page";
	$page->is_admin_only = true;
	
	// check if page_wk is set
	if (!isset($_GET["page_wk"])) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
		die();
	}
	
	$page_wk = $_GET["page_wk"];
	$page_found = Page::find_by_id($page_wk);	
	
	// check that the page_wk exists
	if (!$page_found) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
		die();
	}
	
	// check if the page is deleted
	if ($page_found->is_deleted == "1") 
	{
		$session->message("The page you are trying to delete has already been deleted.");
		redirect_head(ROOT_URL);
		die();
	}
	
	//make sure we're not deleting the home page or about us page
	if ($page_found == '1' || $page_found == '2') {
		$session->message("You cannot delete the following page: ".$page_found->name.".");
		redirect_head(ROOT_URL."view_page.php?page_wk=".$page_found);
		die();
	}
	
	// if the user confirmd we're deleting the page
	if (isset($_POST["confirm"]))
	{	
		// delete the page
		$page_found->delete();
		$session->message("The page was successfully deleted!");
		redirect_head(ROOT_URL . "index.php");
		die();
	}
	else if (isset($_POST["deny"]))
	{
		//do not delete the page
		$session->message("The page was not deleted.");
		redirect_head(ROOT_URL . "view_page.php?page_wk={$page_found}");
		die();
	}
	
	require_once "requires/template/header.php";
	
?>	
	
	<form id="confirm_delete" action="admin_delete_page.php?page_wk=<?php echo $page_found; ?>" method="post">
		<label>Are you sure you want to delete the <b><?php echo $page_found->name; ?></b> page?</label> <br />
		<input type="submit" value="No, this was a mistake!" name="deny" />
		<input type="submit" value="Yes, delete the page." name="confirm" />
	</form>
	
<?php
	
	require_once "requires/template/footer.php";
	
?>