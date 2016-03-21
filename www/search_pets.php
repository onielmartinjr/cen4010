<?php
	
	//require the framework
	require_once "requires/initialize.php";
	
	//construct the page
	$page = new Page();
	$page->name = "Search Pets";
	
	//grab the set of pets to display
	$pets = Pet::find_all();
	
	//depending on the # of records, we either display
	//them or display a message saying no pets returned
	if(count($pets) == 0) {
		//there are 0 pets in the search results
		$page->body = "<p><em>Your search returned 0 pets.</em></p>";
	} else {
		//there are pets to display
		$page->body = "<p>I'm working on it....</p>";
	}
	
	//include the header
	require_once "requires/template/header.php";
	
	//display the page
	echo $page->body;
	
	//include the footer
	require_once "requires/template/footer.php";

?>