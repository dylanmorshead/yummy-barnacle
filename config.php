<?php
$project = "Project Lizard";

// this is the mySQL connection information

global $domain;
$domain = "localhost";
global $username ;
$username = "root";
global $password ;
$password = "";
global $database;
$database = "comments"; // lets use comments

// this is the copyright function

function copyright(){

	printf("<br>");
	printf("<br>");
	printf("<br>");
	
	printf("%s (c) Dylan Morshead 2014", $GLOBALS['project']);
}

?>