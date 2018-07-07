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

//read the rules
$rec = array();
$file = '/etc/shorewall/sark_rules' ;
$change = false;

$handle = @fopen($file, "r") or die('Could not read file!');
if ($handle) {
    while (($buffer = fgets($handle, 4096)) !== false) {
	if (preg_match(' /TRUNK/ ',$buffer)) {
		continue;
	}
	array_push ($rec, $buffer);	
    }
    if (!feof($handle)) {
        echo "Error: unexpected fgets() fail\n";
    }
    fclose($handle);
}

try {
    /*** connect to SQLite database ***/

    $dbh = new PDO($sarkdb);

    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        

//
//  SIP extensions (phones)
//
/*	include("generated_file_banner.php");	 
	$sql = "SELECT * FROM IPphone order by pkey";
    	foreach ($dbh->query($sql) as $row) {        
    		if ($row['technology'] == "SIP") {
			$OUT .= "[" . $row['pkey'] . "]\n";
			$OUT .= $row['sipiaxfriend'] . "\n\n"; 
		}
	}
*/

//
//  SIP peers (Lines)
//
	$sql = "SELECT * FROM lineIO order by pkey";
    	foreach ($dbh->query($sql) as $row) {                 		   
		$cquery = "select * from Carrier where pkey='" . $row['carrier'] . "'";
		$carrier = $dbh->query($cquery)->fetch();
		if ($carrier['technology'] == "SIP"  || $carrier['technology'] == "IAX2") { 
			handleFwRule($rec, $row['subnetstr'], $row['active']) ;
			handleFwRule($rec, $row['subnet1str'], $row['active']) ;
			handleFwRule($rec, $row['subnet2str'], $row['active']) ;
		}
	}


/*** close the database connection ***/
	$dbh = null;   

/*** write the control file back out ***/        
	$fh = fopen($file, 'w') or die('Could not open file!');
		foreach ($rec as $line) {  
			fwrite($fh, $line)
				or die('Could not write to file');
		
		} 
	fclose($fh);
}  

catch(PDOException $e)
    {
    echo $e->getMessage();
    }    

function handleFwRule(&$rec, $rule, $active)  {
//
//  function to handle a firewall rule.
//
	if ($rule && $active == "YES") {
		$rule .= "\n";
		array_push ($rec, $rule);
	}				
    	return (0);
}

?>		
