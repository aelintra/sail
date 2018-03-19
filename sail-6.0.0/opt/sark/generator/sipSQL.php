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
    	if ($global['EDOMAIN'] != "") { 
		$OUT .= 'externip=' . $global['EDOMAIN'] . "\n";	   
	}
	if ($global['BINDADDR'] == "ON") {
		if ($global['HACLUSTERIP']) {
			$OUT .= 'bindaddr=' . $global['HACLUSTERIP'] . "\n";
		}	   
	}
//  deal with TLS
	$tlsport = 'tlsbindaddr=0.0.0.0';
	if (isset($global['TLSPORT'])) {
		if ($global['TLSPORT'] != '5061') {
			$tlsport .= ':' . $global['TLSPORT'];
		}
	}
	`/bin/echo $tlsport > /etc/asterisk/sark_sip_tlsbindaddr.conf`;
	

	if (file_exists( '/etc/asterisk/sark_sip_localnet.conf' )) {
		$OUT .= "#include sark_sip_localnet.conf \n";
	}
	
	$fh = fopen("/etc/asterisk/sark_sip_localnet_header.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh);

//
//	Registrations 	
//
	include("generated_file_banner.php");	 
	$sql = "SELECT * FROM lineIO order by pkey";
    	foreach ($dbh->query($sql) as $row) {    
        	if (!empty($row['register'])) {
			$cquery = "select * from Carrier where pkey='" . $row['carrier'] . "'";
			$carrier = $dbh->query($cquery)->fetch();       
			if ($carrier['technology'] == "SIP") {
				if ($row['active'] == "YES") {
					$OUT .= 'register => ' . $row['register'] . "\n";
				}
			}
		}
	}	   
	$fh = fopen("/etc/asterisk/sark_sip_registrations.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh);	
//[
//  SIP extensions (phones)
//
	include("generated_file_banner.php");	 
	$sql = "SELECT * FROM IPphone order by pkey";
    foreach ($dbh->query($sql) as $row) {
		if ($row['active'] == "YES") {       
			if ($row['technology'] == "SIP") {
				$OUT .= "[" . $row['pkey'] . "]\n";
				$row['sipiaxfriend'] = preg_replace ( '/\$desc/', $row['desc'], $row['sipiaxfriend']);
				$row['sipiaxfriend'] = preg_replace ( '/\$password/', $row['passwd'], $row['sipiaxfriend']);
				$row['sipiaxfriend'] = preg_replace ( '/\$ext/', $row['pkey'], $row['sipiaxfriend']);
				$OUT .= $row['sipiaxfriend'] . "\n\n";
			} 
		}
	}
//
//  SIP peers (Lines)
//

	$sql = "SELECT * FROM lineIO order by pkey";
    foreach ($dbh->query($sql) as $row) {             
    	if ($row['active'] == "YES") {    
			$cquery = "select * from Carrier where pkey='" . $row['carrier'] . "'";
			$carrier = $dbh->query($cquery)->fetch();
			if ($carrier['technology'] == "SIP"  || $carrier['technology'] == "Gateway") {
				if ($row['privileged'] == "NO") {
					if (!preg_match(" /context=mainmenu/",$row['sipiaxuser'])) { 
						$row['sipiaxuser'] = preg_replace ( '/context=internal/','/context=mainmenu/', $row['sipiaxuser'] );
					}
					else {
						$row['sipiaxuser'] = preg_replace ( '/\n\s*$/','/context=mainmenu/', $row['sipiaxuser'] );
					}
				}	 	
				$OUT .= "\n[" . $row['peername'] . "]\n";
				$OUT .= $row['sipiaxpeer'] . "\n";
				if (isset($row['sipiaxuser'])) {
					if (trim($row['sipiaxuser'])) {
						$OUT .= "[" . $row['desc'] . "]\n";
						$OUT .= $row['sipiaxuser'] . "\n";
					}
				}
			}	
		}
	}
    /*** close the database connection ***/
    $dbh = null;   
	 
// write the generated include file 
	$fh = fopen("/etc/asterisk/sark_sip_main.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh); 

	$fh = fopen("/etc/asterisk/sip.conf", 'w') or die('Could not open file!');
// write the sip.conf file
    include("generated_file_banner.php");        
	fwrite($fh, $OUT. " \n" .
				"#include sark_sip_header.conf  \n" .
				"#include sark_customer_sip_header.conf  \n" .
				"#include sark_sip_localnet_header.conf  \n" .
				"#include sark_sip_registrations.conf  \n" .
				"#include sark_sip_main.conf \n" .
				"#include sark_customer_sip_main.conf  \n") 
		or die('Could not write to file');
	fclose($fh);
// clean up any DOS type stuff from the generated file 
	`dos2unix /etc/asterisk/sark_sip_main.conf >/dev/null 2>&1`;
}  

catch(PDOException $e)
    {
    echo $e->getMessage();
    }    



?>		
