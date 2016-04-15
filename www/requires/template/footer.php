</body>
</html>
    <footer id="footer" class="midnight-blue">
        <div class="container">
            <div class="row">
                <div class="col-sm-6">
                    &copy; <a target="_blank" href="http://shapebootstrap.net/" title="Free Twitter Bootstrap WordPress Themes and HTML templates">Cen4010-Bootstrap</a>. All Rights Reserved.
                </div>
                <div class="col-sm-6">
                    <ul class="pull-right">
<?php
						//display static pages
						//echo 'Public Pages<br />';
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."index.php\">Home</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."search_pets.php\">Search Pets</a></li>";
						echo "<li><a style=\"padding-left:1.45em;\" href=\"".ROOT_URL."view_page.php?page_wk=2\">About Us</a></li>";
?>
                        <li><a id="gototop" class="gototop" href="#"><i class="icon-chevron-up"></i></a></li><!--#gototop-->
                    </ul>
                </div>
            </div>
        </div>
    </footer><!--/#footer-->
<?php

	//close connection
	$database->close_connection();

?>