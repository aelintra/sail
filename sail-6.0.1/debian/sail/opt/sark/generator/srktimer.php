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
require_once $sarkpath . "/php/srkDbClass";
require_once $sarkpath . "/php/srkHelperClass";

	$helper = new helper;        
	$dbh = DB::getInstance();
    $cluster=array();
    $holarray=array();
    $dateseg=array();
    $tuple = array();
    $dbupdated = false;
    $debug = false; 	// set true for debug output
    
    $helper->logit(" SARKTIMER Started", 10 );

		
// initialize a holiday array

	$rows = $helper->getTable("cluster");	
    foreach ($rows as $row) {
    	$holarray[$row['pkey']] = array (
			'cluster' => $row['pkey'],
			'route' => NULL,
			'routeclass' => NULL
		);
    }
    	
// initialize a dateseg array	

	$rows = $helper->getTable("dateseg");	
    foreach ($rows as $row) {
    	$dateseg[$row['pkey']] = 'IDLE';
    }	
	
// check for holiday overrides in the clusters
	$holidays = $helper->getTable("holiday");
	$now = time(); 

// only consider holidays which are active	
    foreach ($holidays as $row) {
    	if ($row['stime'] <= $now && $row['etime'] >= $now) {
			$holarray[$row['cluster']] = array (
				'cluster' => $row['cluster'],
				'route' => $row['route'],
				'routeclass' => $row['routeclass']
			);
		}
    }
    
    if ($debug) {
		print_r($holidays);
		print_r($holarray);
	}

// now we can set any holidays into the cluster table
    foreach ($holarray as $k) {
//		print "K[cluster] IS " . $k['cluster'] . " \n";
		$res = $dbh->query("SELECT pkey,routeoverride,routeclassoverride from cluster where pkey ='" . $k['cluster'] . "'")->fetch(PDO::FETCH_ASSOC);
		if ($debug) {
			print_r($res);
		}

		if ($res['routeoverride'] == $k['route']) {
			continue;
		}
			
		$tuple['pkey'] = $k['cluster'];
		$tuple['routeoverride'] = $k['route'];
		$tuple['routeclassoverride'] = $k['routeclass'];
        $ret = $helper->setTuple('cluster',$tuple);
        $helper->logit("Updated Cluster " . $tuple['pkey'] . " with route " . $tuple['routeoverride'] , 0 );
        $dbupdated = true;
    }
    
    
// now we can deal with regular timers

	$rows = $helper->getTable("cluster");	
    foreach ($rows as $row) {
    	$cluster[$row['pkey']] = "OPEN";
    }
    
    $rows = $helper->getTable("dateseg");
    foreach ($rows as $row) {
    	$match = false;
    	if ($row['month'] != "*" ) {
			if ($row['month'] != strtolower(date("M", time())) ) {
				if ($debug) {
					echo "MONTH FAILS  - WE'RE OPEN " . $row['month'] . "\n";
				}
               	continue;
			}
			$match = true;
			if ($debug) {
				echo "MONTH MATCHED " . $row['month'] . "\n";
			}
        }
        if ($row['dayofweek'] != "*" ) {
			if ($row['dayofweek'] != strtolower(date("D", time())) ) {
				if ($debug) {
					echo "DAY FAILS  - WE'RE OPEN " . $row['dayofweek'] . "\n";
				}
               	continue;
			}
			$match = true;
			if ($debug) {
				echo "DAY MATCHED " . $row['dayofweek'] . "\n";
			}
        }
        if ($row['datemonth'] != "*" ) {
			if ($row['datemonth'] != date( "j", time() ) ) {
				if ($debug) {
					echo "DATE FAILS  - WE'RE OPEN " . $row['datemonth'] . "\n";
				}
               	continue;
			}
			$match = true;
			if ($debug) {
				echo "DATE " . $row['datemonth'] . "\n";
			}
        }
        if ($row['timespan'] == "*-*" ) {
			if ($match == true) {
				if ($debug) {
					echo "TIMESPAN = * BUT MATCHED  - WE'RE CLOSED " . $row['timespan'] . "\n";
				}
                $cluster[$row['cluster']] = "CLOSED";
                $dateseg[$row['pkey']] = "*INUSE*";
               	continue;
           }
           else {
				if ($debug) {
					echo "ALL STARS  - WE'RE OPEN " . $row['cluster'] . "\n";
				}
                continue;
           }
        }
        if (preg_match(" /^(\d\d):(\d\d)-(\d\d):(\d\d)$/",$row['timespan'],$matches)) {
			$tstart =  $matches[1] . $matches[2];
			if ($debug) {
				echo "TSTART $tstart \n";
			}
			$tend =  $matches[3] . $matches[4];
			if ($debug) {
				echo "TEND $tend \n";
			}
			if ($tstart > $tend) {           
				if ( $tstart <  date( "Hi", time()) || $tend > date( "Hi", time()) ) {
					if ($debug) {
						echo "DATE - " . date( "Hi", time()) . "\n";
						echo "Invert TIMESPAN MATCHED  - WE'RE CLOSED " . $row['cluster'] . "\n";
					}
					$cluster[$row['cluster']] = "CLOSED";
					$dateseg[$row['pkey']] = "*INUSE*";
					continue;                
				}
           }
           if ( $tstart <  date( "Hi", time()) && $tend > date( "Hi", time()) ) { 
				if ($debug) {
					echo "DATE - " . date( "Hi", time()) . "\n";
					echo "TIMESPAN MATCHED  - WE'RE CLOSED " . $row['cluster'] . "\n";
				}
        		$cluster[$row['cluster']] = "CLOSED";
        		$dateseg[$row['pkey']] = "*INUSE*";
                continue;
           }
        }
        if ($debug) {
			echo "NO MATCH  - WE'RE OPEN " . $row['pkey'] . "\n";
		}
    }
    
    foreach ($cluster as $k=>$v) {
		$dboclo = $dbh->query("select oclo from Cluster WHERE pkey='" . $k . "'")->fetch();
		if ($dboclo['oclo'] == $v) {
			continue;
		}
		$tuple['pkey'] = $k;
		$tuple['oclo'] = $v;
        $ret = $helper->setTuple('cluster',$tuple);
        $dbupdated = true;
    }
    if ($debug) {
		print_r($dateseg);
	} 
	unset($tuple); 
	$tuple=array();  
    foreach ($dateseg as $k=>$v) {
		$state = $dbh->query("select state from dateseg WHERE pkey='" . $k . "'")->fetch();
		if ($state['state'] == $v) {
			continue;
		}
		
		$tuple['pkey'] = $k;
		$tuple['state'] = $v;
        $ret = $helper->setTuple('dateseg',$tuple);
        $dbupdated = true;
    }
        
    if ($dbupdated == true) {
		$helper->logit(" SARKTIMER MODIFY", 10 );
		`/usr/bin/sqlite3 /opt/sark/db/sark.db "UPDATE globals SET MYCOMMIT='NO' WHERE pkey='global';"`;
		$rc = `/bin/cp /opt/sark/db/sark.db /opt/sark/db/sark.copy.db`;
		$rc = `/bin/mv /opt/sark/db/sark.copy.db /opt/sark/db/sark.rdonly.db`;
#		$rc = `/bin/chown www:www /opt/sark/db/sark.rdonly.db`;  
	}
	$helper->logit(" SARKTIMER Ended", 10 );
?>
