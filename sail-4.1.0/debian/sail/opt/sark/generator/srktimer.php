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
    $cluster=array();
    $sql = "SELECT * FROM Cluster";
    foreach ($dbh->query($sql) as $row) {
    	$cluster[$row['pkey']] = "OPEN";
    }
    $sql = "SELECT * FROM dateSeg";
    foreach ($dbh->query($sql) as $row) {
    	$match = false;
    	if ($row['month'] != "*" ) {
    	   if ($row['month'] != strtolower(date("M", time())) ) {
//           	echo "MONTH FAILS  - WE'RE OPEN " . $row['month'] . "\n";
               	continue;
           }
           $match = true;
//           echo "MONTH MATCHED " . $row['month'] . "\n";
        }
        if ($row['dayofweek'] != "*" ) {
    	   if ($row['dayofweek'] != strtolower(date("D", time())) ) {
//           	echo "DAY FAILS  - WE'RE OPEN " . $row['dayofweek'] . "\n";
               	continue;
           }
           $match = true;
//           echo "DAY MATCHED " . $row['dayofweek'] . "\n";
        }
        if ($row['datemonth'] != "*" ) {
    	   if ($row['datemonth'] != date( "j", time() ) ) {
//                echo "DATE FAILS  - WE'RE OPEN " . $row['datemonth'] . "\n";
               	continue;
           }
           $match = true;
//           echo "DATE " . $row['datemonth'] . "\n";
        }
        if ($row['timespan'] == "*" ) {
           if ($match == true) {
//                echo "TIMESPAN = * BUT MATCHED  - WE'RE CLOSED " . $row['timespan'] . "\n";
                $cluster[$row['cluster']] = "CLOSED";
               	continue;
           }
           else {
//                echo "ALL STARS  - WE'RE OPEN " . $row['cluster'] . "\n";
                continue;
           }
        }
        if (preg_match(" /^(\d\d):(\d\d)-(\d\d):(\d\d)$/",$row['timespan'],$matches)) {
	       $tstart =  $matches[1] . $matches[2];
//           echo "TSTART $tstart \n";
           $tend =  $matches[3] . $matches[4];
//           echo "TEND $tend \n";
           if ($tstart > $tend) {           
				if ( $tstart <  date( "Hi", time()) || $tend > date( "Hi", time()) ) {
//                	echo "DATE - " . date( "Hi", time()) . "\n";
//                	echo "Invert TIMESPAN MATCHED  - WE'RE CLOSED " . $row['cluster'] . "\n";
					$cluster[$row['cluster']] = "CLOSED";
					continue;                
				}
           }
           if ( $tstart <  date( "Hi", time()) && $tend > date( "Hi", time()) ) {           
//                echo "DATE - " . date( "Hi", time()) . "\n";
//                echo "TIMESPAN MATCHED  - WE'RE CLOSED " . $row['cluster'] . "\n";
        		$cluster[$row['cluster']] = "CLOSED";
                continue;
           }
        }
//           echo "NO MATCH  - WE'RE OPEN " . $row['pkey'] . "\n";
    }
    $dbupdated = false;
    foreach ($cluster as $k=>$v) {
		$dboclo = $dbh->query("select oclo from Cluster WHERE pkey='" . $k . "'")->fetch();
		if ($dboclo['oclo'] == $v) {
			continue;
		}

    	$sql="UPDATE Cluster SET oclo=\"$v\" WHERE pkey='" . $k . "'";
        $dbh->exec($sql);
        $dbupdated = true;
    }
    /*** close the database connection ***/
    $dbh = null;
    if ($dbupdated == true) {
		syslog(LOG_WARNING, date("M j H:i:s") . ": " ."SARKTIMER MODIFY ");
		$rc = `/bin/cp /opt/sark/db/sark.db /opt/sark/db/sark.copy.db`;
		$rc = `/bin/mv /opt/sark/db/sark.copy.db /opt/sark/db/sark.rdonly.db`;
#		$rc = `/bin/chown www:www /opt/sark/db/sark.rdonly.db`;  
	}
}

catch(PDOException $e)
    {
    echo $e->getMessage();
    }
?>
