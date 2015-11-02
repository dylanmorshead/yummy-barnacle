<!DOCTYPE html>
<html>
<head>
</head>
<body>

<style>
ul.pagination {
    text-align:center;
    color:#829994;
}
ul.pagination li {
    display:inline;
    padding:0 3px;
}
ul.pagination a {
    color:#0d7963;
    display:inline-block;
    padding:5px 10px;
    border:1px solid #cde0dc;
    text-decoration:none;
}
ul.pagination a:hover, 
ul.pagination a.current {
    background:#0d7963;
    color:#fff; 
}
</style>

<?php

include("config.php"); // contains mySQL information and stuff



// Project Blue Lizard (C) Dylan Morshead 2014

// This video module uses HTML5 and is programmed by Dylan Morshead

if(isset($_GET["video"])){
	$video = $_GET["video"];
} // lets store the video path in a variable if it is set
else{
	$video = "";
}

// lets check if the video exists

function display_title($video){
	$con=mysqli_connect($GLOBALS["domain"],$GLOBALS["username"],$GLOBALS["password"], "videos"); 
	if (mysqli_connect_errno()) {
  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}else{
	// we have to replace part of the string for it to work xD
	$db_string = str_replace("./videos/", "", $video);
	$result = mysqli_query($con,  "SELECT * FROM videos WHERE filename='".$db_string."'");

	while($row = mysqli_fetch_array($result)) {
  		return $row["video"];
	}
	   mysqli_close($con);
	}
}

function display_comments($video){


/*	$con=mysqli_connect($GLOBALS["domain"],$GLOBALS["username"],$GLOBALS["password"],$GLOBALS["database"]); */
	// we need to retreive the comments from the video haha
	
	mysql_connect($GLOBALS["domain"],$GLOBALS["username"],$GLOBALS["password"]);
  	mysql_select_db($GLOBALS["database"]);
	
	//$s = $_GET['s'];
	//if (!$s)
	//$s = 0;
	
	if(isset($_GET['s'])){
	  //$s = 0;
	  $s = $_GET['s'];
	}else{
	  $s = 0;
	}

	// lets set how many results we want
	
	$e = 10;
	
	$next = $s + $e;
	$prev = $s - $e;
	
	$constructx = "SELECT * FROM comment WHERE video='".$video."'";
	$construct = "SELECT * FROM comment WHERE video='".$video."' LIMIT $s, $e";
	
	$run = mysql_query($constructx);
	
	$foundnum = mysql_num_rows($run);
	
	$run_two = mysql_query("$construct");
	
	if($foundnum == 0){
		printf("%s has no comments",  display_title($video));
		printf("<br>");
	}else{
		// lets display the data 
		
		while($runrows = mysql_fetch_assoc($run_two)){
			// get the data and display it xD
			
			
			if($runrows["username"] == ""){
				printf("Anonymous Andy:");
			}else{
			
			printf($runrows["username"] . ":");
			}
			printf("<br>");
			printf($runrows["comment"]); // this displays the comment
			printf("<br>");
			printf("Likes: %d Dislikes: %d <br>", $runrows["likes"], $runrows["dislikes"]);
			printf("<br>");	
		}
		
			printf("<br>");
		
		if (!$s<=0)
 		echo "<a href='video.php?video=$video&s=$prev'>Prev</a>";
		

		$i =1; 
		for ($x=0;$x<$foundnum;$x=$x+$e)
		{
			echo " <a href='video.php?video=$video&s=$x'>$i</a> ";
			$i++;
		}

		if ($s<$foundnum-$e){
  			echo "<a href='video.php?video=$video&s=$next'>Next</a>";
		}
		
		printf("<br>");
	}
}
function display_information($video){
	$con=mysqli_connect($GLOBALS["domain"],$GLOBALS["username"],$GLOBALS["password"], "videos"); 
	if (mysqli_connect_errno()) {
  		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}else{
	// we have to replace part of the string for it to work xD
	$db_string = str_replace("./videos/", "", $video);
	$result = mysqli_query($con,  "SELECT * FROM videos WHERE filename='".$db_string."'");
	echo "<br>Video Information:<br>";
	while($row = mysqli_fetch_array($result)) {
  		echo "Video Name: " . $row["video"]. "<br><br>";
		echo "Video Description: <br>";
		echo $row["description"];
	}
	   mysqli_close($con);
   }

}
if(file_exists($video)){
	// display the video information and maybe connect to a database
	// using the functions display information and display comments
	echo "<title>" . $project . " - " .display_title($video) . "</title>";
	echo "<h1>" . display_title($video) . "</h4>";
	printf("<video width = '500' controls> <source src = '$video' type='
	video/mp4'>");
	printf("Your browser does not support HTML5 video.");
	printf("</video>");
	printf("<br>");
	display_information($video);
	printf("<p>Comments : </p>");
	display_comments($video);
?>


</body>
<br>
<form name = "input" action = "comment.php" method = "post">
<input type="hidden" name="video" value="<?php 
	echo $_GET["video"];
?>" />
<textarea name = "comment" rows="5" cols="60"></textarea><br>
<input type = "submit" value = "Post Comment">
</form>
<?php
copyright();
?>
<?php

}
else{
	printf("Error invaild filename: %s", $video);
}

// lets call the copyright function
?>

</html>
