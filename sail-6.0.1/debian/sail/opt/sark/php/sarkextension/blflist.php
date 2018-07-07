<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
	
    $helper = new helper;     
	$dbh = DB::getInstance();
	
	$blflist = array(
		"None" => "None",
		"Default" => "Default",
		"blf" => "blf",
		"speed" => "speed",
		"line" => "line"
	);
	
	$pkey = $_GET['pkey'];
	$helper->logit("pkey is $pkey");
	
	$res = $dbh->query("SELECT macaddr FROM ipphone where pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	$mac = strtolower($res['macaddr']);
	$helper->logit("mac is $mac");
	
	if (preg_match(' /^0004f2/ ',$mac)) {
		unset($blflist["None"]);
		unset($blflist["speed"]);
		unset($blflist["line"]);  
	}
 							 		
	echo "{" . PHP_EOL;	 	
	foreach ($blflist as $blf ) {
		echo "'$blf':'$blf'," . PHP_EOL;
	}
	
	echo "}" . PHP_EOL; 
?>
