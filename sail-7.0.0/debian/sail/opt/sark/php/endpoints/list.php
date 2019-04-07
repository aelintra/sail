<?php

/*
 * Ajax endpoint getter
 * can be called with ?holiday=yes if you want a shorter list without externals
 * can be called with ?routes=yes if you want to give a route list
 */ 
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
    $helper = new helper;     
	$dbh = DB::getInstance();

	$helper->logit("I'm sending endpoints ",3 );
	$conferences = array();
	$user =  $_SESSION['user']['pkey'];
//	$wherestring = 'ORDER BY pkey';
	$wherestring = null;
	
	if ($_SESSION['user']['pkey'] == 'admin') {
		
	}
	else {
		$res = $dbh->query("SELECT cluster from user where pkey='" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);		
		$wherestring = "WHERE cluster='" . $res['cluster'] . "' OR cluster='default'" ;
	}
	
	$handle = fopen("/etc/asterisk/sark_meetme.conf", "r") or die('Could not read file!');

	while (!feof($handle)) {
		
		$row = trim(fgets($handle));
		
		if (preg_match (" /^;/ ", $row)) {
			continue;
		}
		
		if (preg_match (" /^conf\s*=>\s*(\d{3,4})/ ",$row,$matches)) {
			array_push ($conferences,$matches[1]);
			$helper->logit("I'm sending room " . $matches[1],3);
		}
				
	}
	
	$res = $dbh->query("SELECT pkey from ivrmenu $wherestring");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$ivr = $res->fetchAll(); 
	
	$res = $dbh->query("SELECT pkey from Queue $wherestring");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$queue = $res->fetchAll(); 
	
	$res = $dbh->query("SELECT pkey from ipphone $wherestring");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$ipphone = $res->fetchAll(); 
	
	$res = $dbh->query("SELECT pkey from speed $wherestring");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$speed = $res->fetchAll(); 
	
	$res = $dbh->query("SELECT pkey from appl $wherestring");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$appl = $res->fetchAll(); 
	
	if (isset($_GET['route'])) {
		$res = $dbh->query("SELECT pkey from route $wherestring");  
		$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
		$routes = $res->fetchAll();
	}
	
/*
 * we use a simple counter prefixed to the keys in order to force the sequence we want
 * the list to be displayed in.  Otherwise json will display it in natural key order 
 * which isn't what we want.
 * These prefixes are stripped from the key in the updater when necessary
 */  							 		
	echo "{" . PHP_EOL;	 
	echo "'Operator':'Operator'," . PHP_EOL;
	echo "'Hangup':'Hangup'," . PHP_EOL;	
	if (! empty($ivr) ) {
		foreach ($ivr as $ivr => $value)  {
			echo "'$value':'IVR $value'," . PHP_EOL;
		}
	}
	
	if (! empty($queue) ) {	
		foreach ($queue as $queue => $value)  {
			echo "'$value':'Queue $value'," . PHP_EOL;
		}
	}

	if (! empty($ipphone) ) {
		foreach ($ipphone as $ipphone => $value)  {
			echo "'$value':'$value'," . PHP_EOL;	
			echo "'*$value':'*$value'," . PHP_EOL;
		}
	}

	if (! empty($speed) ) {
		foreach ($speed as $speed => $value)  {
			echo "'$value':'Callgrp $value'," . PHP_EOL;	
		}
		echo "'Retrieve Voicemail':'Retrieve Voicemail'," . PHP_EOL;
	}
	
	if (!isset($_GET['holiday'])) {
		echo "'DISA':'DISA'," . PHP_EOL;
		echo "'CALLBACK':'CALLBACK'," . PHP_EOL;		
		$sql = "select pkey, technology from lineio "; 
		$rows = $helper->getTable("lineio",$sql);
		foreach ($rows as $row ) {
			$key =  $row['pkey'];
			if ($row['technology'] != 'DiD' AND $row['technology'] != 'CLID' AND $row['technology'] != 'Class') {
				echo "'$key':'Trunk $key'," . PHP_EOL;
			}
		}
		if (is_array($routes)) {
			foreach ($routes as $routes => $value)  {
				echo "'$value':'Route $value'," . PHP_EOL;
			}	
		}		
		if (is_array($conferences)) {
			foreach ($conferences as $conferences => $value)  {
				echo "'$value':'Conf $value'," . PHP_EOL;
			}	
		}
		
	}

	if (! empty($appl) ) {
		foreach ($appl as $appl => $value)  {
			echo "'$value':'App $value'," . PHP_EOL;
		}
	}
	
	echo "}" . PHP_EOL; 
?>
