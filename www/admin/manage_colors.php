<?php

	//require the framework
	require_once "../requires/initialize.php";
	
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
		
		// check if any pets are referencing this color key
		// if they are, reassign them to 'color_wk' = 0
		// which should be reserved for default "undefined" color
		while ($pet = Pet::find_by_name($delete_color->color_wk, "color_wk"))
		{
			$failed = false;  // tracks if any of the color reassignments were not successful
			$pet->color_wk = 0;
			if ($pet->save())
			{
				$session->message($session->message. $pet->name . "'s color changed to " . $pet->color_wk . ". ");
			}
			else
			{
				$session->message($session->message. "Unable to reassign " . $pet->name . "'s color at this time. ");
				$failed = true;
			}
		}
		
		// try to delete the color
		if (!$failed) // if all color reassignments were successful
		{
			if ($delete_color->delete())
			{
				$session->message($session->message."The color " . $delete_color->name . " was successfully deleted! ");
				redirect_head(ROOT_URL."admin/manage_colors.php");
				die();
			}
			else
			{
				$session->message("The color " . $delete_color->name . " cannot be deleted at this time. ");
			}
		}
		else // if there were unsuccessful color reassignments
		{
			$session->message($session->message. $pet->name . "'s color needs to be reassigned before this color can be deleted. ");
		}
	}
	
	
	// if renaming a color and/or adding a new color
	if (isset($_POST["submit"]))
	{	

		//debug
		foreach ($_POST as $value)
		{
			echo $value . "<br />";
		}

		// if there are differences between the actual color name and the submitted
		// color name, update to the new color name.
		$colors_array = Color::find_all();
		$count = count($colors_array); 
		for($i = 0; $i < $count; $i++)
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
					$colors_array[$i]->name = $_POST["{$i}"];
					if($colors_array[$i]->save())
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
					redirect_head(ROOT_URL."admin/manage_colors.php");
				}
				else
				{
					$session->message($session->message."The color " . $_POST["new_color"] . " cannot be added at this time. ");
				}
			}
		}
		
		// redirect for colors(s) were updated but no new color added
		redirect_head(ROOT_URL."admin/manage_colors.php");
	}
	
	
	//header template
	require_once ("../requires/template/header.php");
	
?>	
	<section id="registration" class="container"><form class="center" role="form" action="<?php echo file_name_with_get(); ?>" method="post" ><fieldset class="registration-form">
		<?php
		$colors_array = Color::find_all();
		$count = count($colors_array); 
		
		for ($i = 0; $i < $count; $i++){
			echo "<div class=\"form-group\"><input type=\"text\" class=\"form-control\" name=\"" . $i . "\" value=\"" . $colors_array[$i]->name . "\">";
			echo "<a href=\"manage_colors.php?delete_color_wk=" . $colors_array[$i]->color_wk . "\">Delete</a> </div>";
		}
		?>
		<br>
		Add new color:<input type="text" name="new_color" class="form-control" value=""><br />
		<div class="form-group"><button type="submit" value="submit" name="submit" class="btn btn-success btn-md btn-block">Submit</button></div>
	</fieldset></form></section>
	
	
<?php

	//this is a special instance, remove the message, if it's set since we set the messages in this form
	$session->unset_variable('message');
	
	//footer template
	require_once "../requires/template/footer.php";
	
?>