#! /usr/bin/perl -w
#
#
#
#	Copyrigt Aelintra Telecom Limited(2008-19), all rights reserved
#
#  N.B. requires db and sark.db to be 664 and www-data to be a member of group asterisk 
#

use strict;
use Sys::Syslog;
use IO::Socket::INET;
use DBI;

sub doLogin ();
sub doLogoutUser ($);
sub doReset ($);
sub doPrune ();
sub doLogit($);
sub SQLiteDisconnect($);
sub SQLiteGet($$);
sub SQLiteGetKeys($$);
sub SQLiteDo($$);
sub request_syscmd ($);

my $app_name = 'sarkuahd';
$0 = $app_name;
my $t = localtime( );


my $debug=1;  # set to 1 for log output
my $syslog=1; # 1 to log to the system log, 0 to log to /var/log/aelhdlon_log 

if ($debug) {
	if ($syslog) {
		$syslog = openlog($0,'','user');
	}
	else {
		open STDOUT, '>>/var/log/aelhdlon_log' or die "$t\t Aelhdlon102 ==> Can't write to log: $!\n";
	}
}

my ($action, $exten, $clid) = @ARGV;
doLogit("AELHD invoked with command=$action, exten=$exten, CLID=$clid");


unless ($action) {
	doLogit("Syntax Error - invoked without an action command");
    exit (4);
}


my $maclong;
my $mfgmac;
my $notify;
my $clidevice;
my $clitenant;
my $extentenant;


if ($action eq 'prune') {
        doPrune;
        exit 0;
}

my $dbh = DBI->connect( "dbi:SQLite:dbname=/opt/sark/db/sark.db","", "", { RaiseError => 1, AutoCommit => 1 })
	or die "Unable to connect: $DBI::errstr\n";

if ($action eq 'login') {
       $maclong = SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$clid'");
       $clidevice = SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$clid'"); 
       $clitenant = SQLiteGet($dbh, "SELECT cluster FROM ipphone where pkey = '$clid'"); 
       $extentenant = SQLiteGet($dbh, "SELECT cluster FROM ipphone where pkey = '$exten'");
# check tenants match
	   if  ($clitenant ne $extentenant) {
	   	doLogit("VXT exten and target phone are in different tenants! exten->$exten, CLID->$clid");
		exit (4);
	   } 
	   else {
	   	doLogit("VXT exten and target phone are in same tenant, proceeding... exten->$exten, CLID->$clid tenant->$clitenant");
	   }         
}
else {
        $maclong = SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$exten'");
}

my $device = SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$exten'");
unless ($maclong) {
	unless ( $device =~ /VXT/) {
		doLogit("Target phone is not provisioned exten->$exten, CLID->$clid");
		exit (4);
    }
    doLogit("VXT User $exten has logoff request from another extension but is not logged in.");
	exit (4);
}

$maclong =~ /^(\w{2})(\w{2})(\w{2})/ ;
$mfgmac = uc($1).":".uc($2).":".uc($3);
$notify  = SQLiteGet($dbh, "SELECT notify FROM mfgmac where pkey = '$mfgmac'");
my $name = SQLiteGet($dbh, "SELECT name FROM mfgmac where pkey = '$mfgmac'");

unless ($notify) {
	doLogit("No notify procedure found for exten->$exten, CLID->$clid, mfgmac->$mfgmac, maclong->$maclong");
	exit (4);
}

if ($action eq 'login') {
	doLogin;
}

if ($action eq 'logout') {
	doLogoutUser($exten);
}

if ($action eq 'reset') {
	doReset($exten);
}

SQLiteDisconnect($dbh);
exit 0;

# end of mainline

