#!/usr/bin/perl

use strict;

my $arch = (`/bin/uname -m`);
chomp $arch;

unless ( $arch =~ /^arm/ || $arch =~ /^ppc/) { 
	exit;
}

#print STDERR "RUNNING for $arch \n";

my $dir = "/var/spool/asterisk/monout/";
my $dirsize;
my $ret;
my $candidate;
my $threshold = `sqlite3 /opt/sark/db/sark.db "SELECT RECLIMIT FROM globals"`;

#print  "LIMIT IS $threshold\n";

$_ = `du -ms $dir`;
/^(\d{1,})/;
$dirsize = $1;
#print  "directory size is $dirsize \n";

while ($dirsize > $threshold - 1) {

$candidate = `ls -t $dir | tail -1`;
chomp($candidate);
print  "threshold reached $candidate will be deleted\n";

$ret = `rm -rf $dir$candidate`;

print  "deleted $dir$candidate \n";

$_ = `du -ms $dir`;
/^(\d{1,})/;
$dirsize = $1;
#print "directory size is $dirsize \n";

}

exit;
