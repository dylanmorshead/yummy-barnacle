<?php
session_start();
include("config.php");

$comment = nl2br($_POST["comment"]);
$video = $_POST["video"];

// Project Blue Lizard (C) Dylan Morshead 2014

// This comment module uses HTML4 and is programmed by Dylan Morshead
	$connection = mysqli_connect($domain, $username, $password, $database);
	
	// lets check the connection
	
	if(mysqli_connect_errno()){
		printf("Failed to connect to MySQL Database: %s", mysqli_connect_errno());
	}else{
		// since there is no errors we can add the comment to the database
	
		// lets add there comment to the database xD
		
		$connection = mysqli_connect($domain, $username, $password, $database);

		if(isset($_SESSION['user'])){
			$username = $_SESSION["user"];
		}else{
			$username = "";
		}
		
		$sql = "INSERT INTO comment (username, comment, likes, dislikes, video) VALUES ('$username', '$comment', 0, 0, '$video')";
		
		if (!mysqli_query($connection,$sql)) {
			die('Error: ' . mysqli_error($connection));
		}
		printf("Comment added");
		printf("<meta http-equiv='refresh' content='0; url=./video.php?video=$video' />");
	
	}
	echo "<br><br><br><br>";
	echo nl2br($comment);


?>

