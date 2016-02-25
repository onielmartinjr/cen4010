<?php

	//require the framework
	require_once "requires/initialize.php";
	
	$page = new Page();
	$page->name = "Admin1";
	$page->body = "My body needs your administration :0";
	$page->is_admin_only = true;
		
	require_once "requires/template/header.php";
		
	echo $page->body;

	require_once "requires/template/footer.php";

?>