<?php

	if(isset($_FILES['file_upload'])) {
		echo "<pre>";
		print_r($_FILES['file_upload']);
		echo "</pre><hr />";
	}

?>

<html>
	<head>
		<title>Uploads</title>
	</head>
	<body>
		
		<?php if(!empty($message)) { echo "<p>{$message}</p>"; } ?>
		<form action="upload.php" enctype="multipart/form-data" method="POST">
		
			<input type="hidden" name="MAX_FILE_SIZE" value="10000000" />
			<input type="file" name="file_upload" />
			
			<input type="submit" name="submit" value="Upload" />
		</form>
	</body>
</html>


<?php

/*
	$_FILE['file_upload']['name']
	$_FILE['file_upload']['type']
	$_FILE['file_upload']['size']
	$_FILE['file_upload']['tmp_name']
	$_FILE['file_upload']['error']
*/

?>