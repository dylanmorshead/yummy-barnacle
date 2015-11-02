<?php

include("header.php");
include("config.php");
// lets list the last 10 items <3

printf("<center><h4>Last 10 Uploaded Videos</h4>");


$connection=mysqli_connect($GLOBALS["domain"], $GLOBALS["username"], $GLOBALS["password"], "videos");

// lets check the connection

if(mysqli_connect_errno()){
	echo "Failed to connect to MYSQL: " . mysqli_connect_error();
}else{;
	// now lets display theh data
	$result = mysqli_query($connection, "SELECT * FROM videos ORDER BY id DESC LIMIT 5");
	while($row = mysqli_fetch_array($result)){
		echo "<br>";
		echo "Video Title: " . $row["video"] . "<br>";
		// echo "Video Description:" . "<br>". $row["description"] . "<br>"; don't think we need this
		$filename = $row["filename"];
		printf("<a href = './video.php?video=./videos/$filename'><video width = '500' controls> <source src = './videos/$filename'  type='
		video/mp4'>");
		printf("Your browser does not support HTML5 video.");
		printf("</video></a>");
		echo "<br>";
	}
}
		
mysqli_close($connection);
echo "</center>";
?>