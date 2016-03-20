<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	$page = new Page();
	$page->name = "Member1";
	$page->body = "My body is for your user-eyes only ;)";
	$page->is_user_only = true;
		
	//header template
	require_once ("../requires/template/header.php");
		
	echo $page->body;

	//footer template
	require_once "../requires/template/footer.php";

?>