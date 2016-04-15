<?php

	//require the framework
	require_once "../requires/initialize.php";

	$session->logout();

	//close connection
	$database->close_connection();

?>