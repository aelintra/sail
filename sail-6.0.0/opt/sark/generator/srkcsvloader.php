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

/*
 * rewrite of the old V3 loader to V4 ***INCOMPLETE***

/*
 *  csv file layout
 *	$macaddr 		
 *	$vendordevice 	
 *	$pkey 			
 *	$name  			
 * 	$ddi  			
 *	$location (local/remote)	
 *	$cluster  (default is 'default')
 */

include("localvars.php");

if ($argc != 2) {
  echo "Usage: srkcsvloader.php csvfilename\n";
  exit;
}
$loadcsv=$argv[1];

if (!file_exists($loadcsv)) {
	echo $loadcsv . " does not exist\n";
} 
// read it
$recs = file('$loadcsv');

    /*** connect to SQLite database ***/
    $dbh = new PDO($sarkdb);

    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
//do it
foreach ($recs as $phone) {
	$cols = explode(',',$phone);
	foreach ($cols as $cell) {
		$macaddr = $cell[0];
		$vendordevice = $cell[1];
		$pkey = $cell[2];
		$name =  $cell[3];
		$ddi = $cell[4];
		$location = $cell[5];
		$cluster = $cell[6];
	}
	
}
    
?>    
    
