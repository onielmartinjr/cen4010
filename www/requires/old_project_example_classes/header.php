<!doctype html>

<html lang="en">

    <head>
        <title><?php echo $page->title; ?></title>

        <link rel="stylesheet" href="css/bootstrap.css">
        <script src="Scripts/jquery-2.1.4.js"></script>
        <script src="js/bootstrap.js"></script>
		<script>
			//makes table rows clickable
			jQuery(document).ready(function($) {
  				$(".clickable-row").click(function() {
        			window.document.location = $(this).data("href");
    			});
			});
		</script>
		<?php if (isset($script)) echo $script; ?>
    </head>

    <body>

        <!---------- Navigation ---------->
        <nav class="navbar navbar-default">
          <div class="container">
            <!-- Brand and toggle get grouped for better mobile display -->
            <div class="navbar-header">
              <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1" aria-expanded="false">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
              </button>
              <a class="navbar-brand" href="dashboard.php">Clinasyst</a>
            </div>

            <!-- Collect the nav links, forms, and other content for toggling -->
            <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
              <ul class="nav navbar-nav">
                <?php 
				
					//if you're logged in, display navigation
					if ($session->logged_in)
						Page::display_navigation(); 
						
				?>
              </ul>
              <ul class="nav navbar-nav navbar-right">
                <li><a href="logout.php">Sign out</a></li>
              </ul>
            </div><!-- /.navbar-collapse -->
          </div><!-- /.container-fluid -->
        </nav>
      
        <!---------- Page Container ---------->
        <div class="container">
		
			<?php 
			
				//if there is a go back, display it here
				if (isset($go_back)) {
					echo "<!---------- Go Back ---------->";
					echo $go_back;
				}
			
			?>

            <!---------- Page Title ---------->
            <h2 class="page-header"><?php echo $page->title; if (isset($user)) echo ', '.$user->get_name(); ?></h2>
			
			 <?php
			 	
				//if there is a left-column, display it here
				if (isset($left_nav)) {
					echo "<!---------- Left Column ---------->";
					echo "<div class=\"";
					if (isset($right_nav))
						echo "col-md-3 border-right";
					else
						echo "col-md-2 border-right";
					echo "\">";
					echo $left_nav;
					echo "</div>";
				}
					
			 ?>
			 

            <!---------- Body (Middle) ---------->
            <div class="<?php 
				
				//if there's a right-hand navigation
				if ($file_name == 'view_ticket.php')
					echo 'col-md-6 border-right';
				else if (isset($right_nav))
					echo "col-md-7 border-right";
				else 
					echo "col-md-10";
				
				
				?>">

                <!---------- Search ---------->
                <!--<div class="col-md-3 pull-right">
                    <input type="text" class="form-control input-sm" placeholder="Search...">
                </div>-->
                <!---------- End Search ---------->
			
			<?php 
					//if there's an error, display it
					display_error(); 
			?>