sub doLogin () {
	doLogit("Running login routines for exten->$exten, CLID->$clid, mfgmac->$mfgmac, maclong->$maclong");
#	my $device = SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$exten'");
	unless ($device =~ /VXT/) {
		doLogit("Trying to login with non-VXT extension exten->$exten, device->$device ");
		exit (4);
	}
# check device types match	
	unless ($device =~ $clidevice) {
# allow Yealinks and Snoms to cross-map
		unless ($device =~ /^Yealink/ && $clidevice =~ /^Yealink/) {
			unless ($device =~ /^S|snom/ && $clidevice =~ /^S|snom/) {
				doLogit("Trying to log in on wrong device type - device->$device, clidevice->$clidevice,  exten->$exten, CLID->$clid ");
				exit (4);
			}
		}
}

# Am I already logged in?    
	if ( SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$exten'") ) {
		doLogit("Already logged on - exten->$exten, CLID->$clid");
# On this phone?
		if ( $exten eq $clid ) {
        	doLogit("VXT attempting login but is already logged on to this extension exten->$exten, CLID->$clid ");
			exit (4);
   		}
       	doLogoutUser($exten);
 # give the logged in phone a few seconds to clear off and re-register      	
       	sleep(5);
	}
	
    my $clidDevice = SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$clid'");   
    
  # Is this phone already stolen?  
	if ( $clidDevice =~ /VXT/ ) {
			doLogit("Override VXT User $clid with User $exten ");
			my $savexten = $exten;
			$exten = $clid;
			$clid = SQLiteGet($dbh, "SELECT stolen FROM ipphone where pkey = '$exten'");
			doLogit("origin clid is $clid ");	
			doLogoutUser($exten);
 # give the logged in phone a few seconds to clear off and re-register      	
       		sleep(5);			
			$exten = $savexten;
	}
	doLogit("Sense checks passed for login - device->$device, clidevice->$clidevice,  exten->$exten, CLID->$clid ");	
    my $leasehdtime = SQLiteGet($dbh, "SELECT LEASEHDTIME FROM globals where pkey = 'global'" || 43200);
	my $expires = time + $leasehdtime;

	doLogit("update  - device->$device, clidevice->$clidevice,  exten->$exten, CLID->$clid ");	
    SQLiteDo($dbh, "UPDATE ipphone SET macaddr=(select macaddr from ipphone where pkey='$clid') WHERE pkey='$exten'");
    if ($DBI::errstr) {
    	doLogit("update 1 failed with error " . $DBI::errstr);
    }
    SQLiteDo($dbh, "UPDATE ipphone SET stolen='$clid',stealtime='$expires' WHERE pkey='$exten'");
    SQLiteDo($dbh, "UPDATE ipphone SET basemacaddr = macaddr WHERE pkey='$clid'");
    SQLiteDo($dbh, "UPDATE ipphone SET macaddr=NULL,stolen='$exten',stealtime='$expires' WHERE pkey='$clid'");

	doLogit("VXT User $exten is acquiring $clid");	
	doLogit("Sending reset to $notify and $clid");
	request_syscmd ("/usr/sbin/asterisk -rx \"sip notify $notify $clid\" ");
#	`sudo /usr/sbin/asterisk -rx \"sip notify $notify $clid\"`;
}


sub doLogoutUser ($) {
	my ($exten) = shift; 
    my $stolen = SQLiteGet($dbh, "SELECT stolen FROM ipphone where pkey = '$exten'"); 

	if ($stolen) {
        SQLiteDo($dbh, "UPDATE ipphone SET macaddr=NULL,devicemodel=NULL,stolen=NULL,stealtime=NULL WHERE pkey='$exten'");
        SQLiteDo($dbh, "UPDATE ipphone SET macaddr = basemacaddr WHERE pkey='$stolen'");
        SQLiteDo($dbh, "UPDATE ipphone SET stolen=NULL,stealtime=NULL WHERE pkey='$stolen'");
		doLogit("VXT User $exten is releasing $stolen with $notify $exten");		

		request_syscmd ("/usr/sbin/asterisk -rx \"sip notify $notify $exten\" ");
#		`sudo /usr/sbin/asterisk -rx \"sip notify $notify $exten\" `;

	}
	else {
        doLogit("VXT User $exten wants to logoff but is not logged in.");	
	}

}

