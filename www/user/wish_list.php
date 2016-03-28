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
					
	
	// SQL to get all pets on user's wishlist
	$sql =  "SELECT p.* FROM `".Pet::$table_name."` AS 	`p` ";
	$sql .= "INNER JOIN `".Pet_Wish_List::$table_name."` as `w` ON `w`.`pet_wk` = `p`.`pet_wk` ";
	$sql .= "WHERE `w`.`user_wk` = ".$session->user_wk.";";
	
	// display the pet table
	 $page->body = display_pet_table($sql, true);
	 
	 // include the header
	 require_once "../requires/template/header.php";
	 
	 // display the page
	 echo $page->body;

?>

	

<?php
	
	// remove the message
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>