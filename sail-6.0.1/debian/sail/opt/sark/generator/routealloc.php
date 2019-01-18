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
include("localvars.php");

try {
    /*** connect to SQLite database ***/
    $dbh = new PDO($sarkdb);
    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    $defaultroute = $dbh->query("SELECT * FROM route where pkey='DEFAULT'")->fetch(PDO::FETCH_ASSOC);
    
	if ( ! isset($defaultroute['pkey']) ) {
		echo 'No default route in the system, can\'t set a default trunk' . PHP_EOL;
		exit;
	}
	
	if ( $defaultroute['path1'] !=  "None"  ) {
		echo 'Default route already set, can\'t set a default trunk' . PHP_EOL;
		exit;
	}
	
    
    $aoh_array = parse_ini_file("/etc/pika/aohscan.new", true);
//	print_r($aoh_array);
	
	$digital = false;
	$route = 'PIKAFXO_G0';

	if ($aoh_array['Board_0']['nb_fxo'] > 0 ) {
			$digital = false;
	}
	else if (isset($aoh_array['Board_1']) ) {
		if ($aoh_array['Board_1']['nb_span'] > 0 ) {
				$digital = true;
		}
	}
	else {
		echo 'No TDM boards in the system, can\'t set a default route' . PHP_EOL;
		exit;
	}
	
	if ( $digital == True ) {
		$route = 'PIKABRI_G1';
	}
	
	$dbh->exec("UPDATE route SET path1 = '". $route . "' WHERE pkey = 'DEFAULT'" );
		
	echo 'set default route to ' .$route . PHP_EOL;
   
    /*** close the database connection ***/
    $dbh = null;
}
catch(PDOException $e)
    {
    echo $e->getMessage();
    }
?>
