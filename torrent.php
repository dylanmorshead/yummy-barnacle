<?php
	require_once("http_scrape.php"); // we need this 

	// we use this to convert bytes to mb ect....
	
	function formatSizeUnits($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' KB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
}
	
	// this page gets the torrent name via link
	
	// and outputs the data
	
	// lets get the file name from the urldecode
	
	$file_name = $_GET['torrent'];
		// checks a filename to make sure it exists
	
		// declare the file path
		$test = './torrents/' . $file_name;
		// this checks to see if it exists
		if(file_exists($test)){
		
		
			// now lets continue
			$leechers = null;
			$seeders = null;
			// we need the hash to get the torrent information 
			
			$hash_filename = bdecode_file($file_name);
			
			echo "<h4>Torrent Name: " . $hash_filename['info']['name'] . "</h5>";
			
			$hash = strtoupper(sha1(bencode($hash_filename['info'])));
			
			echo "<h4> Torrent Trackers </h4>";
			
			
			foreach ($hash_filename["announce-list"] as $alist){
	
			if(strpos($alist[0], 'udp://') !== false){
		
				echo "<br>Tracker: ";
                 echo $alist[0];
				 
	
				//echo $test['info'];
				

				// echo $text;
				
				//udp_scrape($alist[0], $hash ,"yes");
	
				// udp_scrape($alist[0], "D38701A001AEBFE872B45C8C6FF49946D6B0EC13", "yes");
				// we only need the leechers and seeders so we use no
				$torrent_information = udp_scrape($alist[0], $hash, "no");
				
				$leechers += $torrent_information[0];
				$seeders += $torrent_information[1];
				 
			}else{
				echo "<br>Tracker: ";
				echo $alist[0];
				// we only need the leechers and seeders so we use no
				$torrent_information = http_scrape($alist[0], $hash, "no");
				$leechers += $torrent_information[0];
				$seeders += $torrent_information[1];
				}
			}
			echo "<br>";
			echo "<br>";
			echo "<br>";
			
			echo "<h4> Torrent Details </h5>";
			
			echo "Seeders: ". $seeders;
			echo " Leechers: ". $leechers;

			
			echo "<h4> Files in torrent </h4>";
			
			foreach ($hash_filename["info"]['files'] as $file){
				echo implode('\\',$file['path']).' - '.formatSizeUnits($file['length']) . '<br>';
			}
			
			
		}else{
			// file doesn't exist do not retrieve information
			print("Error: Torrent file doesn't exist");
		}		
	


?>