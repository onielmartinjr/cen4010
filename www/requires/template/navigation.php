<nav>
		<?php
			// get all of the pages in the database and create
			// links for them in the nav section
			$pages_array = Page::find_all();
			
			//display static pages
			echo 'Public Pages<br />';
			echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."index.php\">Home Page</a><br />";
			echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."search_pets.php\">Search Pets</a><br />";
			
			//display dynamic pages
			foreach($pages_array as $value) {
				if($value != '1' && $value != '2') { //excludes home page and about us page
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."view_page.php?page_wk=" . $value->page_wk . "\">" . 
							$value->name . "</a><br />";
				}
			}
			
			//display other static page
			echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."view_page.php?page_wk=2\">About Us</a><br />";
		
			// Guest Only Pages
			if (!$session->is_logged_in)
			{
				echo "<a href=\"login.php\">Login</a><br />";
			}
			else 
			{
				// Admin and Staff Only Pages 
				if (is_admin_or_staff()) {
					echo 'Admin/Staff Only<br />';
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/create_page.php\">Create a New Page</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/add_pet.php\">Add a New Pet</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_breeds.php\">Manage Breeds and Types</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_colors.php\">Manage Colors</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_pet_status.php\">Manage Pet Status</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_vaccinations.php\">Manage Vaccinations</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."admin/manage_website_settings.php\">Manage Web Site Settings</a><br />";
				}
			
			
				// all logged in users
				echo 'Users Only<br />';
				echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/delete_user.php\">Delete my Account</a><br />";
				echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/update_user.php\">Update my Account</a><br />";
				echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/member1.php\">Member 1</a><br />";
				echo "<a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."user/logout.php\">Logout</a><br />";
			}
			
			
		?>	
</nav>
