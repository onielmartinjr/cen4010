<?php

	//require the framework
	require_once "requires/initialize.php";
	
	$page = new Page();
	$page->name = "Homepage";
	$page->body = "This is the body";
	
	require_once "requires/template/header.php";
	
	echo $page->body;

	require_once "requires/template/footer.php";

?>