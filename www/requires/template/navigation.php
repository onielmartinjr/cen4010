<!DOCTYPE html>
<html lang="en">


<header class="navbar navbar-inverse navbar-fixed-top wet-asphalt" role="banner">
	<div class="container">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
				<span class="sr-only">Toggle navigation</span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
				<span class="icon-bar"></span>
			</button>
			<a class="navbar-brand" href="<?php echo ROOT_URL; ?>">
				<img src="<?php echo ROOT_URL; ?>UI-links/images/logo2.png" alt="logo" style="margin-top: -12px;"><strong style="color:white; ">
				<?php if(isset($website_settings['organization_name'])) echo $website_settings['organization_name']; else echo 'Pet Love';?>
			</strong></a>
		</div>
		<div class="collapse navbar-collapse">
			<ul class="nav navbar-nav navbar-right">


			<?php
				// get all of the pages in the database and create
				// links for them in the nav section
				$pages_array = Page::find_all();
				
				//display static pages
				//echo 'Public Pages<br />';
				echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."index.php\">Home</a></li>";
				echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."search_pets.php\">Search Pets</a></li>";
				
				//display dynamic pages
				echo "<li class=\"dropdown\"><a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"> Pages <i class=\"icon-angle-down\"></i></a><ul class=\"dropdown-menu\">";
				foreach($pages_array as $value) {
					if($value != '1' && $value != '2') { //excludes home page and about us page
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."view_page.php?page_wk=" . $value->page_wk . "\">" . 
								$value->name . "</a></li>";
					}
				}
				echo "</ul></li>";
				
				//display other static page
				echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."view_page.php?page_wk=2\">About Us</a></li>";

				
				// Guest Only Pages
				if (!$session->is_logged_in)
				{
					echo "<li><a href=\"login.php\">Login</a></li>";
				}
				else 
				{
					
					// all logged in users
					echo "<li class=\"dropdown\"><a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"> Users <i class=\"icon-angle-down\"></i></a><ul class=\"dropdown-menu\">";
					echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/manage_watch_lists.php\">Watch Lists</a></li>";
					echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/wish_list.php\">Wish List</a></li>";
					echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/delete_user.php\">Delete my Account</a></li>";
					echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/update_user.php\">Update my Account</a></li>";
					echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/logout.php\">Logout</a></li>";
					echo "</ul></li>";
					
					// Admin and Staff Only Pages 
					if (is_admin_or_staff()) {
						echo "<li class=\"dropdown\"><a href=\"#\" class=\"dropdown-toggle\" data-toggle=\"dropdown\"> Admin/Staff <i class=\"icon-angle-down\"></i></a><ul class=\"dropdown-menu\">";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/create_page.php\">Add a New Page</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/add_pet.php\">Add a New Pet</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_breeds.php\">Manage Breeds and Types</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_colors.php\">Manage Colors</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_pet_status.php\">Manage Pet Status</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_vaccinations.php\">Manage Vaccinations</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_website_settings.php\">Manage Web Site Settings</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/report.php\">Report</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/search_users.php\">Search Users</a></li>";
						
						echo "</ul></li>";
					}
					
					if(isset($session->user_wk)){
						$user = User::find_by_id($session->user_wk);
						echo "<li><a style=\"padding-left:1.45em; color:#2ecc71;\"> ".$user->username." </a></li>";
					}					
				}
			?>	
			
			</ul>
		</div>
	</div>
</header>


