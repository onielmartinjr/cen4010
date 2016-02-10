<?php

	//get the initializations
	require_once "requires/initialize.php";
	
	echo random_key(100);
	
	//close connection
	$database->close_connection();

?>