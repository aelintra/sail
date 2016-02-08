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
function ret_localip () {
    $work = `/sbin/ifconfig eth0`;    
	if (preg_match(" /inet addr:*?([\d.]+)/",$work,$matches)) { 
	 		return $matches[1]; 	 		
 	}
	return -1;   
}
//
// don't use this subnet function - it is incorrect
//
function ret_subnet () {
    $work = `/sbin/ifconfig eth0`;
        if (preg_match(" /inet addr:*?([\d\.]+)/",$work,$matches)) {
             $subnet = preg_replace ( '/\d+$/','0', $matches[1] );
             return $subnet;
        }
    return -1;
}
function ret_externip () {
    $work = `/usr/bin/wget -q  -O  - checkip.dyndns.org`;   
	if (preg_match(" /Current IP Address:*.([\d\.]+)/",$work,$matches)) { 
	 		return $matches[1]; 	 		
 	}
	return -1; 
}
function ret_subnetmask () {
    $work = `/sbin/ifconfig eth0`;    
	if (preg_match(" /Mask:*?([\d\.]+)/",$work,$matches)) { 
	 		return $matches[1]; 	 		
 	}
	return -1;
}
?>
