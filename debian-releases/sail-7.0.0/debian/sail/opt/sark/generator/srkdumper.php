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

$sysTables = array(
	"Carrier"  			=> true,			
	"mfgmac"  			=> true,
	"Panel"  			=> true,
	"PanelGroup"  		=> true,
	"PanelGroupPanel"  	=> true,
	"vendorxref"  		=> true,
	"Device_atl"  		=> true
);
     
$prefix='last_';
if (isset ($argv[1])) {
	$prefix = $argv[1];
} 

	$tables=array();
	$colrows=array();
	$datarows=array();
	$cfgfilename='/opt/sark/db/' . $prefix . 'create.sql';
	$datafilename='/opt/sark/db/' . $prefix . 'data.sql';
	$devfilename='/opt/sark/db/' . $prefix . 'device.sql';
	$custdevfilename='/opt/sark/db/' . $prefix . 'custdevice.sql';
	$sysfilename='/opt/sark/db/' . $prefix . 'system.sql';		
		
    /*** connect to SQLite database ***/
    try {
		$dbh = new PDO($sarkdb);
	}
	catch (Exception $e) {
		echo "Oops failed to open DB $sarkdb" . " $e\n";
		exit(4);
	}

    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
 	$COLDATA 	= NULL;
	$VALDATA 	= NULL;
	$CREATE 	= "BEGIN TRANSACTION;\n";
	$INSERT 	= "BEGIN TRANSACTION;\n";
	$SYSINSERT 	= "BEGIN TRANSACTION;\n";
	$DEVINSERT 	= "BEGIN TRANSACTION;\n";
	$CUSTDEVINSERT 	= "BEGIN TRANSACTION;\n";
	       
/*
 * get a list of tables
 */
    try { 
		$tables = $dbh->query("select name from sqlite_master where type='table'")->fetchall();
	}
	catch (Exception $e) {
		echo "Oops on table list fetch " . " $e\n";
		exit(8);
	}

