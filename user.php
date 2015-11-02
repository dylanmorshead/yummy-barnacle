<?php

include("header.php");
include("config.php");
// this lists the users uploads <3
$user = $_GET["user"];


$connection=mysqli_connect($GLOBALS["domain"], $GLOBALS["username"], $GLOBALS["password"], "videos");
echo "<center>";
// lets check the connection

if(mysqli_connect_errno()){
	echo "Failed to connect to MYSQL: " . mysqli_connect_error();
}else{
	// now lets display theh data
	$result = mysqli_query($connection, "SELECT * FROM videos WHERE username = '".$user."'");
	$num_rows = mysqli_num_rows($result);
	if($num_rows == 0){
		echo "<h1>No such user: " . $user . "</br>";
	}else{

	echo "<h1>" . $user . "'s videos</h1>";
	while($row = mysqli_fetch_array($result)){
		echo "<br>";
		echo "Video Title: " . $row["video"] . "<br><br>";
		// echo "Video Description:" . "<br>". $row["description"] . "<br>"; // don't think we need this
		$filename = $row["filename"];
		printf("<a href = './video.php?video=./videos/$filename'><video width = '500' controls> <source src = './videos/$filename'  type='
		video/mp4'>");
		printf("Your browser does not support HTML5 video.");
		printf("</video></a>");
		echo "<br>";
	}
    }
}
echo "</center>";	
mysqli_close($connection);
?>