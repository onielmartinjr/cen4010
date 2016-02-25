<?php
	page_security();
?>

<!DOCTYPE html>
<html>
<head>

	<title><?php echo $page->name;?></title>

</head>
<body>

	<h1><?php echo $page->name;?></h1>
	
	<?php 
		
		if($session->is_logged_in)
		{
			echo $user->username . "<br />";
		} 
	
		require_once "navigation.php"; 
	
		display_error();
	?>