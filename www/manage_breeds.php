<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Breed Management";
	$page->is_admin_only = true;
	// this page allows ADMINs and STAFFs to view breeds, create new breeds,
	// update current breeds and delete current breeds
	
	
	// header
	require_once "requires/template/header.php";
	

	// For each pet type, display update
	
	

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->remove_message();
	
	// footer
	require_once "requires/template/footer.php";
	
?>