sub doReset ($) {

	my ($exten) = shift;
    SQLiteDo($dbh, "UPDATE ipphone SET macaddr=NULL,stolen=NULL,stealtime=NULL WHERE pkey='$exten'");
    doLogit("Reset $exten ");	

}


sub doPrune () {

	doLogit("Pruning Hotdesk entries");
    my $dbh = SQLiteConnect();
    my @IPphones = sort (SQLiteGetKeys($dbh, "SELECT pkey FROM IPphone" ));
    foreach (@IPphones) {
    	my $device = SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = $_");
    	if ($device =~ /VXT/) {
        	my $stealtime = SQLiteGet($dbh, "SELECT stealtime FROM ipphone where pkey = '$_'");
            if  ($stealtime && $stealtime <  time) {
            	doLogit("Found expired candidate $_");
				$exten = $_;
                $clid =  SQLiteGet($dbh, "SELECT stolen FROM ipphone where pkey = '$_'");
                doLogit("Pruning VXT User $exten on timeout ");
                $maclong = SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$_'");
                $maclong =~ /^(\w{2})(\w{2})(\w{2})/ ;
				$mfgmac = $1.":".$2.":".$3;
				$notify  = SQLiteGet($dbh, "SELECT notify FROM mfgmac where pkey = '$mfgmac'");
                doLogoutUser($_);
            }                       
        }
    }
    SQLiteDisconnect($dbh);
}

sub SQLiteConnect() {
    # make connection to database
	doLogit("IN CONNECT");
    $dbh = DBI->connect( "dbi:SQLite:dbname=/opt/sark/db/sark.db","", "", { RaiseError => 1, AutoCommit => 1 });
    return $dbh;
}

sub SQLiteDisconnect($) {
    my ($dbh) = shift;
    $dbh->disconnect;
}

sub SQLiteGet($$) {
	my ($dbh, $getcmd) = @_;
    doLogit("GET DIRECT - $getcmd \n");
    my $out;
    doLogit("SQLiteGet is running $getcmd\n");
    my $sth = $dbh->prepare($getcmd);
    $sth->execute();
    $sth->bind_columns(\$out);
    $sth->fetch();
    $sth->finish();
    if ($out) {
    	doLogit("SQLiteGet returned $out\n");
    }
    return $out;
}

sub SQLiteGetKeys($$) {
#
#   Return all of the keys for a given table
#
    my ($dbh, $getcmd) = @_;
#    print STDERR "GET KEYS - $getcmd \n";
    my $out;
    my @keys;
    my $sth = $dbh->prepare($getcmd);
    $sth->execute();
    $sth->bind_columns(\$out);
    while ($sth->fetch()) {
	push (@keys, $out);
    }
    $sth->finish();
    return @keys;
}

sub SQLiteDo($$) {
#
#   do the SQL and return nothing
#
    my ($dbh, $sqlcmd) = @_;
	doLogit("SQLDO - $sqlcmd\n");
    my $sth = $dbh->prepare($sqlcmd);
    $sth->execute();
    $sth->finish();
    undef $sth;
}


sub doLogit($)
{
	unless ($debug) {
		return;
	}
	my $logmsg = shift;
	if ($syslog) {
		syslog('info', $logmsg);
	}
	else {
		print "$t $logmsg \n";
	}
}

sub request_syscmd ($) {

	my $data = shift || return (-1);
	my ($socket,$client_socket);
# creating object interface of IO::Socket::INET modules which internally creates
# socket, binds and connects to the TCP server running on the specific port.
	$socket = new IO::Socket::INET (
		PeerHost => '127.0.0.1',
		PeerPort => '7601',
		Proto => 'tcp',
	) or die "ERROR in Socket Creation : $!\n";


# read the socket data sent by server.
	my $ack = <$socket>;
        my $ret;

# write on the socket to server.
	print $socket "$data\n";

while (	! /<<EOT>>/ ) {
 	$_ = <$socket>;
	unless (  /<<EOT>>/ ) {
        	$ret .= $_;
	}
#	print STDERR "$_";
}
$socket->close();
return ($ret);

}
		 
