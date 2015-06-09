<?php
	require "../srkDbClass";
    require_once "../srkHelperClass";
    
    $distro = array();  
        
	$dbh = DB::getInstance();
	$helper = new helper;
	$helper->qDistro($distro);
	
	$helper->logit( "I'm sending greetings ",3 );
	
	$dir = "";
	$user =  $_SERVER['REMOTE_USER'];
	if ($_SERVER['REMOTE_USER'] != 'admin') {
		$res = $dbh->query("SELECT cluster FROM user where pkey = '" . $_SERVER['REMOTE_USER'] . "'")->fetch(PDO::FETCH_ASSOC);
		if (!empty ($res['cluster']) && $res['cluster'] != 'default') {
			$dir = $res['cluster'] . "/";
		}
	}
	
	$search = $distro['soundroot'] . "asterisk/sounds/" . $dir;
	$helper->logit("I'm searching $search for greetings ",3 );
	echo "{" . PHP_EOL;	
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
