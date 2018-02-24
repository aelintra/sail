<?php

$sysloghd = file("/var/log/syslog");
$lastScanRec = file("/opt/sark/cache/last_syslog");
preg_match ('/^(\w+\s*\d+\s*\d{2}:\d{2}:\d{2})/',$lastScanRec[0],$matches);
$lastScanDate = $matches[1];
echo "Begin processing\nLast scan timestamp -> $lastScanDate \n";

try {
  $db = new PDO("sqlite:/opt/sark/db/sark.db");
} catch (PDOException $e) {
  die("Failed to get DB handle: " . $e->getMessage() . "\n");
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$timestamp = NULL;
foreach ($sysloghd as $row ) {

		preg_match ('/^(\w+\s*\d+\s*\d{2}:\d{2}:\d{2})/',$row,$matches);
		$timestamp = $matches[1];
		if ($timestamp <= $lastScanDate) {
			continue;
		}
				
		$lastrec = $row;
		$tuple = array(
			"pkey" => NULL,
			"asn" => 'unknown',
			"firstseen" => 'never',
			"hits" => '1',
			"isp" => 'unknown',
			"lastseen" => 'never',		
			"loc" => 'unknown'
		);
		
		if (!preg_match('/Shorewall/',$row)) {
			continue;
		}
		
		preg_match ('/SRC=(\d+\.\d+\.\d+\.\d+)/',$row,$matches);
		$ipAddr = $matches[1];
		
		
		try {
			$sql = $db->prepare("SELECT pkey,hits,lastseen FROM threat WHERE  pkey = '" . $ipAddr . "'");
			$sql->execute();
		} catch (Exception $e) {
			logIt("retrieval failed");
		}
		
		$thisRow = $sql->fetchObject();
		$hits=1;
		if (!empty($thisRow->pkey)) {
			if (isset($thisRow->hits)) {
				$hits = $thisRow->hits;
				$hits++;
			}
			
			$sql = $db->prepare("UPDATE threat SET hits=?,lastseen=? WHERE pkey = ?"); 
			$sql->execute(array($hits,$timestamp,$ipAddr));	
			continue;
		}
		
		
		$url = "ipinfo.io/" . $ipAddr ;
  		$ch = curl_init();
  		curl_setopt($ch, CURLOPT_URL, $url);
  		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  		$json = curl_exec($ch);
  		$jobj = json_decode($json);
  		if (isset($jobj->org)) {
  			$asnarray = explode (" ", $jobj->org,2);
			$tuple['asn'] = $asnarray[0];
			$tuple['isp'] = $asnarray[1];
		}
		if (isset($jobj->country)) {
			$tuple['loc'] = $jobj->country;
		}
		$tuple['lastseen'] = $timestamp;
		$tuple['firstseen'] = $timestamp;
		$tuple['pkey'] = $ipAddr;
		
		try {
			$sql = $db->prepare("INSERT INTO threat (pkey,asn,firstseen, hits,isp,lastseen,loc) VALUES (?,?,?,?,?,?,?)");
			$sql->execute (array($tuple['pkey'], $tuple['asn'], $tuple['lastseen'], $tuple['hits'], $tuple['isp'],$tuple['lastseen'],$tuple['loc']));
		} catch (Exception $e) {
			logIt("INSERT of $ipAddr into table threat failed");
			die ("INSERT of $ipAddr into table threat failed for $e");
		}
		
		sleep(1);
		
	}
	echo "updating timestamp -> $timestamp\n";
 	`echo $timestamp > /opt/sark/cache/last_syslog`;
exit;

function logIt($someText) {
  if (isset($_SERVER["REMOTE_ADDR"])) {
  	syslog(LOG_WARNING, $_SERVER["REMOTE_ADDR"] . " " . $someText . "\n");
  }
  else {
  	syslog(LOG_WARNING, "BATCH LOGSCAN " . $someText . "\n");
  }	
}
