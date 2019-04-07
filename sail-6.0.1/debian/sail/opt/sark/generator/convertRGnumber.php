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
include("localvars.php");

    /*** connect to SQLite database ***/

    $dbh = new PDO($sarkdb);

    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
//
//  Header
//

	$groupnumber=1;

	$sql = "SELECT * FROM Cluster order by pkey";
	foreach ($dbh->query($sql) as $clust) { 
		echo "Running for cluster " . $clust['pkey'] . "\n";
 		$psql = $dbh->prepare("SELECT * FROM IPphone WHERE cluster=?");
		$psql->execute(array($clust['pkey']));	
 		$result = $psql->fetchall();
 		$psql=NULL;		
    	foreach ($result as $row) {    
    		echo "Running for exten " . $row['pkey'] . "\n";  
			$row['sipiaxfriend'] = preg_replace ( '/callgroup=\d{1,2}/', "callgroup=" . $groupnumber, $row['sipiaxfriend']);
			$row['sipiaxfriend'] = preg_replace ( '/pickupgroup=\d{1,2}/', "pickupgroup=" . $groupnumber, $row['sipiaxfriend']);	
			$fsql = "UPDATE ipphone SET sipiaxfriend = '" .  $row['sipiaxfriend'] .  "' WHERE pkey=?";
			$query = $dbh->prepare($fsql);
			$query->execute(array($row['pkey']));			
			$query=NULL; 
		}
	$groupnumber++;
	}
