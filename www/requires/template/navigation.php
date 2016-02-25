<nav>
		<!-- Common Pages -->
		<a href="index.php">Homepage</a><br />
		<a href="public1.php">Public 1</a><br />
		
		
		<?php
		
			// Guest Only Pages
			if (!$session->is_logged_in)
			{
				echo "<a href=\"login.php\">Login</a><br />";
			}
		
			else {
				// User Only Pages
				if ($user->role_wk >= "1")
				{
					echo "<a href=\"member1.php\">Member 1</a><br />";
				}
			
			
				// Admin and Staff Only Pages 
				if ($user->role_wk == "2" || $user->role_wk == "3")
				{
					echo "<a href=\"admin1.php\">Admin 1</a><br />";
				}
			
			
				// Logout
				echo "<a href=\"logout.php\">Logout</a><br />";
			}
			
		?>
		
</nav> <br />