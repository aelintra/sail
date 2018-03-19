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

include("generated_file_banner.php"); 
include("localvars.php");

try {
    /*** connect to SQLite database ***/
    $dbh = new PDO($sarkdb); 

    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $Agents = "SELECT * FROM Agent order by pkey";    
	$Queues = "SELECT * FROM Queue order by pkey";

    foreach ($dbh->query($Queues) as $row) {
        
//        print $row['pkey'] . ' ' . $row['dvrvmail'] . " \n";  
        $OUT .= '[' . $row['pkey'] . "] \n"; 
		$OUT .= $row['conf'];
		$OUT .= "\n";    
/*
		foreach ($dbh->query($Agents) as $A_row) { 
			if (in_array($row['pkey'], $A_row)) {  
				$OUT .= 'member => Agent/' . $A_row['pkey'] . "\n";
			}
		}
*/	      
	}
	
	$OUT .= ";Special Queues(if any)\n";  
        
     
	$row = $dbh->query("select CAMPONQONOFF from globals")->fetch();    

	if ( $row['CAMPONQONOFF'] == 'ON' ) {
		foreach ($dbh->query("select * from IPphone") as $row) {
			$OUT .= '[Q' . $row['pkey'] . "] \n"; 
			$OUT .= 'member=>SIP/' . $row['pkey'] . "\n";
		    $OUT .=	"musiconhold=default\n";
			$OUT .= "strategy=ringall\n";
			$OUT .= "timeout=300\n";
			$OUT .= "retry=5\n";
			$OUT .= "wrapuptime=0\n";
			$OUT .= "maxlen=0\n";
			$OUT .= "announce-frequency=30\n";
			$OUT .= "announce-holdtime=yes\n\n";
		}
	}

    /*** close the database connection ***/
    $dbh = null;   
	 
// write the generated include file 
	$fh = fopen("/etc/asterisk/sark_queues_main.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh); 

	$fh = fopen("/etc/asterisk/queues.conf", 'w') or die('Could not open file!');
// write the voicemail Master file
    include("generated_file_banner.php");        
	fwrite($fh, $OUT. " \n" .
				"#include sark_queues_header.conf  \n" .
				"#include sark_customer_queues_header.conf  \n" .
				"#include sark_queues_main.conf \n" .
				"#include sark_customer_queues_main.conf  \n") 
		or die('Could not write to file');
	fclose($fh); 
}  

catch(PDOException $e)
    {
    echo $e->getMessage();
    }
?>		
