<?php

	/*
		This page will contain all of the constants that will be used by the system.
	*/

	//Define all the constants for the database connection
	
	//if we're running locally, set these constants
	if($am_i_local) {
		//for our local computer
		define("DB_HOST","localhost");
		define("ROOT_URL","http://localhost/cen4010/www/");
		define("DB_USER","root");
		define("DB_PASSWORD","omandcm1");
		define("DB_DB","cen4010");
	} else {
		//for the internet
		define("DB_HOST","localhost");
        define("ROOT_URL","http://cisvm-cen-22.ccec.unf.edu/");
        define("DB_USER","team2");
		define("DB_PASSWORD","erdf3456");
		define("DB_DB","team2");
    }
	
	//turn off all warnings
	error_reporting(E_ALL ^ E_STRICT);
	
	//root base location
	if($am_i_local) {
		define("BASE",$_SERVER['DOCUMENT_ROOT']."/cen4010/www/");
	} else {
		define("BASE",$_SERVER['DOCUMENT_ROOT']."/");
	}
	
?>
