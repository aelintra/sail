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
   $fh = fopen("/etc/asterisk/features.conf", 'w') or die('Could not open file!');
// write the Master file
   include("generated_file_banner.php");  

   $rlse = file_get_contents("/etc/debian_version");
   
// Ast 13 does parking differently	
   if(preg_match( '/^9/', $rlse)) {
   		$OUT .= "[general] \n\n\n";
   }
   else {
       	$OUT .= "#include sark_features_general.conf  \n";
   } 
   $OUT .= "#include sark_features_featuremap.conf  \n";
   $OUT .= 	"#include sark_features_applicationmap.conf  \n";
   fwrite($fh, $OUT. " \n")
   	or die('Could not write to file');
   	
   fclose($fh);
?>
