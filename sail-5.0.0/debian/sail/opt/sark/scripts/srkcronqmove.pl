#! /usr/bin/perl -w
#
#
#
#	Copyright Aelintra Telecom Limited(2008-16), all rights reserved
#
# load required modules

use lib '/opt/sark/perl/modules';
use strict;
#use POSIX qw(setsid);
use File::ReadBackwards;
use File::Copy;
use sark::SarkSubs;
use Sys::Syslog;

sub doLogit($)
{
	my $logmsg = shift;
	syslog('info', $logmsg);
}

#unless ( -e "/opt/sark/.recone" ) {
#	exit 0;
#;}

doLogit("queuemover started ...");

my $dbh = SarkSubs::SQLiteConnect();
my $monitorstage =  SarkSubs::SQLiteGet($dbh, "SELECT MONITORSTAGE FROM globals where pkey = 'global'") || "/home/sark/monstage";
my $monitorout = SarkSubs::SQLiteGet($dbh, "SELECT MONITOROUT FROM globals where pkey = 'global'") || "/home/sark/monout";
my $recqsearchlim = SarkSubs::SQLiteGet($dbh, "SELECT RECQSEARCHLIM FROM globals where pkey = 'global'") || 400;
my $recqdither = SarkSubs::SQLiteGet($dbh, "SELECT RECQDITHER FROM globals where pkey = 'global'") || 1;
SarkSubs::SQLiteDisconnect($dbh);

$monitorstage =~ s/\/$//;
$monitorout =~ s/\/$//;

unless ( -d $monitorstage ) {
        die "Srkqmove104 - Invalid Stage Directory entry  \n";
}
unless ( -d $monitorout ) {
        die "Srkqmove105 - Invalid Out Directory entry  \n";
}

my @files = ();
my @qlogtail;
my $line;
my $linecnt = 1;
my $found;
my $filename;

print STDERR "Queuemover started \n";

        my $Qlog = File::ReadBackwards->new('/var/log/asterisk/queue_log')
    		or die "Srkqmove102 - Couldn't open queue_log - ending \n";
        $linecnt = 1;
        while ( defined($line = $Qlog->readline) ) {
        	push @qlogtail, $line
			if $line =~ /CONNECT/;
		if ( $linecnt >= $recqsearchlim ) {
			last;
		}
		$linecnt++;
        }
        unless ( -e $monitorstage ) {
		exit
	}

        opendir STAGE, $monitorstage
		or die "Srkqmove103 - Couldn't open stageing directory - ending $_ \n";
	while ($filename = readdir STAGE) {
		next if $filename =~ /^\./;
		push @files, $filename;
	}
	closedir STAGE;

        if (@files) {
	   foreach (@files) {
        	if (/Qexec(\d+)-(\w+)-(\w+)-(\w+).wav$/) {
                	my $filename = $_;
                	my $filetimestamp = $1;
                	my $cluster = $2;
                	my $dnid = $3;
                	my $clid = $4;
			$found = 0;
			print STDERR "Candidate found $_ \n";
                        foreach (@qlogtail) {
                        	my @fields = split /\|/, $_;
				print STDERR "qlogrec being examined is $_ \n";
                                my $logtimestamp = $fields[0];
                                my $queue =  $fields[2];
                                my $extension =  $fields[3];
				$extension =~ s/\///;
				print STDERR "Found $logtimestamp, $queue, $extension \n";
                                if ( ($filetimestamp + $recqdither) >= $logtimestamp &&
					($filetimestamp - $recqdither) <= $logtimestamp ) {
					print STDERR "Matched $_ \n";
                                	my $newfilename = $monitorout."/".$filetimestamp."-".$queue."-".$extension."-".$clid.".wav";
                                	print STDERR "Moving Queue file $filename to $newfilename\n";
                                        move ($monitorstage."/".$filename, $newfilename);
                                        $found = 1;
                                        last;
				}
                        }
                        unless ($found) {
                        # Bugger! we didn't find it!
                        	print STDERR "Couldn't find a match for Queuefile $filename using $filetimestamp and $recqdither- will strip Qexec and  move anyway \n";
                        	move ($monitorstage."/".$filename, $monitorout."/".$filetimestamp."-".$dnid."-".$clid.".wav");
                        }
                }
                else {  # It's a regular file
                        print STDERR "Moving regular file $_ \n";
                        move ($monitorstage."/".$_, $monitorout."/".$_);
                }
           }
	}
doLogit("queuemover ended ...");
