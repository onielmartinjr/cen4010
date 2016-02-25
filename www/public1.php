<?php

	//require the framework
	require_once "requires/initialize.php";
	
	$page = new Page();
	$page->name = "Public1";
	$page->body = "My body is public";
		
	require_once "requires/template/header.php";
		
	echo $page->body;

	require_once "requires/template/footer.php";

?>