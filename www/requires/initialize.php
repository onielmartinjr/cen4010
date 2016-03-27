<?php

	/*
		This page will include all the "libraries" or "classes" that we define for our site.
		I.E - this page constructs the framework for our site
	*/

	//get current functions
	require_once "functions.php";
	
	//get constants
	require_once "constants.php";
	
	//establish database connection
	require_once "database.php";
	
	//get database object class
	require_once "database_object.php";
	
	
	/*******************************
		BEGIN DATABASE CLASSES
	*******************************/
	
	require_once "database_classes/breed.php";
	require_once "database_classes/color.php";
	require_once "database_classes/comment.php";
	require_once "database_classes/image.php";
	require_once "database_classes/log.php";
	require_once "database_classes/page.php";
	require_once "database_classes/pet.php";
	require_once "database_classes/pet_type.php";
	require_once "database_classes/pet_wish_list.php";
	require_once "database_classes/reset_password.php";
	require_once "database_classes/role.php";
	require_once "database_classes/setting.php";
	require_once "database_classes/status.php";
	require_once "database_classes/user.php";
	require_once "database_classes/vaccination.php";
	require_once "database_classes/watch_list.php";
	require_once "database_classes/watch_list_detail.php";
	
	/*******************************
		END DATABASE CLASSES
	*******************************/
	
	//get session class
	require_once "session.php";
	
	//get checks
	require_once "checks.php";
	
	
?>