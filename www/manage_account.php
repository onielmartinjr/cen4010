<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Account Management";
	$page->is_user_only = true;
	
	
	require_once "requires/template/header.php";
	
?>	
	
	<br /><br />
	<a href="delete_user.php">Delete Account</a> <br />
	<a href="update_user.php">Update Account</a> <br />
	<!-- Other account management links here -->
	
<?php
	
	require_once "requires/template/footer.php";
	
?>