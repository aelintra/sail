#!/usr/bin/perl
#----------------------------------------------------------------------
# heading     : Telephony
# description : network set IP
#
#
# Copyright (c) aelintra.com         2011
#
#----------------------------------------------------------------------
#----------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
#
# Technical support for this program is available from Selintra Ltd
# Please visit our web site www.selintra.com/ for details.
#----------------------------------------------------------------------


use lib '/opt/sark/perl/modules';
use strict;
use sark::SarkSubs;
my $netaddress = SarkSubs::ret_subnet ();
my $cidr = SarkSubs::ret_cidr ();
my $msk = SarkSubs::ret_subnetmask ();
my $ip = SarkSubs::ret_localip ();
my @Phonelist = qw ( aastra ciscospa gigaset polycom snom yealink );

if ($netaddress eq '0.0.0.0') {
#	$netaddress = '10.10.10.0';
#	$cidr = '24';
#	$msk = '255.255.255.0';
	print "No IP from ifconfig  - got $netaddress \n";
#	`ifconfig eth0 10.10.10.10 netmask 255.255.255.0 up`;
}
else {
	print "Setting local subnet as $netaddress/$cidr \n";
	if ( -e "/etc/shorewall" ) {
		`echo LAN=$netaddress/$cidr > /etc/shorewall/local.lan`;
		`/usr/bin/dos2unix /etc/asterisk/sark_sip_localnet.conf`;
		`echo localnet=$netaddress/$msk >> /etc/asterisk/sark_sip_localnet.conf`;
		`awk '!_[\$0]++'  /etc/asterisk/sark_sip_localnet.conf > /tmp/localnet.tmp`;
		`mv /tmp/localnet.tmp /etc/asterisk/sark_sip_localnet.conf`;
		`chown asterisk:asterisk /etc/asterisk/sark_sip_localnet.conf`;
		`chmod 664 /etc/asterisk/sark_sip_localnet.conf`;
		`asterisk -rx 'reload' > /dev/null`;
	}
	if ( -e "/etc/fail2ban") {
		`sed -i '/^ignoreip/c \ignoreip = 127.0.0.1 $netaddress\/$cidr 224.0.1.0\/24' /etc/fail2ban/jail.conf`;
		`fail2ban-server reload > /dev/null`;
	}
	if ( -e "/etc/dnsmasq" ) {
		`tail /etc/dnsmasq.d/sarkdhcp-opt66 > /etc/dnsmasq.d/sarkdhcp-opt66`;
		foreach (@Phonelist) {
			my $query = '';
			if ($_ eq 'snom') {
				$query = '?mac={mac}';
			}		 
			if ($_ eq 'ciscospa') {
				$query = '/\$MAU';
			}		
			`echo "dhcp-option=$_,option:tftp-server,\"http://$ip/provisioning$query\"" >> /etc/dnsmasq.d/sarkdhcp-opt66`;
		}
	}
}

