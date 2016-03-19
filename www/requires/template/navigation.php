<nav>
		<?php
			//get all pages in the database
			$pages_array = Page::find_all();
			
			//display static pages
			echo 'Public Pages<br />';
			echo "<a style=\"padding-left:1.45em;\" href=\"index.php\">Home Page</a><br />";
			echo "<a style=\"padding-left:1.45em;\" href=\"search_pets.php\">Search Pets</a><br />";
			
			//display dynamic pages
			foreach($pages_array as $value) {
				if($value != '1' && $value != '2') { //excludes home page and about us page
					echo "<a style=\"padding-left:1.45em;\" href=\"view_page.php?page_wk=" . $value->page_wk . "\">" . 
							$value->name . "</a><br />";
				}
			}
			
			//display other static page
			echo "<a style=\"padding-left:1.45em;\" href=\"view_page.php?page_wk=2\">About Us</a><br />";
		
			// guest-only page
			if (!$session->is_logged_in) {
				echo "<a href=\"login.php\">Login</a><br />";
			}
			else 
			{
				// Admin and Staff Only Pages 
				if (is_admin_or_staff()) {
					echo 'Admin/Staff Only<br />';
					echo "<a style=\"padding-left:1.45em;\" href=\"admin_create_page.php\">Create a New Page</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"manage_breeds.php\">Manage Breeds and Types</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"manage_colors.php\">Manage Colors</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"admin_manage_vaccinations.php\">Manage Vaccinations</a><br />";
					echo "<a style=\"padding-left:1.45em;\" href=\"admin_manage_website_settings.php\">Manage Web Site Settings</a><br />";
				}
			
			
				// all logged in users
				echo 'Users Only<br />';
				echo "<a style=\"padding-left:1.45em;\" href=\"delete_user.php\">Delete my Account</a><br />";
				echo "<a style=\"padding-left:1.45em;\" href=\"update_user.php\">Update my Account</a><br />";
				echo "<a style=\"padding-left:1.45em;\" href=\"member1.php\">Member 1</a><br />";
				echo "<a style=\"padding-left:1.45em;\" href=\"logout.php\">Logout</a><br />";
			}
			
			
		?>	
</nav>