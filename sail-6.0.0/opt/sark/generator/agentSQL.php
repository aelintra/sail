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
// | Author: CoCoSoft                                                      |
// +-----------------------------------------------------------------------+
//

include("generated_file_banner.php");
include("localvars.php");

try {
    /*** connect to SQLite database ***/
    $dbh = new PDO($sarkdb);  

    /*** set the error reporting attribute ***/
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT * FROM Agent order by pkey";
    foreach ($dbh->query($sql) as $row) {
    	$OUT .= 'agent => ' . $row['pkey'] . ',' . $row['passwd'] . ',' .
				$row['name'] . " \n";	        
    }
    /*** close the database connection ***/
    $dbh = null;

// write the generated include file 
	$fh = fopen("/etc/asterisk/sark_agents_main.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh);
	$fh = fopen("/etc/asterisk/agents.conf", 'w') or die('Could not open file!');
// write the voicemail Master file
    include("generated_file_banner.php");        
	fwrite($fh, $OUT. " \n" .
				"#include sark_agents_header.conf  \n" .
				"#include sark_customer_agents_header.conf  \n" .
				"#include sark_agents_main.conf \n" .
				"#include sark_customer_agents_main.conf  \n") 
		or die('Could not write to file');
	fclose($fh);

}
catch(PDOException $e)
    {
    echo $e->getMessage();
    }
?>
