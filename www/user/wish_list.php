<?php

	//require the framework
	require_once "../requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Wish List";
	$page->is_user_only = true;
	// this page allows USERs, ADMINs and STAFFs to manage the pets on their wishlist
	$page->style = "<style>
						table, th, td {
							border: 1px solid black;
							border-collapse: collapse;
						}
						th, td {
							padding: 5px;
						}
						tr:nth-child(even) {background: #DDD}
						tr:nth-child(odd) {background: #FFF}
					</style>";
					
	//set the AJAX code
	$page->script = "<script>
		function wish_list(pet, clicked_id)
		{
			var doc_root = \"".ROOT_URL."\";
			var xhttp = new XMLHttpRequest();
			xhttp.onreadystatechange = function() {
				if (xhttp.readyState == 4 && xhttp.status == 200)
				{	
					var response = xhttp.responseText.trim();
					var msg_elem = document.getElementById(\"ajax_message\");
					var wl_button = document.getElementById(clicked_id);
					
					// display the message
					msg_elem.innerHTML = response;
					
					// change the text of the button
					if (wl_button.value == \"Add to Wish List!\") // if the pet was added, change to delete
					{
						wl_button.value = \"Remove from Wish List\";
					}
					else // if the pet was deleted, remove row from the table
					{
						document.getElementById(clicked_id + \"_row\").style.display = \"none\";
					}
				}
			};
			xhttp.open(\"GET\", doc_root + \"ajax_wish_list.php?p=\" + pet, true);
			xhttp.send();
		};

		</script>";
	
	//if the sorting method for the pets resultset changed, process it here
	if(isset($_GET['toggle'])) {
		//we need to process this change
		//so first we need to see what the current sorting method is
		if(isset($session->pet_order_by)) 
			$current_sort = $session->pet_order_by;
		else {
			$current_sort = array();
			$current_sort['column'] = 'name';
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
		$session->set_variable('pet_order_by', $new_sort);
		//redirect back
		redirect_head(file_name_without_get());
	}
			

	//grab the set of pets to display
	$sql = "SELECT `p`.* FROM `pet` AS `p` ";
	$sql .= "INNER JOIN `pet_wish_list` AS `w` ON `w`.`pet_wk` = `p`.`pet_wk` ";
	$sql .= "INNER JOIN `breed` AS `b` ON `b`.`breed_wk` = `p`.`breed_wk` ";
	$sql .= "INNER JOIN `pet_type` AS `pt` ON `pt`.`pet_type_wk` = `b`.`pet_type_wk` ";
	$sql .= "INNER JOIN `status` AS `s` ON `s`.`status_wk` = `p`.`status_wk` ";
	$sql .= "INNER JOIN `color` AS `c` ON `c`.`color_wk` = `p`.`color_wk` ";
	$sql .= "WHERE `p`.`is_deleted` = 0 ";
	$sql .= "AND `w`.`user_wk` = ".$session->user_wk." ";
	$sql .= generate_pet_order_by(). " ";
	$sql .= ";";

	
	// display the pet blocks
	 $page->body = display_pet_blog($sql, true);
	 
	 // include the header
	 require_once "../requires/template/header.php";
	 
	 // temporary messages section
	 echo "<p id=\"ajax_message\" style=\"color: red; font-family: courier;\"></p>";
	 
	 // display the page
	 echo "<section class=\"container\"><div class=\"row\"><div class=\"col-xs-9\">";
	 echo $page->body;
	 echo "</div></div></section>";
	 
	
	// remove the message
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>