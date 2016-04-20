<?php

	require_once "requires/initialize.php";
	
	// check if the user is logged in
	if (!$session->is_logged_in)
	{
		echo "You must be logged in to add a pet to your wish list!";
	}
	else // if user is logged in
	{	
		$pwl = Pet_Wish_List::find_by_sql("SELECT * FROM `pet_wish_list` WHERE `pet_wk` = ".$_GET["p"]." AND `user_wk` = ".$session->user_wk." LIMIT 1;");
		
		// if the pet is already on the user's wish list
		if ($pwl)
		{
			
			if ($pwl[0]->delete()) // if the wish list entry was successfully deleted
			{
				echo "";
			}
			else // if the wish list entry was not successfully deleted
			{
				echo "";
			}
			
		}
		else // if the pet is not on the user's wish list, add it
		{
			$new_wish = new Pet_Wish_List();
			$new_wish->user_wk = $session->user_wk;
			$new_wish->pet_wk = $_GET["p"];
			
			if ($new_wish->save()) // if the wish list addition saved successfully
			{
				echo "";
			}
			else // if the wish list addition does not save successfully
			{
				echo "";
			}
		}
	}

?>