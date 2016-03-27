<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	$page = new Page();
	$page->name = "Manage Watch Lists";
	$page->is_user_only = true;
	
	//grab all the watch lists for this user
	$watch_lists = Watch_List::find_by_sql("SELECT * FROM `watch_list` WHERE `user_wk` = ".$user->user_wk.";");

	$page->body = "<p><a href=\"create_watch_list.php\">Add new Watch List</a></p>";
	//if there are records returned
	if($watch_lists) {
		$page->body .= "<p><em>Your current watch lists.</em><br />";
		foreach($watch_lists AS $list) {
			$page->body .= "<a href=\"update_watch_list.php?watch_list_wk=".$list->watch_list_wk."\">".$list->name."</a><br />";
		}
		$page->body .= "</p>";
	} else {
		//there are no watch lists returned
		$page->body .= "<p>You currently don't have any watch lists.</p>";
	}
		
	//header template
	require_once ("../requires/template/header.php");
		
	echo $page->body;

	//footer template
	require_once "../requires/template/footer.php";

?>