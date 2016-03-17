<nav>
		<!-- Common Pages -->
		<a href="index.php">Homepage</a><br />
		
		
		<?php
			// get all of the pages in the database and create
			// links for them in the nav section
			$pages_array = Page::find_all();
			foreach($pages_array as $value)
			{
				echo "<a href=\"view_page.php?page_wk=" . $value->page_wk . "\">" . 
						$value->name . "</a><br />";
			}
		
			// Guest Only Pages
			if (!$session->is_logged_in)
			{
				echo "<a href=\"login.php\">Login</a><br />";
			}
			else 
			{
				// Admin and Staff Only Pages 
				if ($user->role_wk == "2" || $user->role_wk == "3")
				{
					echo "<a href=\"admin_create_page.php\">Create a New Page</a><br />";
					echo "<a href=\"manage_colors.php\">Manage Colors</a><br />";
					echo "<a href=\"manage_breeds.php\">Manage Breeds and Types</a><br />";
				}
			
			
				// Users, Admin and Staff Only Pages
				echo "<a href=\"manage_account.php\">Manage Your Account</a><br />";
				echo "<a href=\"member1.php\">Member 1</a><br />";
				echo "<a href=\"logout.php\">Logout</a><br />";
			}
			
		?>
		
</nav> <br />