<?php
$con = mysqli_connect("localhost","root","","shart");

// Check connection
if (mysqli_connect_errno())
  {
  echo "Failed to connect to MySQL: " . mysqli_connect_error();
  }

//we are connected to DB
	$result = $con->query("SELECT * FROM roster;");

	if ($result->num_rows > 0) {
    	// output data of each row
	    while($row = $result->fetch_assoc()) {
    	    echo "first_name: " . $row["first_name"]. " - last_name: " . $row["last_name"]. "email: ". $row["email"]."<br>";
	    }
	} else {
    	echo "0 results";
	}
	
	$con->close();
?>