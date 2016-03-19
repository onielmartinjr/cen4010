<?php
	page_security();
?>

<!DOCTYPE html>
<html>
<head>

	<title><?php echo $page->name;?></title>
	<style>
		a:link{color:#3522f4}
		a:active{color:#3522f4}
		a:visited{color:#3522f4}
		a:hover{color:#3522f4}

	</style>

</head>
<body>

	<h1><?php echo $page->name;?></h1>
	
	<?php 
	
		//if logged in, print the username
		if($session->is_logged_in)
		{
			echo "<p>".$user->username."</p>";
		} 
		
		//include the navigation
		require_once "navigation.php"; 
	
		//display all errors if there are any set
		display_error();
		
		//if there is no error message, display a page break
		if(empty($session->message()))
			echo "<br />";
	?>