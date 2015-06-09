#!/usr/bin/perl
package SarkSubs;
use DBI;
use CGI ':all';
#use Net::Domain qw(hostname hostfqdn hostdomain);
use IO::Socket::INET;

my $maxundonum = '5';


sub check_pid {
    if ( -e "/persistent" ) {
    	unless  (`/bin/ps -e | /bin/grep '/bin/asterisk' | /bin/grep -v grep`) {
    		return ();
    	}
    }
    else {
    	unless  (`/bin/ps -e | /bin/grep asterisk | /bin/grep -v grep`) {
    		return();
    	}
    }
#   return (-1);
}

sub check_hapid {
    unless  (`/bin/ps -e | /bin/grep heartbeat | /bin/grep -v grep`) {
    	return();
    }
   return (-1);
}

sub check_hapid_installed {
    unless  ( -e "/usr/lib/heartbeat/heartbeat" ) {
    	return();
    }
   return (-1);
}

sub sark_password ($) {

	my $len = shift || 8;
	$len--; 
    my @alphanumeric = ('a'..'z', 'A'..'Z', 0..9);
    my $randpassword = join '', map $alphanumeric[rand @alphanumeric], 0..$len;
    return ($randpassword);
}

sub sark_stop () {
    my $ret;
    if ( -e "/persistent" ) {
    	$ret = (request_syscmd ('/bin/asterisk -rx "stop now"'));
    	$ret = 'Asterisk STOP issued';
    }
    else {  
    	`/bin/touch /opt/sark/passwd/.stop`;
	$ret = "stop signal sent - PBX will stop in about 1 minute";
    }
    return($ret);
}

