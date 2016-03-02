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

		if ($new_page->save())
		{
			$session->message("Your new page was created successfully!");
			redirect_head(ROOT_URL . "view_page.php?page_wk=" . $database->insert_id());
		}
		else
		{
			$session->message("Unable to create a new page at this time.");
		}
	}
	
	require_once "requires/template/header.php";
?>
	
	<!-- Create a page form -->
	<form id="create_page" action="admin_create_page.php" method="post">
		Page Name: <input type="text" name="page_name" /> <br /> <br />
		Page Content: <textarea rows="5" cols="40" name="page_content"></textarea> <br /> <br />
		<input type="submit" value="submit" name="submit" />
	</form>
	

<?php

	require_once "requires/template/footer.php";

?>