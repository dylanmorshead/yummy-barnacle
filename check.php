<?php

include("header.php");
include("config.php");
// lets list the last 10 items <3

$user = $_POST["user"];
$pass = $_POST["pass"];

$connection=mysqli_connect($GLOBALS["domain"], $GLOBALS["username"], $GLOBALS["password"], "users");

// lets check the connection

if(mysqli_connect_errno()){
	echo "Failed to connect to MYSQL: " . mysqli_connect_error();
}else{
	$result = mysqli_query($connection, "SELECT * FROM username WHERE username ='".$user."'");
	while($row = mysqli_fetch_array($result)){
		if($row["username"] == $user){
			$salt = "sdjhsdffui3487234";
			if($row["password"] == hash('sha512', $pass . $salt)){
				echo "thanks for logging in";
				session_start();
				$_SESSION["user"] = $user;
				
			}else{
				echo "invaild password";	
			}
		}
		else{
	
			echo "invaild account";
		}
	}
	mysqli_close($connection);
}
		


?>