/*
 * get a column list for each table
 */		
	foreach ($tables as $table) {

//	undolog and tt_help_user are gone in V4 - ignore them if this is a V3 upgrade
		if ( $table['name'] == 'undolog' || $table['name'] == 'tt_help_user') {
			continue;
		} 
		
//  vendorxref, device_atl and netphone are gone in V5.x - ignore them
		if ( $table['name'] == 'vendorxref' || $table['name'] == 'device_atl' || $table['name'] == 'netphone') {
			continue;
		} 

//  ignore the master_xref table - it gets built by triggers
	  	if ($table['name'] == 'master_xref') {
	  		continue;
	  	} 

 //  ignore the master_audit table on a reload.  It's no longer relevant to this copy
	  	if ($table['name'] == 'master_audit') {
	  		continue;
	  	} 

//  ignore the messages table on a reload.  It is always refreshed
	  	if ($table['name'] == 'tt_help_core') {
	  		continue;
	  	} 	  	

//  get the create sql from sqlite3
		try {
		 $sql = $dbh->query("select sql from sqlite_master WHERE name='" . $table['name'] . "' AND type='table'" )->fetchColumn();
		}
		catch (Exception $e) {
			echo "Oops on sql retrieve for " . $table['name'] . " $e\n";
			exit(12);
		}
		
//  make it idempotent
		if (!preg_match ('/^CREATE TABLE IF NOT EXISTS/', $sql)) {
			$sql = preg_replace ( '/^CREATE TABLE /','CREATE TABLE IF NOT EXISTS ', $sql );
		}
		$CREATE .= $sql; 
		$CREATE .= ";\n\n";
		
//  ignore empty tables
		$res = $dbh->query('select count(*) from ' . $table['name'] )->fetchColumn();
		if ( $res == 0 ) {
			$someText = "TABLE " . $table['name'] . " IS EMPTY";
			syslog(LOG_WARNING, date("M j H:i:s") . ": SRKDUMPER -> " . $someText . "\n");
			continue;
		}

//  get column metadata and table data 	
		try {
			$colrows = $dbh->query( "PRAGMA table_info(" . $table['name'] . ")" )->fetchall();
			if ($table['name'] == 'IPphoneCOSopen'  ||  $table['name'] == 'IPphoneCOSclosed' 
				|| $table['name'] == 'UserPanel' 	||  $table['name'] == 'PanelGroupPanel'
				|| $table['name'] == 'IPphone_FKEY' ||  $table['name'] == 'Device_FKEY') {
				$rows = $dbh->query( "SELECT * from " . $table['name'] )->fetchall();
			}
			else {
				$rows = $dbh->query( "SELECT * from " . $table['name'] . " ORDER BY pkey" )->fetchall();
			}
		}
		catch (Exception $e) {
			echo "Oops on select from " . $table['name'] . " $e\n";
			exit(16);
		}
			
			
		
// Build the dump string 	
		foreach ($rows as $row) {
				
			foreach ($colrows as $col) {
				$myData = $row[$col['name']];
				if ($myData) {
// don't carry forward the create/update time stamps.  They'll cause interlocks on the DB.
					if ( !preg_match (" /^z_/", $col['name'] )) {
						$COLDATA .= $col['name'] . ",";
						$VALDATA .= "'" . $myData . "',";
					}
				}
			}
			$COLDATA = rtrim($COLDATA, ',');
			$VALDATA = rtrim($VALDATA, ',');
			
// ignore system tables
			if (isset($sysTables[$table['name']])) {			
				$SYSINSERT .= "INSERT OR IGNORE INTO " . $table['name'] . "(" . $COLDATA . ") values (" . $VALDATA . ");\n";
			}
// dump the device table into a separate file
			elseif ($table['name'] == 'Device') {
/*
 * suggested fix for cust data persistence (and DROP device_atl)
 */
 				if (isset($row['owner'])) {
  					if ($row['owner'] == "cust") {
  						$CUSTDEVINSERT .= "INSERT OR IGNORE INTO " . $table['name'] . "(" . $COLDATA . ") values (" . $VALDATA . ");\n";
  					}
  					else { 
						$DEVINSERT .= "INSERT OR IGNORE INTO " . $table['name'] . "(" . $COLDATA . ") values (" . $VALDATA . ");\n";
					}
  				}
			}
// dump the customer data 
			else {
				$INSERT .= "INSERT OR IGNORE INTO " . $table['name'] . "(" . $COLDATA . ") values (" . $VALDATA . ");\n";
			}	
			$COLDATA = NULL;
			$VALDATA = NULL;			
		}		
	}
	$CREATE 	.= "COMMIT;\n";
	$INSERT 	.= "COMMIT;\n";
	$SYSINSERT 	.= "COMMIT;\n";
	$DEVINSERT 	.= "COMMIT;\n";	
	$CUSTDEVINSERT 	.= "COMMIT;\n";
	
	$fh = fopen($cfgfilename, 'w') or die('Could not open file!');
	fwrite($fh,$CREATE) or die('Could not write to cfg file');
	fclose($fh);

	$fh = fopen($datafilename, 'w') or die('Could not open file!');
	fwrite($fh,$INSERT) or die('Could not write to insert file');
	fclose($fh);	

	$fh = fopen($sysfilename, 'w') or die('Could not open file!');
	fwrite($fh,$SYSINSERT) or die('Could not write sysinsert to file');
	fclose($fh);
	
	$fh = fopen($devfilename, 'w') or die('Could not open file!');
	fwrite($fh,$DEVINSERT) or die('Could not write devinsert to file');
	fclose($fh);
	
	$fh = fopen($custdevfilename, 'w') or die('Could not open file!');
	fwrite($fh,$CUSTDEVINSERT) or die('Could not write custdevinsert to file');
	fclose($fh);
	
// clear any junk left by windows-based data changes 
	`dos2unix $cfgfilename >/dev/null 2>&1`;
	`dos2unix $datafilename >/dev/null 2>&1`;
	`dos2unix $devfilename >/dev/null 2>&1`;
	`dos2unix $custdevfilename >/dev/null 2>&1`;	
	`dos2unix $sysfilename >/dev/null 2>&1`;
		
/*
}

catch(PDOException $e)
    {
    echo "fail on try " .  $e->getMessage();
    }
*/    
?>		
