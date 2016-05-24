#!/usr/bin/perl

#####################################################################
#
# Copyright 2007, amooma GmbH, Bachstr. 126, 56566 Neuwied, Germany,
# http://www.amooma.de/
# Stefan Wintermeyer <stefan.wintermeyer@amooma.de>
# Philipp Kempgen <philipp.kempgen@amooma.de>
# Peter Kozak <peter.kozak@amooma.de>
# CoCo <admin@aelintra.com> modified for SARK/SAIL
# CoCo <admin@aelintra.com> added support for Yealink
#
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

#use warnings;
use bytes;
no locale;
use lib '/opt/sark/perl/modules';
use IO::Socket::Multicast;
use POSIX ();
use Net::Domain qw(hostname);
use FindBin ();
use File::Basename ();
use File::Spec::Functions ();
use sark::SarkSubs;
use Sys::Syslog;

# make the daemon cross-platform, so exec always calls the script
# itself with the right path, no matter how the script was invoked.
my $script = File::Basename::basename($0);
my $SELF = File::Spec::Functions::catfile $FindBin::Bin, $script;

my $app_name = 'responder';
$0 = $app_name;

#my $has_tty = (-t STDIN && -t STDOUT);

#
# set debug level to 2 and run --nofork for diagnostics
#
my $debug_level = 2;

sub get_first_line {
	my($str) = @_;
	if (! defined($str)) {
		return '';
	}
	$str =~ s/[\n\r].*//so;
	return $str;
}

sub doLogit($)
{
	my $logmsg = shift;
	syslog('info', $logmsg);
}

my $prov_scheme = '';
my $prov_host = '';
my $prov_port = '';
my $prov_path = '';
my $prov_url_base = '';

sub read_config {
	if ($debug_level >= 1) {
	doLogit("Reading configuration ...");
	}


	$prov_scheme = 'http';
	$prov_host = SarkSubs::ret_localip();
	$prov_port = '80';
	$prov_path = 'provisioning';
	

	if ($prov_scheme ne 'http' && $prov_scheme ne 'https') {
		doLogit("Found unknown scheme \"$prov_scheme\"!\n");
	exit 1;
	}

	if (substr($prov_path,0,1) ne '/') { 
		$prov_path = '/'.$prov_path; 
	}
#
#	if (substr($prov_path,-1 ) ne '/') { 
#		$prov_path = $prov_path.'/'; 
#	}
#
	$prov_url_base = $prov_scheme.'://'.$prov_host;
	$prov_port = int($prov_port);
	if ($prov_port != 0) {
		if ($prov_scheme eq 'http' && $prov_port != 80) {
			$prov_url_base.= ':'.$prov_port;
		}
		elsif ($prov_scheme eq 'https' && $prov_port != 443) {
			$prov_url_base.= ':'.$prov_port;
		}
	}
	$prov_url_base.= $prov_path;

	if ($debug_level >= 1) {
		doLogit("Using provisioning URL base: $prov_url_base\n");
	}
}

sub restart {
#	exec($SELF, @ARGV) or die "Couldn't restart: $!\n";
	exec ('/usr/bin/perl', $SELF, @ARGV) or die "Couldn't restart: $!\n";
}

sub sighup_handler {
	if ($debug_level >= 1) {
		doLogit("Got SIGHUP.\n");
	}
	restart();
}

my $sip_default_port = 5060;
my $my_sip_ip_addr = '224.0.1.75'; # sip.mcast.net
my $my_sip_ip_port = $sip_default_port;

$SIG{PIPE} = 'IGNORE';
#$SIG{HUP} = \&sighup_handler;
$SIG{__WARN__} = 'IGNORE';

# POSIX unmasks the sigprocmask properly
my $sigset = POSIX::SigSet->new();
my $action = POSIX::SigAction->new('sighup_handler', $sigset, &POSIX::SA_NODEFER);
POSIX::sigaction(&POSIX::SIGHUP, $action);

sub daemonize {
#	chdir '/' or die "Can't chdir to \"/\" ($!)";
# 	somehow breaks things
	close(STDIN);
	open STDIN , '</dev/null' or die "Can't read /dev/null ($!)";
	close(STDOUT);
	open STDOUT, '>>/dev/null' or die "Can't write to /dev/null ($!)";
	defined(my $pid = fork()) or die "Can't fork ($!)";
	if ($pid) {
		exit 0;
	}
	POSIX::setsid() or die "Can't start a new session ($!)";
	close(STDERR);
	open STDERR, '>&STDOUT' or die "Can't dup stdout ($!)";
	open STDOUT, '>>/dev/null' or die "Can't write to /dev/null ($!)";
}

