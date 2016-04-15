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

	require_once "requires/template/header.php";
	
	//if homepage, show slider else show intended body
	if($page_wk == 1){
		require_once "requires/template/slider.php";
		require_once "requires/template/pet_slider.php";
		if(isset($website_settings['address']) && isset($website_settings['city']) && isset($website_settings['state'])){
			$unescapedAddress = $website_settings['address'];
			$unescapedAddress = preg_replace('!\s+!', ' ', $unescapedAddress);
			$escapedAddress = str_replace(' ', "+", $unescapedAddress);
			echo "<iframe width=\"100%\" height=\"450px\" frameborder=\"0\" style=\"border:0; margin:0px; padding:0px;\" src=\"https://www.google.com/maps/embed/v1/place?key=AIzaSyC1TqkP5WgrQc76w6jM-SiOuo5ZNns4dmU&q=".$escapedAddress.",".$website_settings['city'].",".$website_settings['state']."\" allowfullscreen></iframe>";
		}
	}
	else {
		echo "<section id=\"blog\"><div class=\"container\"><div class=\"row\"><div class=\"col-md-12\"><div class=\"blog\"><div class=\"blog-item\"><div class=\"blog-content\">";
		echo $page->body;
		echo "</div></div></div></div></div></div></section>";	
	}
	
	if(is_admin_or_staff()) {
		if($page != '1'){
			echo "<section><div class=\"container center\"> <div class=\"row col-md-3 center\">";
			echo "<a class=\"btn btn-block btn-sm emerald btn-primary\" href=\"".ROOT_URL."admin/update_page.php?page_wk=" . $page->page_wk . "\">Update Page</a>";
		}
		//if we're not looking at the home page or about us page, display the option to delete this page
		if($page != '1' && $page != '2'){
			echo "<a class=\"btn btn-block btn-sm emerald btn-primary\" href=\"".ROOT_URL."admin/delete_page.php?page_wk=" . $page->page_wk . "\">Delete Page</a>";
			echo "</div></div></section>";
		}
		if($page == '2'){
			echo "</div></div></section>";
		}
	}
	
	require_once "requires/template/footer.php";

?>

