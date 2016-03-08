<?php

	//require the framework
	require_once "requires/initialize.php";
	
	// create the page
	$page = new Page();
	$page->name = "Color Management";
	$page->is_admin_only = true;
	// this page allows ADMINs and STAFFs to view colors, create new colors,
	// update current colors and delete current colors
	
	
	// if deleting a color
	if (isset($_GET["delete_color_wk"]))
	{
		$delete_color = Color::find_by_id($_GET["delete_color_wk"]);
		if($delete_color->delete())
		{
			$session->message($session->message."The color " . $delete_color->name . " was successfully deleted! ");
			redirect_head(ROOT_URL."manage_colors.php");
			die();
		}
		else
		{
			$session->message("The color " . $delete_color->name . " cannot be deleted at this time. ");
		}
	}
	
	
	// if renaming a color and/or adding a new color
	if (isset($_POST["submit"]))
	{
		// if there are differences between the actual color name and the submitted
		// color name, update to the new color name.
		$colors_array = Color::find_all();
		$count = count($colors_array); 
		for($i = 0; $i < $count; $i++) // i == color_wk TODO change logic. consider deleted colors and foreach not in order
		{
			if ($colors_array[$i]->name != $_POST["{$i}"])
			{
				// check if the name already exists
				if (Color::find_by_name($_POST["{$i}"]))
				{
					$session->message($session->message."The color " . $_POST["{$i}"] . " already exists and was not changed. ");
				}
				else
				{
					// update color with the new name
					$updated_color = new Color();
					$updated_color->color_wk = $colors_array[$i]->color_wk;
					$updated_color->name = $_POST["{$i}"];
					if($updated_color->save())
					{
						$session->message($session->message."The color " . $_POST["{$i}"] . " successfully updated! ");
					}
					else
					{
						$session->message($session->message."The color " . $_POST["{$i}"] . " failed to update. ");
					}
				}
			}
		}
		
		// check if a new color has been added
		if ($_POST["new_color"] != "")
		{
			// check if the color name already exists
			if (Color::find_by_name("{$_POST["new_color"]}"))
			{
				$session->message($session->message."The color " . $_POST["new_color"] . " already exists and was not added. ");
			}
			else
			{
				// add new color
				$new_color = new Color();
				$new_color->name = $_POST["new_color"];
				if ($new_color->save())
				{
					$session->message($session->message."The color " . $_POST["new_color"] . " was successfully added! ");
					redirect_head(ROOT_URL."manage_colors.php");
					die();
				}
				else
				{
					$session->message($session->message."The color " . $_POST["new_color"] . " cannot be added at this time. ");
				}
			}
		}
		
		// redirect for colors(s) were updated but no new color added
		redirect_head(ROOT_URL."manage_colors.php");
		die();
	}
	
	
	// header
	require_once "requires/template/header.php";
	
?>	
	
	<!-- form to rename and add colors -->
	<h2>Colors:</h2>
	<form id="color_management" action="manage_colors.php" method="post">
		<?php
		$colors_array = Color::find_all();
		$count = count($colors_array); 

		for($i = 0; $i < $count; $i++)
		{
			echo $i+1 . ": <input type=\"text\" name=\"" . $i . "\" value=\"" . $colors_array[$i]->name . "\">";
			echo "<a href=\"manage_colors.php?delete_color_wk=" . $colors_array[$i]->color_wk . "\">Delete</a><br />";
		}
		?>
		Add new color:<input type="text" name="new_color" value=""><br />
		<input type="submit" value="save" name="submit"/>
	</form>
	
	
<?php

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->remove_message();
	
	// footer
	require_once "requires/template/footer.php";
	
?>