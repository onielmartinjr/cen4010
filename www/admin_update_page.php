<?php

	//require the framework
	require_once "requires/initialize.php";
	
	$page = new Page();
	$page->name = "Update a Page";
	$page->is_admin_only = true;
		
	// update the page if the form is submitted
	if(isset($_POST["submit"])) 
	{ 
		$update_page = Page::find_by_id($_POST["page_wk"]);
		$update_page->name = $_POST["page_name"];
		$update_page->body = $_POST["page_content"];

		// if the page successfully updates, go to the page
		if ($update_page->save())
		{
			$session->message("Your page was updated successfully!");
			redirect_head(ROOT_URL . "view_page.php?page_wk=" . $update_page->page_wk);
			die();
		}
		else
		{
			$session->message("The page was not updated.");
		}
	}
	
	// grab the page so it's content can be pre-loaded into the form
	$update_page = Page::find_by_id($_GET["page_wk"]);
	
	// check that the page_wk exists
	if (!$update_page) 
	{
		$session->message("There is an error with the page you were trying to access.");
		redirect_head(ROOT_URL);
		die();
	}
	
	require_once "requires/template/header.php";
	
?>

	<!-- Update a page form -->
	<form id="update_page" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
		Page Name: <input type="text" name="page_name" value="<?php echo $update_page->name; ?>"<?php
			//if looking at home page or about us page, disable this field
			if($update_page == '1' || $update_page == '2')
				echo ' readonly';
		?>/> <br /> <br />
		Page Content: <textarea rows="5" cols="40" name="page_content"><?php echo $update_page->body; ?></textarea> <br /> <br />
		<input type="hidden" value="<?php echo $update_page->page_wk; ?>" name="page_wk" />
		<input type="submit" value="Submit" name="submit" />
	</form>

<?php

	require_once "requires/template/footer.php";

?>