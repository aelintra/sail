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
// | Author: CoCoSoft                                                           |
// +-----------------------------------------------------------------------+
// 
//include("ip_helper_functions.php"); 
include("generated_file_banner.php");
include("localvars.php");  

try {
    /*** connect to SQLite database ***/
    	$dbh = new PDO($sarkdb);

    /*** set the error reporting attribute ***/
    	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
//
//  Header
//
	$global = $dbh->query("select * from globals")->fetch();

	if ($global['BINDADDR'] == "ON") { 
		$OUT .= 'bindaddr=' . $global['HACLUSTERIP'] . "\n";	   
	}
  
	$fh = fopen("/etc/asterisk/sark_iax_localnet_header.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh);

//
//	Registrations 	
//
	include("generated_file_banner.php");	 
	$sql = "SELECT * FROM lineIO order by pkey";
    	foreach ($dbh->query($sql) as $row) {
        if ($row['register'] != "") {                

			$cquery = "select * from Carrier where pkey='" . $row['carrier'] . "'";
			$carrier = $dbh->query($cquery)->fetch();       
			if ($carrier['technology'] == "IAX2") {
				if ($row['active'] == "YES") {
					$OUT .= 'register => ' . $row['register'] . "\n";
				}
			}
		}
	}	   
	$fh = fopen("/etc/asterisk/sark_iax_registrations.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh);	
//
//  IAX extensions (phones) 
//
	include("generated_file_banner.php");	 
	$sql = "SELECT * FROM IPphone order by pkey";
    	foreach ($dbh->query($sql) as $row) {
    	if ($row['technology'] == "IAX2") {
			$OUT .= "[" . $row['pkey'] . "]\n";
			$OUT .= $row['sipiaxfriend'] . "\n\n"; 
		}
	}
//
//  IAX peers (Lines)
//					       
	$sql = "SELECT * FROM lineIO order by pkey";
    foreach ($dbh->query($sql) as $row) {
		if ($row['active'] == "YES") {    
       		$cquery = "select * from Carrier where pkey='" . $row['carrier'] . "'";
 			$carrier = $dbh->query($cquery)->fetch();
			if ($carrier['technology'] == "IAX2"  || $carrier['technology'] == "Gateway") {
				if ($row['carrier'] == "InterSARK"  || $row['carrier'] == "SailToSail") {
					if ($row['privileged'] == "NO") {
						if (!preg_match(" /context=mainmenu/",$row['sipiaxuser'])) { 
							$row['sipiaxuser'] = preg_replace ( '/context=internal/','context=mainmenu', $row['sipiaxuser'] );
 						}
						else {
							$row['sipiaxuser'] = preg_replace ( '/\n\s*$/','context=mainmenu', $row['sipiaxuser'] );
						}
					}
				}               
				$OUT .= "[" . $row['peername'] . "]\n";
				$OUT .= $row['sipiaxpeer'] . "\n";
				if (isset($row['sipiaxuser'])) {
					$OUT .= "[" . $row['desc'] . "]\n";
					$OUT .= $row['sipiaxuser'] . "\n\n";;
				}
			}       
		}
    }
    /*** close the database connection ***/
    	$dbh = null;
// write the generated include file 
	$fh = fopen("/etc/asterisk/sark_iax_main.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh);

	$fh = fopen("/etc/asterisk/iax.conf", 'w') or die('Could not open file!');
// write the voicemail Master file
    	include("generated_file_banner.php");
	fwrite($fh, $OUT. " \n" .
				"#include sark_iax_header.conf  \n" .
				"#include sark_customer_iax_header.conf  \n" .
				"#include sark_iax_localnet_header.conf  \n" .
				"#include sark_iax_registrations.conf  \n" .
				"#include sark_iax_main.conf \n" .
				"#include sark_customer_iax_main.conf  \n") 
		or die('Could not write to file');
	fclose($fh); 
}

catch(PDOException $e)
{
    echo $e->getMessage();
}
?>		
