<?php

	/*
		Defines the Watch_List class.
		**NOTE: make sure to always make the first item in $db_fields as the primary_key
	*/

class Watch_List extends Database_Object {
	
	protected static $table_name = 'watch_list';
	protected static $db_fields = array('watch_list_wk', 'user_wk', 'name',
										'last_update_dt', 'create_dt');
	
	public $watch_list_wk;
	public $user_wk;
	public $name;
	public $last_update_dt;
	public $create_dt;
	
	
	public static function is_watch_list_eligible($pet_wk, $watch_list_wk) {
		global $session;
		global $database;
		
		$pet = Pet::find_by_id($pet_wk);
		$watch_list_detail = Watch_List_Detail::find_by_sql("SELECT * FROM `watch_list_detail` WHERE `watch_list_wk` = ".$watch_list_wk.";");
		
		//echo '<pre>' . var_export($watch_list_detail, true) . '</pre>'; //debug
		
		//generate the dynamic MySQL statement
		$sql = "SELECT `p`.* FROM `pet` AS `p` ";
		$sql .= "INNER JOIN `breed` AS `b` ON `b`.`breed_wk` = `p`.`breed_wk` ";
		$sql .= "INNER JOIN `pet_type` AS `pt` ON `pt`.`pet_type_wk` = `b`.`pet_type_wk` ";
		$sql .= "WHERE `p`.`is_deleted` = 0 AND `p`.`pet_wk` = ".$pet_wk." ";
		//loop through each criteria, determine if it's eligible
		foreach($watch_list_detail AS $item) {
			//if one of the min or max fields
			if(in_array($item->column_name,array('age_min','age_max','weight_min','weight_max'))) {
				if($item->column_name == 'age_min')
					$sql .= "AND `age` >= ".$item->value." ";
				else if($item->column_name == 'age_max')
					$sql .= "AND `age` <= ".$item->value." ";
				else if($item->column_name == 'weight_min')
					$sql .= "AND `weight` >= ".$item->value." ";
				else if($item->column_name == 'weight_max')
					$sql .= "AND `weight` <= ".$item->value." ";
			} else {
				//not one of the min or max fields
				$sql .= "AND ".($item->column_name == 'pet_type' ? '`pt`' : '`p`').".`".$item->column_name."_wk` = ".$item->value." ";
			}
		}
		$sql .= ";";
		
		$result = Pet::find_by_sql($sql);
		
		//if the count of animals returned is 1, return true
		//else, return false
		if(count($result) == 1)
			return true;
		else
			return false;
	}
	
}

function initiate_watch_list($pet_wk=0, $type="") {
	global $session;
	global $database;
	global $am_i_local;
	
	//first thing's first, get a list of all watch lists that are eligible
	$all_eligile_watch_lists = Watch_List::find_by_sql("SELECT DISTINCT `wl`.* FROM `user` AS `u` 
		INNER JOIN `watch_list` AS `wl` ON `wl`.`user_wk` = `u`.`user_wk`
		WHERE `u`.`is_deleted` = 0 AND `u`.`is_notifications_enabled` = 1;");
	
	//we retain an array of all user_wk's that are eligible to receive an e-mail
	$temp_users = array();
		
	//now that we have a list of all of them, we need to loop through each item
	//and we determine wether or not there are any people who should receive an e-mail
	foreach($all_eligile_watch_lists AS $all_eligile_watch_lists) {
		if(Watch_List::is_watch_list_eligible((int)$pet_wk, (int)$all_eligile_watch_lists->watch_list_wk)) {
			$temp_users[] = $all_eligile_watch_lists->user_wk;
		}
	}
	
	//by this point, we have a list of all users that are eligible to receive an e-mail
	//now, we need to de-dupe the list by people in case someone is in the list more than once
	$temp_users = array_unique($temp_users);
	
	//at this point, we have a list of all users to send an e-mail to
	foreach($temp_users AS $user) {
		//compose the email
			//only if we're not in a local environment
			if(!$am_i_local) {
				$to = $user->email_address;
				$subject = "Watch List Alert!";

				$message = "
				<html>
					<head>
						<title>".$subject."</title>
					</head>
					<body>
						<p><strong>".$user->username."</strong>, we have an ".$type." pet that meets at least one of your Watch Lists!</p>
						<p>Please the link below to view the pet!</p>
						<p><a href=\"".ROOT_URL."view_pet.php?pet_wk=".$pet_wk."\">".ROOT_URL."rview_pet.php?pet_wk=".$pet_wk."</a></p>
						<br /><br />
						<p>If you no longer wish to receive these e-mails, you can either delete your watch list or opt out of all e-mail notifications through your account.</p>
					</body>
				</html>
				";

				// Always set content-type when sending HTML email
				$headers = "MIME-Version: 1.0" . "\r\n";
				$headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";

				// More headers
				$headers .= 'From: <support@pet_adoption.com>' . "\r\n";
			
				//send out the email
				mail($to,$subject,$message,$headers);
			}
	}
	
	//we're done!
}

?>