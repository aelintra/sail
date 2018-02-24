#!/usr/bin/perl

#####################################################################
#
# Copyright 2012, aelintra telecom
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston,
# MA 02110-1301, USA.
#####################################################################


use lib '/opt/sark/perl/modules';
use sark::SarkSubs;
use Sys::Syslog;
#syslog('info', "Enforcer -> Running");
my $app_name = 'sark-ua-enforcer';
$0 = $app_name;
my $showchan = "core show channels";		#Asterisk 1.6/8 
my $softhangup = "channel request hangup";	#Asterisk 1.6/8 


#
# hangup any running external calls for a cluster which has gone zero on credit
#
	my $dbh = SarkSubs::SQLiteConnect();
	my $extlen = SarkSubs::SQLiteGet($dbh, "SELECT EXTLEN FROM globals where pkey = 'global'");	
	my $extplus = $extlen++;
	my $astdlim = SarkSubs::SQLiteGet($dbh, "SELECT ASTDLIM FROM globals where pkey = 'global'");
    my @Cluster = SarkSubs::SQLiteGetKeys($dbh, "SELECT pkey FROM cluster");
    
	if ($astdlim eq "|") {
		$showchan = "show channels";			#Asterisk 1.4	
		$softhangup = "soft hangup";			#Asterisk 1.4
	}

    
    foreach $Cluster (@Cluster) {
		if  (SarkSubs::SQLiteGet($dbh, "SELECT abstimeout FROM cluster where pkey = '$Cluster'") == 0) {
			my @Exten = SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM ipphone where cluster = '$Cluster'");
			foreach $Exten (@Exten) {
				my @Channel = `/usr/sbin/asterisk -rx "$showchan"`;
#				foreach (@Channel) {
#					syslog('info', "Enforcer -> $_");
#				}
				foreach (@Channel) {
					if ( /^(SIP\/$Exten-\d{6,})\s*(\d{$extplus,})@/) {
						`/usr/sbin/asterisk -rx "$softhangup $1"`;
						syslog('info', "Enforcer -> Hung up $1 out of credit");
					}
				}
			}
		}
	}
	
    SarkSubs::SQLiteDisconnect($dbh);
# and out...
	
	
	
	
	