sub sark_start () {
    my $ret;
    if ( -e "/persistent" ) {
       $ret = (request_syscmd ("/persistent/autorun/S98asterisk"));
       $ret = "Asterisk START issued";
    }
    else { 
	`/bin/touch /opt/sark/passwd/.start`;
	$ret = "start signal sent - PBX will start in about 1 minute";           
    }
    return($ret);
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

#	$socket->autoflush(1);
#	print "TCP Connection Success.\n";

# read the socket data sent by server.
	my $ack = <$socket>;
        my $ret;
# we can also read from socket through recv()  in IO::Socket::INET
# $socket->recv($data,1024);
#	print "Received from Server : $ack\n";
#	print "sending $data\n";
#
# write on the socket to server.
#$data = "/etc/init.d/sark stop";
	print $socket "$data\n";
# we can also send the data through IO::Socket::INET module,
# $socket->send($data);

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

sub sark_standby () {

    `/usr/lib/heartbeat/hb_standby`;
    return ();
}


sub get_rec_list () {
    my @Reclist;
    if ( !-e "/opt/sark/recone") {
    	@Reclist = qw /default None OTR/;
    	return (@Reclist);
    }
    @Reclist = qw /default None OTR OTRR/;
    return (@Reclist);
}


sub routeClass($) {
	my ($var) = shift;
#
# This little sub returns a "routeclass" for the open/closed/outcome value you input
# The routeclass makes life a lot easier for the AGI when it has
# to route a call through the open/closed/outcome vectors
#
# 0  => value is "None" for an IVR menu selection
# 1  => value is a dialable internal number (extension or callgroup)
# 2  => value is an IVR name
# 3  => value is the default IVR
# 4  => value is a queue name
# 5  => value is DISA
# 6  => value is CALLBACK
# 7  => Not Used
# 8  => value is a sibling
# 9  => value is a trunk name
# 10 => value is a custom_app name
# 11 => value is a trunk group
# 20 => value is Retrieve Voicemail
# 21 => value is Leave Voicemail
#100 => value is Operator
#101 => value is Hangup
#

        if ($var eq "None") {
        	return (0);
        }
        if ($var =~ /^\*\d{3,4}$/) {
        	return (1);
        }
        if ($var =~ /^\d{3,4}$/) {
        	return (1);
        }

        if ($var eq "Default IVR") {
        	return (3);
        }
        if ($var eq "DISA") {
        	return (5);
        }
        if ($var eq "CALLBACK") {
        	return (6);
        }
        if ($var eq "Retrieve Voicemail") {
        	return (20);
        }
        if ($var eq "Leave Voicemail") {
        	return (21);
        }
        if ($var eq "Operator") {
        	return (100);
        }
        if ($var eq "Hangup") {
        	return (101);
        }
        my $ret = 0;
        my $dbh = SQLiteConnect();
        if (SQLiteGet($dbh, "SELECT pkey FROM lineIO where pkey = '$var'")) {
        	my $carrier = SQLiteGet($dbh, "SELECT carrier FROM lineIO where pkey = '$var'");
                my $type = SQLiteGet($dbh, "SELECT carriertype FROM Carrier where pkey = '$carrier'");
                if ($type eq 'group') {
                        $ret = 11;
                }
                else {
        		if ($var =~ /~/) {
                		$ret = 8;
                	}
                	else {
                      		$ret = 9;
                	}
                }
        }
        elsif (SQLiteGet($dbh, "SELECT pkey FROM speed where pkey = '$var'")) {                    
                $ret = 1;                                                                          
        } 
        elsif (SQLiteGet($dbh, "SELECT pkey FROM Queue where pkey = '$var'")) {
        	$ret = 4;
        }
        elsif (SQLiteGet($dbh, "SELECT pkey FROM ivrmenu where pkey = '$var'")) {
        	$ret = 2;
        }
        elsif (SQLiteGet($dbh, "SELECT pkey FROM Appl where pkey = '$var'")) {
        	$ret = 10;
        }
        SQLiteDisconnect($dbh);
        return ($ret);
}

sub ret_localip () {
    $_ = `/sbin/ifconfig eth0`;
    /inet addr:*?([\d.]+)/;
    return ($1);
}
sub ret_subnet () {
    	my $ipaddr = ret_localip();
    	my $nmask = ret_subnetmask();
    	my @addrarr=split(/\./,$ipaddr);
 	my ( $ipaddress ) = unpack( "N", pack( "C4",@addrarr ) );
	my @maskarr=split(/\./,$nmask);
	my ( $netmask ) = unpack( "N", pack( "C4",@maskarr ) );

# Calculate network address by logical AND operation of addr & netmask
# and convert network address to IP address format
	my $netadd = ( $ipaddress & $netmask );
	my @netarr=unpack( "C4", pack( "N",$netadd ) );
	my $netaddress=join(".",@netarr);

	return ($netaddress);
}



sub ret_subnetmask () {
    $_ = `/sbin/ifconfig eth0`;
    /Mask:*?([\d\.]+)/;
    return ($1);
}

sub ret_cidr () {
    my $numbits = 0;
    my $nmask = ret_subnetmask ();
    $nmask =~ /^(\d{1,3})\.(\d{1,3})\.(\d{1,3})\.(\d{1,3})/;
    push (my @nums, $1, $2, $3, $4 );
    foreach ( @nums ) {
	if ( /255/ ) {
		$numbits+=8;
	}
	elsif ( /254/ ) {
		$numbits+=7;
	}
	elsif ( /252/ ) {
		$numbits+=6;
	}
	elsif ( /248/ ) {
		$numbits+=5;
	}
	elsif ( /240/ ) {
		$numbits+=4;
	}
	elsif ( /224/ ) {
		$numbits+=3;
	}
	elsif ( /192/ ) {
		$numbits+=2;
	}
	elsif ( /128/ ) {
		$numbits+=1;
	}
    }
    return ($numbits);
}

#sub ret_buildpath () {
#
#    return ("/etc/asterisk/");
#}

sub sysCommit($) {
#    print STDERR "IN sysCommit\n";
    my ($q) = shift;

#   simple serialization
#    my $expire = 0;
#   while ( -e  '/opt/sark/db/sark.lock' ) {
#        print STDERR "Couldn't get Commit lock - Retrying in 1 second\n";
#    	sleep 1;
#        $expire++;
#        if ($expire > 10) {
#        	die "Commit processor locked";
#        }
#    }
#    `/bin/touch /opt/sark/db/sark.lock`;

# ask the helper to run a regen/reload for us.

    $rc = request_syscmd ("/bin/sh /opt/sark/scripts/srkgenAst >/dev/null 2>&1");
    $rc = request_syscmd ("/bin/sh /opt/sark/scripts/srkreload >/dev/null 2>&1");
  
 
    my $dbh =SQLiteConnect();
# turn off commit (done in genAst as of r1986)
#    SQLiteDo($dbh, "UPDATE globals SET MYCOMMIT='NO' WHERE pkey='global'");
# clear undologs
#    SQLiteDo($dbh,"DELETE FROM undolog");
# generate FOP layouts if requested
	if (SQLiteGetGlobal($dbh,"RUNFOP") eq 'enabled') {
		$rc = request_syscmd ("perl /opt/sark/scripts/op_buttons.pl >/dev/null 2>&1");
		$rc = request_syscmd ("perl /opt/sark/scripts/name_op_buttons.pl >/dev/null 2>&1");	
	}
# we're out
    SQLiteCommit($dbh);
    SQLiteDisconnect($dbh);
    my $cmd = "/bin/sh /opt/sark/scripts/snap.sh";
    request_syscmd ( $cmd );
    my @Snapshots = `/bin/ls -trd /opt/sark/snap/*`;
    my $ss = scalar @Snapshots;
    if ($ss > 5 ) {
    	my $deletions = $ss - 5;
    	while ($deletions) {
    		my $deletefile = shift(@Snapshots);
        	$cmd = "/bin/rm $deletefile";
        	request_syscmd ( $cmd );
    		$deletions--;
        }
    }
    
#    $rc = request_syscmd ("/bin/cp /opt/sark/db/sark.db /opt/sark/db/sark.copy.db");
#    $rc = request_syscmd ("/bin/mv /opt/sark/db/sark.copy.db /opt/sark/db/sark.rdonly.db");	 
     
    unlink '/opt/sark/db/sark.lock';
}

sub validateTuple ($$$$$$) {
#
#   Iterate over the screen variables and validate any variable which has
#   a match in the DB record hash.  Validate by processing against a hash
#   of REGEX flagging any errors into the error hashes.
#
    my ($hashScrnVarRef,
    	$hashRecordRef,
   	$hashVarRegexRef,
        $hashOutBufRef,
        $hashColorDefaultRef,
        $VerrorRef
        ) = @_;
    while ( my ($key, $value) = each %{$hashScrnVarRef} ) {
    	if (exists $hashRecordRef->{$key} ) {
        	unless ($value eq $hashRecordRef->{$key} ) {
                	if ( exists $hashVarRegexRef->{$key} ) {
                                $value =~ s/^\s+//;
                		$value =~ s/\s$//;
                		$value =~  $hashVarRegexRef->{$key} ;
                                if ( $1 eq $value ) {
#	                              	print STDERR "DOLLAR 1 is $1\n";
									$hashOutBufRef->{$key} = $1;
                                }
                                else {
                                	push ( @{$VerrorRef}, "$key Failed validation with $value");
                                        $hashColorDefaultRef->{$key} = 'red';
                                        $hashVarErrorRef->{$key} = 1;
                                        $gonogo = 1;
#                                        print STDERR "DOLLAR 1 IS $1 \n";
#                                        print STDERR "SET GONOGO \n";
                                }
                        }
                        else {
                        	$value =~ $hashVarRegexRef->{"AADEFAULT"} ;
                                if ( $1 eq $value ) {
#                              	print STDERR "DOLLAR 1 is $1\n";
        			 	$hashOutBufRef->{$key} = $1;
                                }
                                else {
                                	push (@{$VerrorRef}, "$key Failed Standard validation with $value");
                                        $hashColorDefaultRef->{$key} = 'red';
                                        $hashVarErrorRef->{$key} = 2;
                                        $gonogo = 1;
#                                        print STDERR "SET GONOGO \n";
                                }
                        }
                }
        }
    }
    unless ( $hashScrnVarRef->{pkey} ) {
    	push (@{$VerrorRef}, "No KEY Entered");
        $hashColorDefaultRef->{"pkey"} = 'red';
    	$gonogo = 1;
    }
    return ($gonogo);
}

sub newTuple ($$$) {
#        print STDERR "BEGININSERT\n";
	my ($hashVarOutBufRef, $table, $dbh) = @_;
	my $inserts = "(";
	my $newvalue = "(";

   	while ( my ($k, $v) =  each %{$hashVarOutBufRef} ) {
    		if ($v) {
    			$inserts .= "$k,";
			$newvalue .= "'$v',";
        	}
        }
#        unless (SQLiteGetGlobal($dbh,"UNDOONOFF") eq 'ON') {
#		SQLiteDo($dbh, "INSERT INTO undolog VALUES(NULL,'BEGINTRANS')");
#    		SQLiteSetUndoTrigger($dbh,$table);
#        }
    	my $sqlstmt = "INSERT INTO $table ".$inserts;
    	$sqlstmt =~ s/,$/) VALUES $newvalue/;
    	$sqlstmt =~ s/,$/)\;/;
