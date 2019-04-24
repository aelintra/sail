<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
	require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass");
	require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass");

	
    $helper = new helper;  
	$dbh = DB::getInstance();

	$helper->logit("I'm sending operators ",3 );
	
	$user =  $_SESSION['user']['pkey'];
	if ($_SESSION['user']['pkey'] == 'admin') {
		$wherestring = '';
	}
	else {
		$wherestring = "WHERE cluster='" . $_SESSION['user']['pkey'] . "'";
	}
	

	$res = $dbh->query("SELECT pkey from ipphone $wherestring");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$ipphone = $res->fetchAll(); 
	
	$res = $dbh->query("SELECT pkey from speed $wherestring");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$speed = $res->fetchAll(); 
	
							 		
	echo "{" . PHP_EOL;	 
	echo "'System Operator':'System Operator'," . PHP_EOL;

	foreach ($ipphone as $ipphone => $value)  {
		echo "'$value':'Ext $value'," . PHP_EOL;	
	}

	foreach ($speed as $speed => $value)  {
		if ( $value != 'RINGALL' ) {
			echo "'$value':'Callgrp $value'," . PHP_EOL;
		}	
	}

	echo "}" . PHP_EOL; 
?>
