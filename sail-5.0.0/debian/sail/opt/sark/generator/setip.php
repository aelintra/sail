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

include("/opt/sark/php/srkNetHelperClass");

$net = new nethelper();

$interface = $net->get_interfaceName();
$netaddress = $net->get_networkIPV4();
$cidr = $net->get_networkCIDR();
$msk = $net->get_netMask();
$ip = $net->get_localIPV4();

echo  "Interface name on this node: $interface\n";
echo  "IPV4: $ip\n";
echo  "Network address: $netaddress\n";
echo  "netmask: $msk\n";
echo  "CIDR: $cidr\n";

$Phonelist = array ('aastra'=>'aastra',
					'cisco'=>'cisco',
					'ciscospa'=>'ciscospa',
					'gigaset'=>'gigaset',
					'panasonic'=>'panasonic',
					'polycom'=>'polycom',
					'snom'=>'snom',
					'yealink'=>'yealink'
					);
					
$f2b_target = '/etc/fail2ban/jail.conf';

if ($netaddress == '0.0.0.0') {
	print "No IP from ifconfig  - got $netaddress \n";
}
else {
	print "Setting local subnet as $netaddress/$cidr \n";
	if ( file_exists( "/etc/shorewall") ) {
		`echo LAN=$netaddress/$cidr > /etc/shorewall/local.lan`;
		`echo IF1=$interface > /etc/shorewall/local.if1`;
		# shorewall pre 4.5.13
		`sed -i '/^BLACKLISTNEWONLY=/c\\BLACKLISTNEWONLY=NO' /etc/shorewall/shorewall.conf`;
		# shorewall 4.5.13+
		`sed -i '/^BLACKLIST=/c\\BLACKLIST=ALL' /etc/shorewall/shorewall.conf`;
	}
	
	if ( file_exists( "/etc/fail2ban/jail.local")) {	
		`sed -i --follow-symlinks '/^ignoreip/c \ignoreip = 127.0.0.1 $netaddress\/$cidr 224.0.1.0\/24' /etc/fail2ban/jail.local`;
		`fail2ban-server reload > /dev/null`;
	}
		
	if ( file_exists( "/etc/asterisk")) {
		# set correct Asterisk dateformat in logger.conf
		`sed -i 's/^;dateformat=%F %T /dateformat=%F %T/' /etc/asterisk/logger.conf`;
		`sed -i '/^messages/c \messages => security,notice,warning,error' /etc/asterisk/logger.conf`;
		# set localnet values for Asterisk
		`[ -e /etc/asterisk/sark_sip_localnet.conf ] && /usr/bin/dos2unix /etc/asterisk/sark_sip_localnet.conf`;
		`echo localnet=$netaddress/$msk >> /etc/asterisk/sark_sip_localnet.conf`;
		`awk '!_[\$0]++'  /etc/asterisk/sark_sip_localnet.conf > /tmp/localnet.tmp`;
		`mv /tmp/localnet.tmp /etc/asterisk/sark_sip_localnet.conf`;
		`chown asterisk:asterisk /etc/asterisk/sark_sip_localnet.conf`;
		`chmod 664 /etc/asterisk/sark_sip_localnet.conf`;
		`asterisk -rx 'reload' > /dev/null`;
	}
	
	if ( file_exists( "/etc/dnsmasq.d" )) {
		`tail /etc/dnsmasq.d/sarkdhcp-opt66 > /etc/dnsmasq.d/sarkdhcp-opt66`;
		foreach ($Phonelist as $phone) {
			$query = '';
			if ($phone == 'snom') {
				$query = '?mac={mac}';
			}
			if ($phone == 'panasonic') {
				$query = '?mac={mac}';
			}					 
			if ($phone == 'ciscospa') {
				$query = '/\$MAU';
			}		
			`echo "dhcp-option=$phone,option:tftp-server,\"http://$ip/provisioning$query\"" >> /etc/dnsmasq.d/sarkdhcp-opt66`;
		}
	}
}

?>		