#
# Let the work begin...
#

if ($ARGV[0] && $ARGV[0] eq '--daemonize') {
	doLogit("forking");
	daemonize();
}
elsif ($ARGV[0] && $ARGV[0] eq '--nofork') {
	if ($debug_level >= 1) {
		doLogit("Not forking");
	}
}
else {
	print STDERR "Use --daemonize or --nofork\n";
	exit 1;
}


read_config();


# create multicast socket
my $sock = IO::Socket::Multicast->new(
	Proto => 'udp',
	LocalPort => $my_sip_ip_port,
	LocalAddr => $my_sip_ip_addr, # / '0.0.0.0' ?
	Reuse => 1,
	ReuseAddr => 1
)
or die "socket: $@\n"; # yes, it uses $@ here

if ($debug_level >= 1) {
	#print STDOUT "Listening on $my_sip_ip_addr:$my_sip_ip_port ...\n";
	doLogit("Listening on " . $sock->sockhost() . ":" . $sock->sockport() . " ...");
}

# join multicast group (on INADDR_ANY = 0.0.0.0 = "any")
$sock->mcast_add( $my_sip_ip_addr );

# turn off local mirroring
$sock->mcast_loopback(0);

# set TTL (default 1)
$sock->mcast_ttl(255);


# SIP Event Notification:
# http://tools.ietf.org/html/rfc3265
# SIP UA Profile Event Package:
# http://tools.ietf.org/html/draft-ietf-sipping-config-framework-15
# http://tools.ietf.org/html/draft-channabasappa-sipping-app-profile-type-03
#
# Snom 3xx:
# http://wiki.snom.com/SIP_Traces#PnP_Config

# other drafts:
# http://tools.ietf.org/html/draft-petrie-sip-config-framework-01
# http://www.cs.columbia.edu/sip/drafts/sip/draft-schulzrinne-sip-config-events-00.txt


if ($debug_level >= 1) {
	doLogit("Interface: " .  $sock->mcast_if() );
	doLogit("Interface: " .  $sock->mcast_dest() );
}

my $remote_addr = $my_sip_ip_addr;
my $remote_port = $sip_default_port;
$resp = 'NOTIFY '. $remote_addr.':'.$remote_port .' SIP/2.0' ."\n";
if ($debug_level >= 1) {
	doLogit("OUT-------------------------------------------------------{\n");
	foreach (split(/\n/,$resp)) {
		s/\r//g;
		doLogit($_);
	}
	doLogit("----------------------------------------------------------}\n");
}
$sock->mcast_send( $resp, $remote_addr.':'.$remote_port );


my @x = unpack_sockaddr_in($sock->mcast_dest());
if ($debug_level >= 1) {
	doLogit("Interface: " . $sock->mcast_if() );
	doLogit("Interface: " . inet_ntoa($x[1]) );
}
my $in = '';
my $buf = '';
my $pkt = '';
my $resp = '';

#
# Main loop
#

