<?php
require_once("config.php");
$fileName = $_FILES["file1"]["name"]; // The file name
$fileTmpLoc = $_FILES["file1"]["tmp_name"]; // File in the PHP tmp folder
$fileType = $_FILES["file1"]["type"]; // The type of file it is
$fileSize = $_FILES["file1"]["size"]; // File size in bytes
$fileErrorMsg = $_FILES["file1"]["error"]; // 0 for false... and 1 for true
if (!$fileTmpLoc) { // if file not chosen
    echo "ERROR: Please browse for a file before clicking the upload button.";
    exit();
}
session_start();
$temp = explode(".",$_FILES["file1"]["name"]);
$file = $project . "_" . rand(1,99999) . rand(1,99999). rand(1,99999). rand(1,99999). '.' .end($temp);
$newfilename = str_replace(" ","_", $file);

// $_FILES["file1"]["name"] . 

if(move_uploaded_file($fileTmpLoc, "./videos/". $newfilename)){
$ext = pathinfo($fileName, PATHINFO_EXTENSION);
$allowed = array('mp4');
if(in_array( $ext, $allowed ) ){
 
   echo "$fileName upload is complete";
  // lets add this shit to a database or sumthin...
    $video = $_POST["name"];
    $keywords = $_POST["keywords"];
    $description = $_POST["description"];
    $filename = $fileName;

    // This comment module uses HTML4 and is programmed by Dylan Morshead
	$connection = mysqli_connect($domain, $username, $password, $database);
	
	// lets check the connection
	
	if(mysqli_connect_errno()){
		printf("Failed to connect to MySQL Database: %s", mysqli_connect_errno());
	}else{
		// since there is no errors we can add the comment to the database
	
		// lets add there comment to the database xD
		
		$connection = mysqli_connect($domain, $username, $password, "videos");
		if(isset($_SESSION['user'])){
			$uploader = $_SESSION["user"];
		}else{
			$uploader = "Anonymous Andy";
		}
		$sql = "INSERT INTO videos (video, keywords, description, filename, username) VALUES ('$video', '$keywords', '$description', '$newfilename', '$uploader')";
		
		if (!mysqli_query($connection,$sql)) {
			die('Error: ' . mysqli_error($connection));
		}
	
	}
  



}else{
	printf("We only support MP4 at this time");
}	

	

} else {
    echo "Upload Failed";
}
?>