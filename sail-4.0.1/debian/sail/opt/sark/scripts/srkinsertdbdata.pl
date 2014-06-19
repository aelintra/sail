#!/usr/bin/perl
use lib '/opt/sark/perl/modules';

package esmith;
package sark;
use sark::SarkSubs;
use strict;
use esmith::db;
my %selintra;

my %XLATE = (
	"COMMIT" => "MYCOMMIT",
        "group" => "grouptype",
        "FORMAT-2.1.11" => "FORMAT2111"
        );
my %XLATETABLE = (
	);
my %rewriteCarrier = (
	"SIP"  => "GeneralSIP",
        "IAX2" => "GeneralIAX2",
        );
my %SKIP = (
	"ADDHEADER" => "ignore",
        "basemacaddr" => "ignore",
        "channel" => "ignore",
        "CALLRECORD2" => "ignore",
        "Carrier" => "ignore",
		"CDR" => "ignore",
        "CFEXTRN" => "ignore",
        "COMPRESSION" => "ignore",
        "COUNTRY" => "ignore",
        "crecord" => "ignore",
        "DDITOEXTEN" => "ignore",
		"default" => "ignore",
        "Device" => "ignore",
        "DIGIUMCARD" => "ignore",
        "DISAPASSWORD" => "ignore",
		"Engin" => "ignore",
        "EOR" => "ignore",
        "FAILOVER" => "ignore",
        "globals" => "ignore",
        "GRPCLID" => "ignore",
        "GRPTRANSFORM" => "ignore",
        "iaxextn" => "ignore",
        "Header" => "ignore",
        "IMPEDANCE" => "ignore",
        "LCLVOIPMAX" => "ignore",
        "LOGBAK" => "ignore",
        "LOGROTATE" => "ignore",
        "MAILMODE" => "ignore",
        "mfgmac" => "ignore",
        "MTIME" => "ignore",
        "NOTIFY" => "ignore",
		"num" => "ignore",
        "Pennytel" => "ignore",
        "remotenum" => "ignore",
        "service" => "ignore",
        "SKIN" => "ignore",
        "sysdev" => "ignore",
        "loadmod" => "ignore",
        "ONETOUCHREC" => "ignore",
        "on-board" => "ignore",
        "openconf" => "ignore",
        "restart" => "ignore",
        "routeable" => "ignore",
        "RTPPORTS" => "ignore",
        "RUNCHANGE" => "ignore",
        "RUNMODE" => "ignore",
        "SPAMMODE" => "ignore",
        "speedrec" => "ignore",
        "SQUIDMODE" => "ignore",
        "SUBNET" => "ignore",
        "switchgroup" => "ignore",
        "TDMDRIVER" => "ignore",
        "TIMEOUTD" => "ignore",
        "TIMEOUTR" => "ignore",
        "TIMEZONE" => "ignore",
        "USBDISK" => "ignore",
        "utilities" => "ignore",
        "VMAILSERVER" => "ignore",
        "ZAPALARM" => "ignore",
        "zeor" => "ignore",
        "zzeor" => "ignore",
        "zzDummy" => "ignore",
        );
my @Like = qw /AUTO/;
my @IPphone;
my @COS;
my $type;
my $element;
my $insert;
if (@ARGV) {
    tie %selintra, 'esmith::config', $ARGV[0];
}
elsif (-e "/home/e-smith/db/selintra-work" ) {
    tie %selintra, 'esmith::config', '/home/e-smith/db/selintra-work';
}
else {
	die "NO OLD FORMAT SELINTRA DATABASE FOUND - ENDING NOW \n";
}

foreach (keys %selintra) {
        push (@IPphone, $_)
        if (db_get_type(\%selintra, $_) eq "IPphone");
        push (@COS, $_)
        if (db_get_type(\%selintra, $_) eq "COS");
}
my $dbh = SarkSubs::SQLiteConnect();

