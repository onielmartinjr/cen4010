<?php
	page_security();
?>

<!DOCTYPE html>
<html>
<head>

	<title><?php echo (isset($website_settings['organization_name']) ? 
		$website_settings['organization_name'].' - ' : '').$page->name; ?></title>
	<style>
		a:link{color:#3522f4}
		a:active{color:#3522f4}
		a:visited{color:#3522f4}
		a:hover{color:#3522f4}

	</style>
	<?php
		if(isset($page->style)) {
			if(!empty($page->style)) {
				echo $page->style;
			}
		}
		
		if(isset($page->script)) {
			if(!empty($page->script)) {
				echo $page->script;
			}
		}
	?>

</head>
<body>

	<h1><?php echo (isset($website_settings['organization_name']) ? 
		$website_settings['organization_name'].' - ' : '').$page->name;?></h1>
	
	<?php 
		
		//include the navigation
		require_once "navigation.php"; 
	
		//display all errors if there are any set
		display_error();
		
		//if there is no error message, display a page break
		if(empty($session->message()))
			echo "<br />";
			
	?>