#        print STDERR "SQL STATEMENT IS \n $sqlstmt \n";
    	SQLiteDo($dbh, $sqlstmt);
    	SQLiteDo($dbh, "UPDATE globals SET MYCOMMIT='YES' WHERE pkey='global'");
    	SQLiteCommit($dbh);
	return 0;
}

sub updateTuple ($$$$) {
	my ($hashVarUpdBufRef, $hashRecordRef, $table, $dbh) = @_;
#    	print STDERR "BEGINUPDATE\n";
#    	my @Cols = keys %{$hashRecordRef};
    	my $update ;
    	while ( my ($key, $value) =  each %{$hashVarUpdBufRef} ) {
#        	print STDERR "\nEXAMINING KEY $key, VALUE IS $value \n \n";
                if ( exists $hashRecordRef->{$key} ) {
        		unless ( $value eq $hashRecordRef->{$key} ) {
#                              	print STDERR "PASSED COL $key = $hashRecordRef->{$key} \n";
#                              	print STDERR "PASSED COL $key = $hashVarUpdBufRef->{$key} \n";
    				$update .= "$key='".$value."',";
                        }
                }
    	}
#       print STDERR "UPDATE $update \n";
# Only update if the user modified any data
        if ( $update ) {
#        	print STDERR "In UPDATE \n";
#                unless (SQLiteGetGlobal($dbh,"UNDOONOFF") eq 'ON') {
#			SQLiteDo($dbh, "INSERT INTO undolog VALUES(NULL,'BEGINTRANS')");
#                        SQLiteSetUndoTrigger($dbh,$table);
#                }
                my $sqlstmt = "UPDATE $table SET ".$update;
    		$sqlstmt =~ s/,$/ WHERE pkey='$hashRecordRef->{pkey}'/;
#    		print STDERR "$table TABLE UPDATE => $sqlstmt \n ";
    		SQLiteDo($dbh, $sqlstmt);
                SQLiteDo($dbh, "UPDATE globals SET MYCOMMIT='YES' WHERE pkey='global'");
                SQLiteCommit($dbh);
	}
	return 0;
}


