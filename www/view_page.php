<?php
	
	//require the framework
	require_once "requires/initialize.php";
	
	// check if page_wk is set
	if (!isset($_GET["page_wk"])) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	
	$page_wk = $_GET["page_wk"];
	$page = Page::find_by_id($page_wk);	

	
	// check that the page_wk exists
	if (!$page) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
	}
	
	// check if the page is deleted
	if ($page->is_deleted == "1") 
	{
		$session->message("The page you are trying to view has been deleted.");
		redirect_head(ROOT_URL);
	}

	require_once "requires/template/header.php";
	
	echo $page->body;
	
	if(is_admin_or_staff()) {
		echo "<br /><br /><br />";
		echo "<a href=\"".ROOT_URL."admin/admin_update_page.php?page_wk=" . $page->page_wk . "\">Edit Page</a><br />";
		
		//if we're not looking at the home page or about us page, display the option to delete this page
		if($page != '1' && $page != '2')
			echo "<a href=\"".ROOT_URL."admin/admin_delete_page.php?page_wk=" . $page->page_wk . "\">Delete Page</a>";
	}
	
	require_once "requires/template/footer.php";

?>