while (1) {
#
#   wait for asterisk
#
    my $ast_data = `/usr/sbin/asterisk -rx 'core show channels'`;
    if ($?) {
		if ($debug_level >= 2) {
			doLogit("srk_ua_responder Waiting for Asterisk");
		}
		sleep 10;
		next;
	}	
	my $dbh = SarkSubs::SQLiteConnect();
    my $sipmcast = SarkSubs::SQLiteGet($dbh, "SELECT SIPMULTICAST FROM globals where pkey = 'global'");
	my $ztp = SarkSubs::SQLiteGet($dbh, "SELECT ZTP FROM globals where pkey = 'global'");
# check if I'm an inactive standby node in a HA cluster and override mcast if I am 
    my $rank = SarkSubs::SQLiteGet($dbh, "SELECT HASECNODE FROM globals") ;

    SarkSubs::SQLiteDisconnect($dbh);
    unless ($sipmcast eq 'enabled') {		
		if ($debug_level >= 2) {
			doLogit("inactive");
		}
		sleep 10;
		next;
	}
	while ($sock->recv($in, 8192)) {
		$buf .= $in;
		while ($buf =~ m/^(.*?)\r\n\r\n/so) {
#			while ($buf =~ m/^((?:[^\r\n]+\r\n)*)\r\n/so) {
			if (! defined($&)) {
				next;
			}
			$buf = substr($buf, length($&));
			if (! defined($1)) {
				next;
			}
			$pkt = $1;
			if ($pkt eq '') {
				next;
			}

			if ($debug_level >= 2) {
				doLogit("IN -------------------------------------------------------{");
				foreach (split(/\n/,$pkt)) {
					s/\r//g;
					doLogit($_);
				}
				doLogit("----------------------------------------------------------}");
			}
			if ($pkt =~ m/^SUBSCRIBE/sio) {
#
# read headers
#

#				my $remote_addr = '224.0.1.75';
				my $remote_addr = '';
				my $remote_port = $sip_default_port;
				if ($pkt =~ m/^(?:Contact|m):\s*([^\r\n]*)\r\n/mso) {
					my $tmp = $1;
					if ($tmp =~ m/([0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3})/so) {
						$remote_addr = $1;
					}
					if ($tmp =~ m/:([0-9]{4,5})/so) {
						$remote_port = $1;
					}
				}
				if ($remote_addr eq '') {
					next;
				}

				my $in_header_via = '';
				my $in_header_from = '';
				my $in_header_to = '';
				my $in_header_callid = '';
				my $in_header_cseq = '';
				my $in_header_event = '';
				my $in_header_accept = '';
				my $in_header_expires = 0;

				my $in_event_type = '';
				my $in_event_ua_profile_type = '';
				my $in_event_ua_profile_vendor = '';
				my $in_event_ua_profile_model = '';
				my $in_event_ua_profile_version = '';
				my $macaddr;

				if ($pkt =~ m/^(?:Via|v):\s*([^\r\n]*)\r\n/msio) {
# FIXME - Via can occur more than once
					$in_header_via = $1;
				}
				if ($pkt =~ m/^(?:From|f):\s*([^\r\n]*)\r\n/msio) {
					$in_header_from = $1;
# get MAC address - Yealink doesn't send the %3a sequence, Snom and Gigaset do.
					$pkt =~ /MAC(%3[a|A])?([0-9A-Fa-f]{12})/; 
					$macaddr = lc($2);
					if ($debug_level >= 1) {
						doLogit("Harvested MAC address $macaddr");
					}					
				}
				if ($pkt =~ m/^(?:To|t):\s*([^\r\n]*)\r\n/msio) {
					$in_header_to = $1;
				}
				if ($pkt =~ m/^(?:Call-ID|i):\s*([^\r\n]*)\r\n/msio) {
					$in_header_callid = $1;
				}
				if ($pkt =~ m/^(?:CSeq):\s*([^\r\n]*)\r\n/msio) {
					$in_header_cseq = $1;
				}
				if ($pkt =~ m/^(?:Expires):\s*([^\r\n]*)\r\n/msio) {
#					$resp.= $&;
					$in_header_expires = int($1);
#if ($in_header_expires > 3600) {$in_header_expires = 3600;}
#elsif ($in_header_expires <= 0) {$in_header_expires = 1800;}
#elsif ($in_header_expires < 60) {$in_header_expires = 60;}
				}
				if ($pkt =~ m/^(?:Accept):\s*([^\r\n]*)\r\n/msio) {
					$in_header_accept = $1;
				}

				if ($pkt =~ m/^(?:Event|o):\s*([^\r\n]*)\r\n/msio) {
					$in_header_event = $1;
					if ($in_header_event =~ m/^ua-profile/io) {
# Event: ua-profile;profile-type="device";vendor="snom";model="snom370";version="7.1.24"
						$in_event_type = 'ua-profile';
						if ($in_header_event =~ m/;\s*profile-type\s*=\s*["']?([^"';]*)/io) {
							$in_event_ua_profile_type = $1;
						}
# The silly buggers at Panasonic can't spell vendor !!!
						if ($in_header_event =~ m/;\s*vend[e|o]r\s*=\s*["']?([^"';]*)/io) {
							$in_event_ua_profile_vendor = $1;
							if ($debug_level >= 1) {
								doLogit("Harvested Vendor $in_event_ua_profile_vendor");
							}
						}
						if ($in_header_event =~ m/;\s*model\s*=\s*["']?([^"';]*)/io) {
							$in_event_ua_profile_model = $1;
							if ($debug_level >= 1) {
								doLogit("Harvested Model $in_event_ua_profile_model");
							}							
						}
						if ($in_header_event =~ m/;\s*version\s*=\s*["']?([^"';]*)/io) {
							$in_event_ua_profile_version = $1;
						}
					}
				}

#
# build default OK response
#

				$resp = 'SIP/2.0 200 OK' ."\r\n";
				if ($in_header_via ne '') {
					$resp.= 'Via: '. $in_header_via ."\r\n";
				}
				if ($in_header_from ne '') {
					$resp.= 'From: '. $in_header_from ."\r\n";
				}
				if ($in_header_to ne '') {
					$resp.= 'To: '. $in_header_to ."\r\n";
				}
				if ($in_header_callid ne '') {
					$resp.= 'Call-ID: '. $in_header_callid ."\r\n";
				}
				if ($in_header_cseq ne '') {
					$resp.= 'CSeq: '. $in_header_cseq ."\r\n";
				}
				if ($in_header_expires != -1) {
					$resp.= 'Expires: '. $in_header_expires ."\r\n";
				}
				$resp.= 'Contact: <sip:'.$my_sip_ip_addr.':'.$my_sip_ip_port.'>' ."\r\n";
				$resp.= 'User-Agent: '. $app_name ."\r\n";
				$resp.= 'Content-Length: 0' ."\r\n";




#
#	make decision to respond/ignore
#


				if ($in_event_type eq 'ua-profile') {
#
# Harvest the information for later use by the discover program
#
					my $dbh = SarkSubs::SQLiteConnect();
					my $checkmac = SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM netphone where pkey = '$macaddr'");
					unless ($checkmac) {
						SarkSubs::SQLiteDo($dbh, "INSERT INTO netphone (pkey,vendor,model) 	
							VALUES ('$macaddr','$in_event_ua_profile_vendor','$in_event_ua_profile_model')" );
					}
					SarkSubs::SQLiteDisconnect($dbh);

#if (($pkt =~ m/MAC%3[aA](000413[^@]+)@/sio || $pkt =~ m/snom/so) && $pkt =~ m/^(?:Accept):\s*application\/url/msio) {
#				if ($in_event_ua_profile_vendor eq 'snom') {
# Snom 3xx
# http://wiki.snom.com/SIP_Traces#PnP_Config

					if ($in_header_accept !~ m/\bapplication\/url\b/io) {
						next;
					}

					if ($debug_level >= 2) {
						doLogit("OUT (-> $remote_addr:$remote_port) -------------------------------{");
						foreach (split(/\n/,$resp)) {
							s/\r//g;
							doLogit($_);
						}
						doLogit("----------------------------------------------------------}");
					}
					$resp.= "\r\n";
					$sock->mcast_send( $resp, $remote_addr.':'.$remote_port );
					select(undef,undef,undef, 0.01); # sleep 0.01 s = 10 ms

					my $out_header_to = $in_header_from;
					my $out_header_from = $in_header_to;
					if ($out_header_from !~ m/;\s*tag\s*=\s*/io) {
						$out_header_from.= ';tag='. sprintf('%x-%x', time(), int(rand(999999999)));
					}
					$resp = 'NOTIFY '. $remote_addr.':'.$remote_port .' SIP/2.0' ."\r\n";
					$resp.= 'Via: SIP/2.0/UDP '.$my_sip_ip_addr.':'.$my_sip_ip_port.';rport' ."\r\n";
					$resp.= 'Max-Forwards: 25' ."\r\n";
					$resp.= 'Contact: <sip:'.$my_sip_ip_addr.':'.$my_sip_ip_port.'>' ."\r\n";
					$resp.= 'To: '. $out_header_to ."\r\n";
					$resp.= 'From: '. $out_header_from ."\r\n";
					$resp.= 'Call-ID: '. $in_header_callid ."\r\n";
					$resp.= 'CSeq: 3 NOTIFY' ."\r\n";					
					$resp.= 'Content-Type: application/url' ."\r\n";
					$resp.= 'User-Agent: '. $app_name ."\r\n";
					$resp.= 'Subscription-State: terminated;reason=timeout' ."\r\n";
					$resp.= 'Event: '. $in_event_type ."\r\n";
					

#
#	Response String
#
#   if it's a snom tell it to send its mac address in the query
#
					my $body = $prov_url_base;
					if ($in_event_ua_profile_vendor eq 'snom' || $in_event_ua_profile_vendor eq 'Panasonic') {
						$body = $prov_url_base . '?mac={mac}';
					}

#					if ($in_event_ua_profile_vendor = 'Gigaset') {
#						$body = $prov_url_base . '/';
#					}									
					$resp.= 'Content-Length: '. length($body) ."\r\n";
					$resp.= "\r\n";
					$resp.= $body;

					
					my $bytes = 0;
# ZTP
					my $dbh = SarkSubs::SQLiteConnect();
					my $macisknown = SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM ipphone where lower(macaddr) = '$macaddr'") || '';
					SarkSubs::SQLiteDisconnect($dbh);
# 
#	If we already know the phone, then just respond normally.
#   if we don't know the phone and ztp is OFF then don't respond
#   If we don't know the phone and ztp is ON then try to build an
#     extension on the fly.
#

					unless ($macisknown) {
						if ($ztp eq 'enabled') {							
								unless (createExten($in_event_ua_profile_model,$macaddr)) {
#									sleep 2;
									$bytes = $sock->mcast_send( $resp, $remote_addr.':'.$remote_port );
									if ($debug_level >= 2) {
										doLogit("OUT-------------------------------------------------------{");
										foreach (split(/\n/,$resp)) {
											s/\r//g;
											doLogit($_);
										}
										doLogit("----------------------------------------------------------}");
									}
								}
								else {
									if ($debug_level >= 1) {
										doLogit("ZTP is ON but unsupported device $in_event_ua_profile_model at $remote_addr - ignored");
									}							
								}
						}
						else {
							if ($debug_level >= 1) {
								doLogit("Request from unknown end-point at $remote_addr and ZTP is OFF - ignored");
							}
						}
					}
					else {
							$bytes = $sock->mcast_send( $resp, $remote_addr.':'.$remote_port );
							if ($debug_level >= 2) {
								doLogit("OUT-------------------------------------------------------{");
								foreach (split(/\n/,$resp)) {
									s/\r//g;
									doLogit($_);
								}
								doLogit("----------------------------------------------------------}");
							}							
					}
				} # end if ($in_event_type eq 'ua-profile')
			} # end if ($pkt =~ m/^SUBSCRIBE/sio)
		} # end while ($buf =~ m/^(.*?)\r\n\r\n/so)
	} # end while ($sock->recv($in))
sleep(1);
}

sub createExten($$) {
	
	my $vendordevice = shift;
	my $macaddr = shift;
	
	my $dbh = SarkSubs::SQLiteConnect();
		
# check vendor device type is in the database
	my $device =  SarkSubs::SQLiteGet($dbh, "SELECT intpkey FROM vendorxref where pkey = '$vendordevice'") ;
	unless ($device) {
		if ($debug_level >= 1) {
			doLogit("Unknown Vendor device type $vendordevice - can't create extension");
		}
		SarkSubs::SQLiteDisconnect($dbh);
		return (-1);
	}

# Do clf check
	my $count = SarkSubs::SQLiteGet($dbh,"SELECT count(*) FROM ipphone");
	if (-e "/opt/sark/scripts/srkdclf") { 
		my $extlim = `/opt/sark/scripts/srkdclf`;
		unless ($?) {
			if ($count >= $extlim) {
				doLogit("Extension limit exceeded - can't create extension");
				return (-1);
			}
		}
	}
	my $extlim = SarkSubs::SQLiteGet($dbh,"SELECT EXTLIM FROM globals where pkey = 'global'");		
	if ( $extlim && $count >= $extlim) {
		doLogit("Extension limit exceeded - can't create extension");
		return -1;
	} 	
				
# get an extension key	
    my $pkey = SarkSubs::SQLiteGet($dbh, "SELECT SIPIAXSTART FROM globals where pkey = 'global'");
    my $pwdlen = SarkSubs::SQLiteGet($dbh, "SELECT PWDLEN FROM globals where pkey = 'global'") || 8;
    while (SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM IPphone where pkey = '$pkey'"))  {
	$pkey++;
    }
    
# get the template 

    my $sipiaxfriend = SarkSubs::SQLiteGet($dbh, "SELECT sipiaxfriend FROM Device where pkey = '$device'");
    my $provision 	 = SarkSubs::SQLiteGet($dbh, "SELECT provision FROM Device where pkey = '$device'"); 
    my $blfkeyname	 = SarkSubs::SQLiteGet($dbh, "SELECT blfkeyname FROM Device where pkey = '$device'"); 
    my $blfkeys	 	 = SarkSubs::SQLiteGet($dbh, "SELECT blfkeys FROM Device where pkey = '$device'");
    my $passwd	 	 = SarkSubs::sark_password($pwdlen);    
    my $desc = "Ext".$pkey;
	my $callerid = $pkey;
	my $dvrvmail = $pkey;
	my $ipaddr = SarkSubs::ret_localip();
    my $ipbase = SarkSubs::ret_subnet();
    my $nmask = SarkSubs::ret_subnetmask();
    	
# tailor the template

#	
#    if ($sipiaxfriend =~ /callerid=/) {
#     		$sipiaxfriend =~ s/callerid=/callerid=\"\$desc\" <\$ext>/;
#    }
#    else {
#		$sipiaxfriend .= "\ncallerid=\"\$desc\" <\$pkey>";
#    }
#    if ($sipiaxfriend =~ /username=/) {
#    		$sipiaxfriend =~ s/username=/username=\$desc/;
#    }
#    else {
#		$sipiaxfriend .= "\nusername=\$desc";
#    }
#   if ($sipiaxfriend =~ /secret=/) {
#    		$sipiaxfriend =~ s/secret=/secret=\$password/;
#    }
#    else {
#		$sipiaxfriend .= "\nsecret=\$password";
#    }
#    if ($sipiaxfriend =~ /mailbox=/) {
#		$sipiaxfriend =~ s/mailbox=/mailbox=\$ext/;
#    }
#    else {
#		$sipiaxfriend .= "\nmailbox=\$ext";
#    }
#    if ($sipiaxfriend =~ /pickupgroup=/) {
#		$sipiaxfriend =~ s/pickupgroup=/pickupgroup=1 \ncallgroup=1/;
#    }
#    else {
#		$sipiaxfriend .= "\npickupgroup=1\ncallgroup=1";
#    }
#    unless ($sipiaxfriend =~ /call-limit/) {
#                $sipiaxfriend .= "\ncall-limit=3";
#    }
#    unless ($sipiaxfriend =~ /subscribecontext/) {
#                $sipiaxfriend .= "\nsubscribecontext=extensions";
#    }
    
# set ACL
	my $acl 		 = SarkSubs::SQLiteGet($dbh, "SELECT ACL FROM globals where pkey = 'global'") || 'NO'; 
	if ($acl eq "YES") {
		unless ($sipiaxfriend =~ /deny=/) {
			$sipiaxfriend .= "\ndeny=0.0.0.0/0.0.0.0";
		}
		unless ($sipiaxfriend =~ /permit=/) {
			$sipiaxfriend .= "\npermit=$ipbase/$nmask";
		}
	}
	else {
		$sipiaxfriend =~ s/^.*deny=.*$//;
		$sipiaxfriend =~ s/^.*permit=.*$//;
    }
     
    $sipiaxfriend =~ s/^\s+//;
    $sipiaxfriend =~ s/\s+$//;
    $sipiaxfriend =~ s/\r//g;

	$provision = "#INCLUDE $device";
	if ($blfkeyname) {
		$provision .= "\n#INCLUDE $blfkeyname";
	}
    
# insert the extension   

	SarkSubs::SQLiteDo($dbh, "INSERT INTO ipphone (pkey,active,callerid,cluster,desc,device,devicerec,dvrvmail,location,macaddr,passwd,provision,sndcreds,sipiaxfriend,technology) 	
		VALUES ('$pkey','YES','$pkey','default','$desc','$device','default','$dvrvmail','local','$macaddr','$passwd','$provision','Always','$sipiaxfriend','SIP')" );


	if ($debug_level >= 2) {
		doLogit("Inserted new extension $pkey with MAC $macaddr");
	}		
#
#               doCOS
#
	my @Cos = SarkSubs::SQLiteGetKeys($dbh, "SELECT pkey FROM COS");
	foreach (@Cos) {
       	if (SarkSubs::SQLiteGet($dbh, "SELECT defaultopen FROM COS where pkey = '$_'") eq "YES" ) {
				SarkSubs::SQLiteDo($dbh, "INSERT INTO IPphoneCOSopen  (IPphone_pkey,COS_pkey) VALUES ('$pkey','$_')" );
         }
         if (SarkSubs::SQLiteGet($dbh, "SELECT defaultclosed FROM COS where pkey = '$_'") eq "YES" ) {
				SarkSubs::SQLiteDo($dbh, "INSERT INTO IPphoneCOSclosed  (IPphone_pkey,COS_pkey) VALUES ('$pkey','$_')" );
         }
    }
    SarkSubs::SQLiteDisconnect($dbh);
# do a regen 
	SarkSubs::sysCommit($q);
# and out...
	
}
	
	
	
	
	
