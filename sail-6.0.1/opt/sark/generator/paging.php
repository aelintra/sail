<?php
// +-----------------------------------------------------------------------+
// |  Copyright (c) CoCoSoft 2005-10                                  |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: CoCoSoft                                                      
// +-----------------------------------------------------------------------+
// 

include("/opt/sark/php/srkHelperClass");

	$helper = new Helper;

	try {
    /*** connect to SQLite database ***/
    	$dbh = new PDO("sqlite:/opt/sark/db/sark.db");

    /*** set the error reporting attribute ***/
    	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }
    catch(PDOException $e) {

    	echo $e->getMessage();
	}

	$sql = "select EXTLEN from globals WHERE pkey='global'";   
	$global = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);

	$count = 1;
	$dialstr = "";
	$sql = "select * from IPphone order by pkey"; 
    foreach ($dbh->query($sql) as $row) { 
    	if ($count > 30) {
    		break;
    	}    
		$dialstr .= "SIP/" . $row['pkey'] .'&';
		$count++;
    } 
    //  strip last & and save pageall
    $dialstr = preg_replace ( '/&$/',"", $dialstr ); 
    $res = $dbh->prepare("UPDATE page SET pagegroup = '". $dialstr . "' WHERE pkey = ?");
 	$res->execute(array('pageall')); 

 	
	$dialstr = "";
	$pagearray = array();
	$dialstr = "";
	$pagearray = array();
	$sql = "select * from speed where grouptype='Page'";
 	foreach ($dbh->query($sql) as $row) { 
		$outlist = explode (' ', $row['out']);
		foreach ($outlist as $ep) {
// ignore non-extension entries			
			if (strlen($ep) != $global['EXTLEN'] ) {
				continue;
			}
			$sql2 = "select technology from IPphone WHERE pkey = '" . $ep . "'";                
			$IPphone = $dbh->query($sql2)->fetch(PDO::FETCH_ASSOC);
// ignore non sip entries
			if ($IPphone['technology'] != "SIP") {
				continue;
			}
			$dialstr .= $IPphone['technology'] . "/" . $ep . "&";		
		}
		$dialstr = preg_replace ( '/&$/',"", $dialstr );
		// build array of page groups (key & dialstring)				
		$pagearray [$row['pkey']] =	$dialstr; 			 
		$dialstr = "";
	}

// insert page group dialstrings into their tables 

	foreach ($pagearray as $pkey=>$dialstr) {
		$count = $dbh->exec("UPDATE speed SET pagegroup = '". $dialstr . "' WHERE pkey = '" . $pkey . "'" );
 	}
  
 		