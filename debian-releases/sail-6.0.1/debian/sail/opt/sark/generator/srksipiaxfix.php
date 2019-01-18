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
include("localvars.php");
include ("/opt/sark/php/srkNetHelperClass");

$nethelper = new netHelper;

try {
    /*** connect to SQLite database ***/

    $dbh = new PDO($sarkdb);

    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

//
//  SIP extensions (phones) fixup for V6
// 
	$sql = "SELECT * FROM IPphone order by pkey";
    foreach ($dbh->query($sql) as $row) {
    	$sipiaxfriend = $row['sipiaxfriend'];
    	if (! preg_match(' /nat=/ ', $sipiaxfriend)) {
    		$sipiaxfriend .= "\n" . 'nat=$nat';
    	}
    	
    	if (! preg_match(' /transport=/', $sipiaxfriend)) {
    		$sipiaxfriend .= "\n" . 'transport=$transport';
    	}
        if (! preg_match(' /encryption=/', $sipiaxfriend)) {
            $sipiaxfriend .= "\n" . 'encryption=$encryption';
        }        
    	if ($sipiaxfriend != $row['sipiaxfriend']) {
    		$sql = $dbh->prepare("UPDATE ipphone SET sipiaxfriend=? WHERE pkey=?");
    		$sql->execute(array($sipiaxfriend,$row['pkey']));
    	}
    }


    /*** close the database connection ***/
    $dbh = null;   
}  

catch(PDOException $e)
    {
    echo $e->getMessage();
    }    

?>		
