<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	$page = new Page();
	$page->name = "Manage Watch Lists";
	$page->is_user_only = true;
	
	//if we're deleting a watch list
	if(isset($_GET['delete_watch_list_wk'])) {
		//grab the watch list
		$to_delete = Watch_list::find_by_id($_GET['delete_watch_list_wk']);
		
		//if the watch list doesn't exist, error
		if(!$to_delete) { 
			//if the watch list does not exist
			$session->message("There was an error in the URL; please try again.");
		} else if($to_delete->user_wk->user_wk != $user->user_wk) {
			//if the watch list does not belong to the current user
			$session->message("You cannot delete someone else's watch list.");
		} else {
			//if we're here, we actually need to delete it
			
			//and all the entries from it
			$sql = "DELETE FROM `watch_list_detail` WHERE `watch_list_wk` = ".$to_delete->watch_list_wk.";";
			if($to_delete->delete() && $database->query($sql)) {
				$session->message("Your watch list was successfully removed!");
			} else {
				$session->message("There was an error removing the watch list entry.");
			}
		}
		
		//redirect back
		redirect_head(file_name_without_get());
	}
	
	//grab all the watch lists for this user
	$watch_lists = Watch_List::find_by_sql("SELECT * FROM `watch_list` WHERE `user_wk` = ".$user->user_wk.";");

	$page->body = "<p><a href=\"create_watch_list.php\">Add new Watch List</a></p><br>";
	//if there are records returned
	if($watch_lists) {
		$page->body .= "<p><em>Your current watch lists.</em><br />";
		foreach($watch_lists AS $list) {
			//var_dump($list);
			$page->body .= "<br /><strong>".$list->name."</strong><br />";
			$page->body .= "<a href=\"".file_name_without_get()."?delete_watch_list_wk=".$list->watch_list_wk."\" style=\"margin-left:1.5em;\" >Delete</a><br />";
		}
		$page->body .= "</p>";
	} else {
		//there are no watch lists returned
		$page->body .= "<p><em>You currently don't have any watch lists.</em></p>";
	}
		
	//header template
	require_once ("../requires/template/header.php");
		
	echo "<section id=\"blog\"><div class=\"container\"><div class=\"row\"><div class=\"col-md-12\"><div class=\"blog\"><div class=\"blog-item\"><div class=\"blog-content\">";
	echo $page->body;
	echo "</div></div></div></div></div></div></section>";

	//footer template
	require_once "../requires/template/footer.php";

?>