<?php
	page_security();
?>

<!DOCTYPE html>

<html lang="en" class="csstransforms csstransforms3d csstransitions">
<head>
	<meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    <script src="<?php echo ROOT_URL;?>ckeditor/ckeditor.js"></script>  
    <link href="<?php echo ROOT_URL;?>UI-links/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ROOT_URL;?>UI-links/css/font-awesome.min.css" rel="stylesheet">
    <link href="<?php echo ROOT_URL;?>UI-links/css/prettyPhoto.css" rel="stylesheet">
    <link href="<?php echo ROOT_URL;?>UI-links/css/animate.css" rel="stylesheet">
    <link href="<?php echo ROOT_URL;?>UI-links/css/main.css" rel="stylesheet">
    <!--[if lt IE 9]>
    <script src="js/html5shiv.js"></script>
    <script src="js/respond.min.js"></script>
    <![endif]-->       
    <link rel="shortcut icon" href="<?php echo ROOT_URL;?>UI-links/images/ico/favicon.ico">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?php echo ROOT_URL;?>UI-links/images/ico/apple-touch-icon-144-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?php echo ROOT_URL;?>UI-links/images/ico/apple-touch-icon-114-precomposed.png">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?php echo ROOT_URL;?>UI-links/images/ico/apple-touch-icon-72-precomposed.png">
    <link rel="apple-touch-icon-precomposed" href="<?php echo ROOT_URL;?>UI-links/images/ico/apple-touch-icon-57-precomposed.png">
	<script src="<?php echo ROOT_URL;?>UI-links/js/jquery.js"></script>
    <script src="<?php echo ROOT_URL;?>UI-links/js/bootstrap.min.js"></script>
    <script src="<?php echo ROOT_URL;?>UI-links/js/jquery.prettyPhoto.js"></script>
    <script src="<?php echo ROOT_URL;?>UI-links/js/main.js"></script>
	
	<title><?php echo (isset($website_settings['organization_name']) ? 
		$website_settings['organization_name'].' - ' : '').$page->name; ?></title>
	<script src="<?php echo ROOT_URL."requires/template/jquery.js"; ?>"></script>
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
	<?php
		//include the navigation
		require_once "navigation.php"; 
		
		//if this page is not the homepage, do this
		//var_dump($page);
		if($page->page_wk != 1){
			echo "<section id=\"title\" class=\"emerald\"><div class=\"container\"><div class=\"row\"><div class=\"col-sm-6\"><h3>";
			echo (isset($website_settings['organization_name']) ? 
				$website_settings['organization_name'].' - ' : '').$page->name;
			
			echo "</h3><p>";	
			//display all errors if there are any set
			display_error();
			
			echo "</p></div></div></div></section><!--/#title-->";	
			
		}
		


		//if there is no error message and this is not the homepage, display a page break
		if(empty($session->message()) && $page->name != "Home Page")
			echo "<br />";
			
	?>