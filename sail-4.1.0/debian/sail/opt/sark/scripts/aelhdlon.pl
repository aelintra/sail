#! /usr/bin/perl -w
#
#
#
#	Copyrigt Aelintra Telecom Limited(2008-12), all rights reserved
#

use lib '/opt/sark/perl/modules';

use strict;
use sark::SarkSubs;
use Sys::Syslog;

sub doLogin ();
sub doLogoutUser ();
sub doReset ();
sub doPrune ();
sub doCommit ();
sub doLogit($);

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


unless ($action) {
	doLogit("Syntax Error - invoked without an action command $action");
    exit (4);
}


my $maclong;
my $mfgmac;
my $notify;
my $clidevice;


if ($action eq 'prune') {
        doPrune;
        exit 0;
}

my $dbh = SarkSubs::SQLiteConnect();
if ($action eq 'login') {
       $maclong = SarkSubs::SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$clid'");
       $clidevice = SarkSubs::SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$clid'");      
}
else {
        $maclong = SarkSubs::SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$exten'");
}

my $device = SarkSubs::SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$exten'");
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
$notify  = SarkSubs::SQLiteGet($dbh, "SELECT notify FROM mfgmac where pkey = '$mfgmac'");
my $name = SarkSubs::SQLiteGet($dbh, "SELECT name FROM mfgmac where pkey = '$mfgmac'");

unless ($notify) {
	doLogit("No notify procedure found for exten->$exten, CLID->$clid, mfgmac->$mfgmac, maclong->$maclong");
	exit (4);
}

if ($action eq 'login') {
	doLogin;
}

if ($action eq 'logout') {
	doLogoutUser;
}

if ($action eq 'reset') {
	doReset;
}

SarkSubs::SQLiteDisconnect($dbh);
exit 0;

sub doLogin () {

#	my $device = SarkSubs::SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$exten'");
	unless ($device =~ /VXT/) {
		doLogit("Trying to login with non-VXT extension exten->$exten, device->$device ");
		exit (4);
	}
# check device types match	
	unless ($device =~ $clidevice) {
# allow Yealinks to cross-map
		unless ($device =~ /^Yealink/ && $clidevice =~ /^Yealink/) {
			doLogit("Trying to log in on wrong device type - device->$device, clidevice->$clidevice,  exten->$exten, CLID->$clid ");
			exit (4);
		}
}

# Am I already logged in?    
	if ( SarkSubs::SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$exten'") ) {
		doLogit("Already logged on - exten->$exten, CLID->$clid");
# On this phone?
		if ( $exten eq $clid ) {
        	doLogit("VXT attempting login but is already logged on to this extension exten->$exten, CLID->$clid ");
			exit (4);
   		}
       		doLogoutUser;
	}
	
# Is this phone already stolen?
    my $clidDevice = SarkSubs::SQLiteGet($dbh, "SELECT device FROM ipphone where pkey = '$clid'");
	if ( $clidDevice =~ /VXT/ ) {
			doLogit("Override VXT User $clid with User $exten ");
			my $savexten = $exten;
			$exten = $clid;
			$clid = SarkSubs::SQLiteGet($dbh, "SELECT stolen FROM ipphone where pkey = '$exten'");
			doLogit("origin clid is $clid ");	
			doLogoutUser;
			$exten = $savexten;
	}	
    my $leasehdtime = SarkSubs::SQLiteGet($dbh, "SELECT LEASEHDTIME FROM globals where pkey = 'global'" || 43200);
	my $expires = time + $leasehdtime;

    SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET macaddr=(select macaddr from ipphone where pkey='$clid') WHERE pkey='$exten'");
    SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET stolen='$clid',stealtime='$expires' WHERE pkey='$exten'");
    SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET basemacaddr = macaddr WHERE pkey='$clid'");
    SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET macaddr=NULL,stolen='$exten',stealtime='$expires' WHERE pkey='$clid'");

	doLogit("VXT User $exten is acquiring $clid");	
	doLogit("Sending reset to $notify and $clid");
	SarkSubs::request_syscmd ( "/usr/sbin/asterisk -rx \"sip notify $notify $clid\" ");
	doCommit;
}


sub doLogoutUser () {

    my $stolen = SarkSubs::SQLiteGet($dbh, "SELECT stolen FROM ipphone where pkey = '$exten'"); 

	if ($stolen) {
        SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET macaddr=NULL,stolen=NULL,stealtime=NULL WHERE pkey='$exten'");
        SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET macaddr = basemacaddr WHERE pkey='$stolen'");
        SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET stolen=NULL,stealtime=NULL WHERE pkey='$stolen'");
		doLogit("VXT User $exten is releasing $stolen with $notify $exten");		
		SarkSubs::request_syscmd ("/usr/sbin/asterisk -rx \"sip notify $notify $exten\" ");
		doCommit;
	}
	else {
        doLogit("VXT User $exten wants to logoff but is not logged in.");	
	}

}

sub doReset () {

    SarkSubs::SQLiteDo($dbh, "UPDATE ipphone SET macaddr=NULL,stolen=NULL,stealtime=NULL WHERE pkey='$exten'");
	doCommit;
    doLogit("Reset $exten ");	

}


sub doPrune () {

    my $dbh = SarkSubs::SQLiteConnect();
    my @IPphones = sort (SarkSubs::SQLiteGetKeys($dbh, "SELECT pkey FROM IPphone" ));
    foreach (@IPphones) {
    	if (/VXT/) {
        	if (SarkSubs::SQLiteGet($dbh, "SELECT stealtime FROM ipphone where pkey = '$_'") ) {
                	if  (SarkSubs::SQLiteGet($dbh, "SELECT stealtime FROM ipphone where pkey = '$_'") <  time) {
						$exten = $_;
                        $clid =  SarkSubs::SQLiteGet($dbh, "SELECT stolen FROM ipphone where pkey = '$_'");
                        doLogit("Pruning VXT User $exten on timeout ");
                        $maclong = SarkSubs::SQLiteGet($dbh, "SELECT macaddr FROM ipphone where pkey = '$_'");
                        $maclong =~ /^(\w{2})(\w{2})(\w{2})/ ;
						$mfgmac = $1.":".$2.":".$3;
						$notify  = SarkSubs::SQLiteGet($dbh, "SELECT notify FROM mfgmac where pkey = '$mfgmac'");
                        doLogoutUser;
                    }
            }
        }
    }
    SarkSubs::SQLiteDisconnect($dbh);
}

sub doCommit ()
{
#  disconnect to unlock the database	
	SarkSubs::SQLiteDisconnect($dbh);
# don't do a full commit - just do the provisioning files.
    SarkSubs::request_syscmd ("/usr/bin/php /opt/sark/generator/tftpSQL.php");
	$dbh = SarkSubs::SQLiteConnect();
	return;

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
		 
