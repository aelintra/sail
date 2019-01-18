<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
	require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass");
	require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass");
        
	$dbh = DB::getInstance();
	$helper = new helper;
	
	$helper->logit( "I'm sending greetings ",3 );
	
	$dir = "";
	$user =  $_SESSION['user']['pkey'];
	if ($_SESSION['user']['pkey'] != 'admin') {
		$res = $dbh->query("SELECT cluster FROM user where pkey = '" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
		if (!empty ($res['cluster']) && $res['cluster'] != 'default') {
			$dir = $res['cluster'] . "/";
		}
	}
	
	$search = "/usr/share/asterisk/sounds/" . $dir;
	$helper->logit("I'm searching $search for greetings ",3 );
	echo "{" . PHP_EOL;	
	echo "'None':'None'," . PHP_EOL;
	if ($handle = opendir($search)) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match("/^usergreeting(\d*)/",$entry,$matches)) { 
				echo "'" . $matches[1] . "':'" . $matches[1] . "'," . PHP_EOL;
			}
		}
		closedir($handle);
	}							 		
	 

	 
	echo "}" . PHP_EOL; 
?>
