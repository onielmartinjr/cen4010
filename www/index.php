<?php

	//require the framework
	require_once "requires/initialize.php";
	
	//just in case there is a message, copy it over
	if(!empty($session->message))
		$session->message($session->message);
				
	//redirect to view home page
	redirect_head(ROOT_URL."view_page.php?page_wk=1");
	
	//close connection
	$database->close_connection();
	
?>