# ignore COS entries
foreach (@COS) {
		print STDERR "Adding COS subentry closed$_ to my ignore list\n";
        $SKIP{"closed$_"} = "ignore";
        print STDERR "Adding COS subentry open$_ to my ignore list\n";
        $SKIP{"open$_"} = "ignore";
}
foreach (sort @IPphone) {
	my $num   = $_;
        foreach (@COS) {
                if (db_get_prop(\%selintra, $num, "closed$_") eq "YES")  {
                	$insert = "INSERT INTO IPphoneCOSopen  (IPphone_pkey, COS_pkey) ";
                        $insert .= "VALUES  (\'$num\',\'$_\');";
                        SarkSubs::SQLiteDo($dbh, $insert);
				}
                if (db_get_prop(\%selintra, $num, "open$_") eq "YES")  {
                	$insert = "INSERT INTO IPphoneCOSclosed  (IPphone_pkey, COS_pkey) ";
                	$insert .= "VALUES  (\'$num\',\'$_\');";
                        SarkSubs::SQLiteDo($dbh, $insert);
				}
        }
}

foreach (keys %selintra) {

	my $key = $_;
        # remove trailing asterisks from DDIs
        my $fkey = $key;
	my %rec = db_get_prop(\%selintra, $_);
        $type = db_get_type(\%selintra, $_);
        # remove trailing asterisks
        if ( $type eq "lineIO" ) {
               	$fkey =~ s/\*$//;
                # call subs to get the routeclass
		$rec{routeclassopen} = SarkSubs::routeClass($rec{openroute});
                $rec{routeclassclosed} = SarkSubs::routeClass($rec{closeroute});
                unless (SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM Carrier where pkey = '$rec{carrier}'" )) {
			my %carrier = db_get_prop(\%selintra, $rec{carrier});
                	if ( $rewriteCarrier{$carrier{technology}} ) {
                        	print STDERR "translated carrier $rec{carrier} to $rewriteCarrier{$carrier{technology}} \n";
                        	$rec{carrier} = $rewriteCarrier{$carrier{technology}};
                	}
                }
        }
        # remove speed literal from callgroup key
        if ( $type eq "speed" ) {
               	$fkey =~ s/^speed//;
                # call subs to get the routeclass
		$rec{outcomerouteclass} = SarkSubs::routeClass($rec{outcome});
        }
        # routeclasses for IVRs
        if ( $type eq "ivrmenu" ) {
                my $op=0;
        	while ($op < 12) {
        		$rec{"routeclass$op"} = SarkSubs::routeClass($rec{"option$op"});
                	$op++;
        	}
        	$rec{timeoutrouteclass} = SarkSubs::routeClass($rec{timeout});
        }

        unless ($SKIP{$_} || $SKIP{$type}) {
                if (SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM $type where pkey = '$fkey'")) {
        		print STDERR "KEY $fkey ALREADY EXISTS - SKIPPED \n";
        		next;
        	}
        	my $create_type = $XLATETABLE{$type} || $type;
        	$create_type =~ s/-/_/g;
        	$insert = "INSERT INTO $create_type  (pkey ";
 		for $element (sort keys %rec )  {
               		unless ($SKIP{$element} || $element =~ /^AUTO/ || $element  eq '' ) {
                        	my $writename =  $XLATE{$element};
                		unless ($writename) {
					$writename = $element;
                                }
                		$writename =~ s/-/_/g;
        			$writename =~ s/'//;
                		$insert .= ",$writename ";
                        }
                }
        	$insert .= ") ";
        	$insert .= "VALUES  (\'$fkey\'";
        	for $element (sort keys %rec )  {
                	unless ($SKIP{$element} || $element =~ /^AUTO/ || $element eq '' ) {
                                my $data =  $rec{$element};
				$data =~ s/'//g;
                		$insert .= ",\'$data\' ";
                        }
            	}
        	$insert .= "); ";
                SarkSubs::SQLiteDo($dbh, $insert);
        }
}

SarkSubs::SQLiteCommit($dbh);
SarkSubs::SQLiteDisconnect($dbh);
