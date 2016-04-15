<?php
	
	//require the framework
	require_once "../requires/initialize.php";
	
	//construct the page
	$page = new Page();
	$page->name = "Search Users";
	$page->is_admin_only = true;
	//set the style for the table
	$page->style = "<style>
						table, th, td {
							border: 2px solid white;
							border-collapse: collapse;
						}
						th, td {
							padding: 5px;
						}
						tr:hover {background: #2ecc71 !important;}
						tr:nth-child(even) {background: #DDD}
						tr:nth-child(odd) {background: #FFF}
					</style>";
	
	//by this point, we know what the filter variables are
	//so we need to create the SQL that will reflect those changes
	//these are the function calls to generate the SQL
	//generate_user_where();
	//generate_user_order_by();
	
	//if the selection has changed, process here
	if(isset($_GET['type'])) {
		//get the value and save it, redirect
		$new_where = $_GET['type'];
				
		//set the new sort mechanism
		$session->set_variable('user_where', $new_where);
		
		//set the message
		$session->message("Your criteria was successfully changed!");
		
		//redirect back
		redirect_head(file_name_without_get());
		
	}
	
	//if the sorting method for the users resultset changed, process it here
	if(isset($_GET['toggle'])) {
		//we need to process this change
		//so first we need to see what the current sorting method is
		if(isset($session->user_order_by)) 
			$current_sort = $session->user_order_by;
		else {
			$current_sort = array();
			$current_sort['column'] = 'username';
			$current_sort['order'] = 'ASC';
		}
		
		//so now we need to set the new sort
		$new_sort = array();
		//it is the new column item from the $_GET variable
		$new_sort['column'] = $_GET['toggle'];
		
		//now we need to determine the column sort order
		if($_GET['toggle'] == $current_sort['column']) {
			//the values are equivalent, simply switch from ASC to DESC and vice-versa
			if($current_sort['order'] == 'ASC')
				$new_sort['order'] = 'DESC';
			else
				$new_sort['order'] = 'ASC';
		} else {
			//the values are not equivalent, force set to ASC
			$new_sort['order'] = 'ASC';
		}
				
		//set the new sort mechanism
		$session->set_variable('user_order_by', $new_sort);
		//redirect back
		redirect_head(file_name_without_get());
		
	}
	
	//grab the set of users to display
	$sql = "SELECT `u`.* FROM `user` AS `u` ";
	$sql .= "INNER JOIN `role` AS `r` ON `r`.`role_wk` = `u`.`role_wk` ";
	$sql .= "WHERE 1=1 ";
	$sql .= generate_user_where()." ";
	$sql .= generate_user_order_by(). " ";
	$sql .= ";";
	$users = User::find_by_sql($sql);
	
		
	//display filters
	$page->body = "<p><a href=\"".file_name_without_get()."?type=all\">All</a> | <a href=\"".file_name_without_get()."?type=users\">Active Users</a> | <a href=\"".file_name_without_get()."?type=staff\">Active Staff</a> | <a href=\"".file_name_without_get()."?type=admin\">Active Admin</a> | <a href=\"".file_name_without_get()."?type=is_deleted\">Disabled</a></p>";
	
	//only display the table with results if
	//there are more than 0 users
	if(count($users) > 0) {
		//there are users to display
		$page->body .= "<table style=\"width:100%\">
							<tr>
								<th><a href=\"".file_name_without_get()."?toggle=username\">Username</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=first_name\">First Name</a></th>		
								<th><a href=\"".file_name_without_get()."?toggle=last_name\">Last Name</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=email_address\">Email Address</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=role\">Role</a></th>
								<th><a href=\"".file_name_without_get()."?toggle=is_deleted\">Is Disabled</a></th>
							</tr>";
							
		//loop through all objects
		foreach($users as $value) {
			$page->body .= "<tr>
								<td><a href=\"".ROOT_URL."admin/update_user.php?user_wk=".$value->user_wk."\">".$value->username."</a></td>
								<td>".$value->first_name."</td>		
								<td>".$value->last_name."</td>
								<td>".$value->email_address."</td>
								<td>".$value->role_wk->name."</td>		
								<td>".($value->is_deleted == '1' ? 'Yes' : 'No')."</td>
							</tr>";
		}
							
		$page->body .= "</table>";
	}
	$page->body .= "<p><em>Your search returned ".count($users)." user(s).</em></p>";
	
	//include the header
	require_once "../requires/template/header.php";
	
	echo "<section class=\"container\">";
	//display the page
	echo $page->body;
	echo "</section>";

	//include the footer
	require_once "../requires/template/footer.php";
	
?>