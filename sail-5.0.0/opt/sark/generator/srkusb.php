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

if ( ! `mount | grep "/dev/sda1 on /media/usb0 type"`) {
	exit;
}

$media = "/media/usb0";
if (!file_exists($media . '/SARK')) {
	exit;
}

$reboot=false;
$dhcp_on_string = 
		"auto lo eth0\n".
		"iface lo inet loopback\n".
		"iface eth0 inet dhcp\n".
		"allow-hotplug wlan0\n". 
		"iface wlan0 inet manual\n". 
		"wpa-roam /etc/wpa_supplicant.conf\n". 
		"iface default inet dhcp\n" .
		"source /etc/network/interfaces.d/*\n";

ini_set("log_errors", 1);
ini_set("error_log", $media."/media/usb0/SARK/runlog");
logIt("USB UTILS stick detected, Begin processing");

if (file_exists($media."/SARK/MAC")) {
	$usbmac = trim(file_get_contents($media."/SARK/MAC"));
	$mac = trim(strtoupper(`ip link show eth0 | awk '/ether/ {print $2}'`)); 	
	if ($usbmac != $mac) {
		logIt("MAC address given but doesn't match system");
		logIt("SYSMAC=".$mac);
		logIt("USBMAC=".$usbmac);
	}
	else {
		logIt("MAC addresses match");
		
		if (file_exists($media."/SARK/RESETDHCP")) {
			logIt("DHCP RESET REQUESTED");
			logit("setting DHCP defaults for eth0");
			file_put_contents("/etc/network/interfaces",$dhcp_on_string);
			logit ("restarting eth0 - check NETWORK file for details");			
			unlink($media."/SARK/RESETDHCP");
			$reboot=true;			
		}
					
		if (file_exists($media."/SARK/RESETPASS")) {
			logIt("Password RESET REQUESTED");
			logIt("resettng web password");
/*
			`echo admin:saY5WNr1mlMqU > /opt/sark/passwd/htpasswd`;
			logIt("resetting root password");
			`/usr/sbin/usermod -p $(echo sarkroot | openssl passwd -1 -stdin) root`;
*/
			`/usr/bin/sqlite3 /opt/sark/db/sark.db "delete from User where pkey='admin';"`;
			`/usr/bin/sqlite3 /opt/sark/db/sark.db < /opt/sark/always/1408278454.db_v4_set_defaults`;
			unlink($media."/SARK/RESETPASS");
		}	
	}	
}

logIt("saving ip details to $media/SARK/NETWORK");
`ip addr > $media/SARK/NETWORK`;
logIt("saving release info to $media/SARK/RELEASE");
`dpkg -l | grep sail > $media/SARK/RELEASE`;
if ($reboot) {
	logIt("issuing reboot");
	`/sbin/reboot`;
}
logIt("End processing");
`/bin/sync`;
exit;

function logIt($someText) {
		error_log($someText);	
}	

?>		
