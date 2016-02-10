</div>
                  
			<?php
			 	
				//if there is a right-column, display it here
				if (isset($right_nav)) {
					echo "<!---------- Right Column ---------->";
					echo "<div class=\"col-md-3\">";
					echo $right_nav;
					echo "</div>";
					
					echo "</form>";
				}
					
			 ?>
			 
        </div>
        <!---------- End Container ---------->

    </body>

</html>


<?php	
	
	//close connection
	$database->close_connection();

?>