#
#  SQLite abstraction
#

sub SQLiteConnect() {
    # make connection to database
#    print STDERR "IN CONNECT\n\n";
    $dbh = DBI->connect( "dbi:SQLite:dbname=/opt/sark/db/sark.db","", "", { RaiseError => 1, AutoCommit => 1 });
    return $dbh;
}

sub SQLiteDisconnect($) {
    my ($dbh) = shift;
#    print STDERR "IN DISCONNECT\n\n";
    # disconnect from database
#    $dbh->commit();
    $dbh->disconnect;
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

sub SQLiteGet($$) {
#
#  return a column value for a given key
#
    my ($dbh, $getcmd) = @_;
#    print STDERR "GET DIRECT - $getcmd \n";
    my $out;
#    print STDERR "SQLiteGet is running $getcmd\n";
    my $sth = $dbh->prepare($getcmd);
    $sth->execute();
    $sth->bind_columns(\$out);
    $sth->fetch();
    $sth->finish();
    return $out;
}

sub SQLiteGetCols($$) {
#
#  return an array of all of the column names in a table
#
    my ($dbh, $table) = @_;
    my @cols;
    my $sth = $dbh->prepare("PRAGMA table_info($table)");
    $sth->execute();
    my $out =  $sth->fetch();
    while ($out) {
	push (@cols, @$out[1]);
        $out =  $sth->fetch();
    }
    $sth->finish();
    return @cols;
}

sub SQLiteGetHashCols($$) {
#
#  return a hash of all of the column names in a table
#
    my ($dbh, $table) = @_;
#    print STDERR "BUILDING COLUMN HASH FOR TABLE=>$table \n";
    my %cols;
    my $sth = $dbh->prepare("PRAGMA table_info($table)");
    $sth->execute();
    my $out =  $sth->fetch();
    while ($out) {
	$cols{@$out[1]} = undef;
        $out =  $sth->fetch();
    }
    $sth->finish();

    return %cols;
}

sub SQLiteGetHash($$) {
#
#  Return an entire row as hash/value
#
    my ($dbh, $getcmd) = @_;
#    print STDERR "GETHASH - $getcmd\n";
    my $sth = $dbh->prepare($getcmd);
    $sth->execute();
    my $ret = $sth->fetchrow_hashref;
    $sth->finish();
    return %$ret;
}

sub SQLiteGetHashKey($$$) {
#
#  Return an entire row as hash/value
#
    my ($dbh, $getcmd,$key) = @_;
#    print STDERR "GETHASH - $getcmd\n";
    my $sth = $dbh->prepare_cached($getcmd);
    $sth->execute($key);
    my $ret = $sth->fetchrow_hashref;
    $sth->finish();
    return %$ret;
}


sub SQLiteGetGlobal($$) {
#
#   Return a single global variable by name
#
    my ($dbh, $getcmd) = @_;
    return SQLiteGet($dbh, "SELECT $getcmd FROM globals where pkey = 'global'");
}


sub SQLiteUpdate($$) {
#
#  Update a single key/value - not used as far as I know
#
    my ($dbh, $sqlcmd) = @_;
    my $sth = $dbh->prepare($sqlcmd);
    $sth->execute();
    $sth->finish();
    undef $sth;
}

sub SQLiteDelete($$) {
#
#   Delete a row
#
    my ($dbh, $delcmd) = @_;
    my $sth = $dbh->prepare($delcmd);
    $sth->execute();
    $sth->finish();
    undef $sth;
#    print STDERR "SQL DELETE issued $delcmd\n";
}

sub SQLiteDo($$) {
#
#   do the SQL and return nothing
#
    my ($dbh, $sqlcmd) = @_;
#    print STDERR "SQLDO - $sqlcmd\n";
    my $sth = $dbh->prepare($sqlcmd);
    $sth->execute();
    $sth->finish();
    undef $sth;
}

sub SQLiteCommit($) {
#
#   Transaction COMMIT - Deprecated
#
#    print STDERR "SQLiteCommit\n";

#    my ($dbh) = shift;
#    unless (SQLiteGetGlobal($dbh,"UNDOONOFF") eq 'ON') {
#     	return 0;
#    }
#    $dbh->commit();
     return 0;
}

#
#  BEGIN/END TRANSACTION (FOR ROLLBACK)
#

