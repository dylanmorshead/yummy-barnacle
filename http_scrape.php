<?php	
	error_reporting(0);
	// SimpleScrape(C) Dylan Morshead 2014
	
	// Usage: 
	// for fancy print out http_scrape("http://bttracker.crunchbanglinux.org:6969/announce", "D38701A001AEBFE872B45C8C6FF49946D6B0EC13", "yes");
	// for just the return values 
	// for fancy print out http_scrape("http://bttracker.crunchbanglinux.org:6969/announce", "D38701A001AEBFE872B45C8C6FF49946D6B0EC13", "no");
	
	// took this code, can't remember where i got this
	// from some bencode function



	
	function bdecode($s, &$pos=0) {
		if($pos>=strlen($s)) {
			return null;
		}
		switch($s[$pos]){
		case 'd':
			$pos++;
			$retval=array();
			while ($s[$pos]!='e'){
				$key=bdecode($s, $pos);
				$val=bdecode($s, $pos);
				if ($key===null || $val===null)
					break;
				$retval[$key]=$val;
			}
			$retval["isDct"]=true;
			$pos++;
			return $retval;
	
		case 'l':
			$pos++;
			$retval=array();
			while ($s[$pos]!='e'){
				$val=bdecode($s, $pos);
				if ($val===null)
					break;
				$retval[]=$val;
			}
			$pos++;
			return $retval;
	
		case 'i':
			$pos++;
			$digits=strpos($s, 'e', $pos)-$pos;
			$val=(int)substr($s, $pos, $digits);
			$pos+=$digits+1;
			return $val;
	
	//	case "0": case "1": case "2": case "3": case "4":
	//	case "5": case "6": case "7": case "8": case "9":
		default:
			$digits=strpos($s, ':', $pos)-$pos;
			if ($digits<0 || $digits >20)
				return null;
			$len=(int)substr($s, $pos, $digits);
			$pos+=$digits+1;
			$str=substr($s, $pos, $len);
			$pos+=$len;
			return (string)$str;
		}
		return null;
}

	function bencode(&$d){
		//error_reporting(0);
		if(is_array($d)){
			$ret="l";
			if($d["isDct"]){
				$isDict=1;
				$ret="d";
				//this is required by the specs, and BitTornado actualy chokes on unsorted dictionaries
				ksort($d, SORT_STRING);
		}
			foreach($d as $key=>$value) {
				if($isDict){
					// skip the isDct element, only if it's set by us
					if($key=="isDct" and is_bool($value)) continue;
					$ret.=strlen($key).":".$key;
				}
				if (is_string($value)) {
					$ret.=strlen($value).":".$value;
				} elseif (is_int($value)){
					$ret.="i${value}e";
				} else {
					$ret.= bencode ($value);
				}
			}
			return $ret."e";
		} elseif (is_string($d)) // fallback if we're given a single bencoded string or int
			return strlen($d).":".$d;
		elseif (is_int($d))
			return "i${d}e";
		else 
			return null;
	}
	
	function bdecode_file($filename){
		$f=file_get_contents($filename, FILE_BINARY);
		return bdecode($f);
	}


		
	// udp scrape, to scrape udp torrents
	
	
	function udp_scrape($scrape_url,$infohash, $fancy = null){
	
		$maxreadsize = 4096; // max read size is 4096 bytes
		$timeout = 2; // times out after two seconds
		try{
			if(!is_array($infohash)){ 
				$infohash = array($infohash); 
			}
			foreach($infohash as $hash){
				if(!preg_match('#^[a-f0-9]{40}$#i',$hash)){ 
					throw new Exception('Invalid infohash: ' . $hash . '.'); 
				}
			}
			if(count($infohash) > 74){ 
				throw new Exception('Too many infohashes provided.'); 
			}
			if(!preg_match('%udp://([^:/]*)(?::([0-9]*))?(?:/)?%si', $scrape_url, $m)){ 
				throw new Exception('Invalid tracker scrape_url.'); 
			}
			$tracker = 'udp://' . $m[1];
			$port = isset($m[2]) ? $m[2] : 80;
			
			$transaction_id = mt_rand(0,65535);
			$fp = fsockopen($tracker, $port, $errno, $errstr);
			if(!$fp){
				throw new Exception('Could not open UDP connection: ' . $errno . ' - ' . $errstr,0,true); 
			}
			stream_set_timeout($fp, $timeout);
			
			$current_connid = "\x00\x00\x04\x17\x27\x10\x19\x80";
			
			//Connection request
			$packet = $current_connid . pack("N", 0) . pack("N", $transaction_id);
			fwrite($fp,$packet);
			
			//Connection response
			$ret = fread($fp, 16);
			if(strlen($ret) < 1){ 
				throw new Exception('No connection response.'); 
			}
			if(strlen($ret) < 16){ 
				throw new Exception('Too short connection response.'); 
			}
			$retd = unpack("Naction/Ntransid",$ret);
			if($retd['action'] != 0 || $retd['transid'] != $transaction_id){
				throw new Exception('Invalid connection response.');
			}
			$current_connid = substr($ret,8,8);
			
			//Scrape request
			$hashes = '';
			foreach($infohash as $hash){ 
				$hashes .= pack('H*', $hash); 
			}
			$packet = $current_connid . pack("N", 2) . pack("N", $transaction_id) . $hashes;
			fwrite($fp,$packet);
			
			//Scrape response
			$readlength = 8 + (12 * count($infohash));
			$ret = fread($fp, $readlength);
			if(strlen($ret) < 1){
				throw new Exception('No scrape response.',0,true); 
			}
			if(strlen($ret) < 8){ 
				throw new Exception('Too short scrape response.'); 
			}
			$retd = unpack("Naction/Ntransid",$ret);
			// Todo check for error string if response = 3
			if($retd['action'] != 2 || $retd['transid'] != $transaction_id){
				throw new Exception('Invalid scrape response.');
			}
			if(strlen($ret) < $readlength){ 
				throw new Exception('Too short scrape response.'); 
			}
			$torrents = array();
			$index = 8;
			foreach($infohash as $hash){
				$retd = unpack("Nseeders/Ncompleted/Nleechers",substr($ret,$index,12));
				$retd['infohash'] = $hash;
				$torrents[$hash] = $retd;
				$index = $index + 12;
				
				// now lets return the data in a easy to read format
				if($fancy === "yes"){
				print("<h3> UDP: Torrent Information </h3>");
				print("Leechers: " . $torrents[$hash]['leechers']);
				print("<br>");
				print("Seeders: " . $torrents[$hash]['seeders']);
				print("<br>");
				print("Infohash :" . $hash);
				print("<br>");
				print("Tracker: " . $scrape_url);
			}
			
			else{
				//return($torrents);
				
				$torrent_information = array($torrents[$hash]['leechers'], $torrents[$hash]['seeders']);
						
				return $torrent_information;
			}
			
		}
			
		}catch(Exception $e){
		// lets log the error
		// and save it to the server
		print("<br>Error: " . $e->getMessage());
		file_put_contents("log.txt", $e->getMessage() . "\r\n", FILE_APPEND | LOCK_EX);
		
}
	}
	
		
	// http scrape, to scrape http torrents
		
	function http_scrape($scrape_url, $infohash, $fancy = null){
		
			$maxreadsize = 4096; // max read size is 4096 bytes
			$timeout = 2; // times out after two seconds
		
			try{
			if(!is_array($infohash)){
			// lets convert the infohash into an array
			// if it is currently not an array
			$infohash = array($infohash);
			}
			// loops though the info hash to check if it is a correct hash
			foreach($infohash as $hash){
				if(!preg_match('#^[a-f0-9]{40}$#i',$hash)){
					print("Invaild infohash: " . $hash);
					 throw new Exception("Invaild infohash");
				}
			}
			$scrape_url = trim($scrape_url);
			
			if(preg_match('%(http://.*?/)announce([^/]*)$%i', $scrape_url, $m) || preg_match('%(http://.*?/)scrape([^/]*)$%i', $scrape_url, $m)){
				$scrape_url = $m[1] . 'scrape' . $m[2];
			}
			
			else{
				// let the person know they importated a invaild tracker scrape_url
				throw new Exception("Invaild tracker scrape_url");
			}
			
			
			$separator = preg_match ('/\?.{1,}?/i', $scrape_url) ? '&' : '?';
			
			$requestscrape_url = $scrape_url;
			
			foreach($infohash as $hash){
				$requestscrape_url .= $separator . 'info_hash=' . urlencode(pack('H*', $hash)); 
				$separator = '&';
			}
			
			// now lets set phps timeout 
			
			ini_set('default_socket_timeout', $timeout);
			
			$connection = @fopen($requestscrape_url, 'r');
			
			// if we can't open the connection, notify tracker
			
			if(!$connection){
				throw new Exception("Couldn't open the HTTP connection");
			}
			
			stream_set_timeout($connection, $timeout);
			
			$return = '';
			$pos = 0;
			
			while(!feof($connection) && $pos < $maxreadsize){
				$return .= fread($connection, 1024);
		}
		// lets close the connection to the server
		fclose($connection);
		
		// checks if it is a vaild scrape response
		if(!substr($return, 0, 1) == 'd'){
			throw new Exception("Invaild scrape response");
		}
		else{
			$scrape_data = bdecode($return);
			$torrents = array();
			foreach($infohash as $hash){
				$ehash = pack('H*', $hash);
				if(isset($scrape_data['files'][$ehash])){
					
					if($fancy === "yes"){
					// print it in a fancy way
					print("<h3> HTTP: Torrent Information </h3>");
					print("Leechers: " . $scrape_data['files'][$ehash]['incomplete']);
					print("<br>");
					print("Seeders: " . $scrape_data['files'][$ehash]['complete']);
					print("<br>");
					print("Infohash :" . $hash);
					print("<br>");
					print("Tracker: " . $scrape_url);
					}
					else{
						// instead just return the values
						// as a array
						
						$torrent_information = array($scrape_data['files'][$ehash]['incomplete'], $scrape_data['files'][$ehash]['complete']);
						
						return $torrent_information;
					}
				}
				

				
			}
		}
	}catch(Exception $e){
		// lets log the error
		// and save it to the server
		print("<br>Error: " . $e->getMessage());
		file_put_contents("log.txt", $e->getMessage() . "\r\n", FILE_APPEND | LOCK_EX);
		
	}
}


?>