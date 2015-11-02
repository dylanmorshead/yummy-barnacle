<?php
	require_once("http_scrape.php");
	
	$example = "winxp.torrent";

	$test = bdecode_file($example);
	
	// use this to get the correct hash <3 fuck yes
	
$hash = strtoupper(sha1(bencode($test['info'])));

	
	foreach ($test["announce-list"] as $alist){
	
		if(strpos($alist[0], 'udp://') !== false){
		
		echo "<br>Tracker: ";
                 echo $alist[0];
				 
	
				//echo $test['info'];
				

				// echo $text;
				
				//udp_scrape($alist[0], $hash ,"yes");
	
				// udp_scrape($alist[0], "D38701A001AEBFE872B45C8C6FF49946D6B0EC13", "yes");
				 
			udp_scrape($alist[0], $hash, "yes");
				 
				 
		}else{
			echo "<br>HTTP:";
			echo "<br>Tracker: ";
            echo $alist[0];
			
			http_scrape($alist[0], $hash, "yes");
			
			
	}
}

	
	//http_scrape("http://bttracker.crunchbanglinux.org:6969/announce", "D38701A001AEBFE872B45C8C6FF49946D6B0EC13", "yes");


	

	///
	//udp_scrape("udp://tracker.publicbt.com:80/announce", "7EF0145BC19F53251DF9FF7E11A795E9E088B65C", "yes");

	//echo $values['seeders'];
	//echo $values["D38701A001AEBFE872B45C8C6FF49946D6B0EC13"]['seeders'];
	//echo $values["D38701A001AEBFE872B45C8C6FF49946D6B0EC13"]['leechers'];
?>