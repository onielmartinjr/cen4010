<?php
	
	//require the framework
	require_once "requires/initialize.php";
	
	//construct the page
	$page = new Page();
	$page->name = "Search Pets";
	
	
	require_once "requires/template/header.php";
	
?>

	<p>Search for Pets!<br />Coming soon to a theatre near you.</p>

<?php
	
	//include the footer
	require_once "requires/template/footer.php";

?>