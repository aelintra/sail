#!/usr/bin/perl
use strict;
use CGI ':all';
use LWP::Simple qw(!head);

my $q = new CGI;
my $ip_adr = $q->param('ip_adr');
#print STDERR "IPADR $ip_adr\n" ;
#take a delay before issuing the re-register
sleep 3;
my $url = 'http://'.$ip_adr.'/dummy.htm?REREGISTER:1=Re-Register';
#print STDERR "URL $url\n" ;
my $content = get $url;
die "Couldn't issue url" unless defined $content;
print header('text/html','204 No response');
exit 0;
