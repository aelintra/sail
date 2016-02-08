#!/usr/bin/perl
#srkhelper.pl

use IO::Socket::INET;
use POSIX qw(setsid);

my $app_name = 'sark-ua-helper';
$0 = $app_name;
my $t;
# flush after every write
$| = 1;

# open logs

open STDIN, '/dev/null'   or die "$t Can't read /dev/null: $!";
open STDOUT, '>>/var/log/srkhelper.log' or die "$t/tSHADOW102 ==> Can't write to log: $!\n";

if ($ARGV[0] && $ARGV[0] eq '--daemonize') {
	daemonize();
}
elsif ($ARGV[0] && $ARGV[0] eq '--nofork') {
#	print STDERR "Not forking.\n";
}
else {
	print STDERR "Use --daemonize or --nofork\n";
	exit 1;
}


my ($socket,$client_socket);
my ($peeraddress,$peerport);
my $return;
my $data;

# creating object interface of IO::Socket::INET modules which internally does
# socket creation, binding and listening at the specified port address.
$socket = new IO::Socket::INET (
LocalHost => '127.0.0.1',
LocalPort => '7601',
Proto => 'tcp',
Listen => 5,
Reuse => 1
) or die "ERROR in Socket Creation : $!\n";

print "$t SRKHELPER started and Waiting for client connection on port 7601 \n";

while(1)
{
# waiting for new client connection.
$client_socket = $socket->accept();

# get the host and port number of newly connected client.
$peer_address = $client_socket->peerhost();
$peer_port = $client_socket->peerport();
$t = localtime();
print "$t Accepted New Client Connection\n";

# write operation on the newly accepted client.
$data = "Ready";
print $client_socket "$data\n";

# read operation on the newly accepted client
$data = <$client_socket>;
print "$t Received cmd : $data";
print "$t running command \n";
$return = `$data`;
$t = localtime();
print "$t Returned $return\n";
print $client_socket "$return\n<<EOT>>\n";
print "$t waiting for work...\n";
}

$socket->close();
exit (0);

#
# Daemon no longer used - running under runit
#
sub daemonize {
    $t = localtime();
    chdir '/'                 or die "$t Can't chdir to /: $!";

    defined(my $pid = fork)   or die "$t ==>Can't fork: $!";
    exit if $pid;
    setsid                    or die "$t ==>can't start a new session: $!\n";
    umask 0;
}
