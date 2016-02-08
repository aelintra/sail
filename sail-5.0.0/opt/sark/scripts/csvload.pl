#!/usr/bin/perl 
use Text::CSV;
#use Data::Dumper;
use lib '/opt/sark/perl/modules';
use DBI;
use sark::SarkSubs;

#
# csv file layout
#
#	$macaddr 		
#	$vendordevice 	
#	$pkey 			
#	$name  			
#	$ddi  			
#	$location (local/remote)	
#	$cluster  (default is 'default')
#

my $debug_level = 2;
$num_args = $#ARGV + 1;
if ($num_args != 2 && $num_args != 1) {
  print "\nUsage: csvload.pl csvfilename (dbfilename)\n";
  exit;
}
my $Dbname;
my $dbh;
my $loadcsv=$ARGV[0];
if ($ARGV[1]) {
	$Dbname = $ARGV[1];
}
	
my @phones;
my $csv = Text::CSV->new();

open (FILE, $loadcsv) or die "Couldn't open csv file: $!";
while (<FILE>) {
    $csv->parse($_);
    push(@phones, [$csv->fields]);
}
close FILE;
#print Dumper(@phones);
if ($Dbname) {	
	$dbh = DBI->connect( "dbi:SQLite:dbname=$Dbname","", "", { RaiseError => 1, AutoCommit => 0 });
}
else {
	print "No DB name given - using sark.db\n";
	$dbh = SarkSubs::SQLiteConnect();
}	
foreach (@phones) {
	my $macaddr 		= $_->[0];
	my $vendordevice 	= $_->[1];
	my $pkey 			= $_->[2];
	my $name  			= $_->[3];
	my $ddi  			= $_->[4];
	my $location 		= $_->[5];
	my $cluster 		= $_->[6];

# check vendor device type is in the database
	my $device =  SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM device where pkey = '$vendordevice'") ;
	unless ($device) {
		if ($debug_level >= 1) {
			print STDOUT "Unknown Vendor device type.. $vendordevice - can't create extension\n";
		}		
		next;
	}
	
#default location to "local" if none given
unless ($location) {
	$location = "local";
}

#default cluster to "default" if none given
unless ($cluster) {
	$cluster = "default";
}

#check pkey does not already exist
if ($pkey) {
	if (SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM ipphone where pkey = '$pkey'")) {
		if ($debug_level >= 1) {
			print STDOUT "Extension already exists.. $pkey - can't create extension\n";
		}		
		next;
	}
}
		
# get an extension key if none given
unless ($pkey) {
		print STDOUT "Extension number not present - can't create extension\n";
		next;
}
if ($pkey eq 'auto') {
    $pkey = SarkSubs::SQLiteGet($dbh, "SELECT SIPIAXSTART FROM globals where pkey = 'global'");
    while (SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM IPphone where pkey = '$pkey'"))  {
		$pkey++;
    }
    print STDOUT "Extension number $pkey allocated to auto entry \n";
}


    
# get the template    
    my $sipiaxfriend = SarkSubs::SQLiteGet($dbh, "SELECT sipiaxfriend FROM Device where pkey = '$device'");
    my $provision 	 = SarkSubs::SQLiteGet($dbh, "SELECT provision FROM Device where pkey = '$device'");   
    my $passwd	= SarkSubs::sark_password; 
	$name =~ s/[\s'*!£\$|\^]*//g;

    my	$desc = $name || "Ext".$pkey;
	
	my $callerid = $pkey;
	my $ipaddr   	= SarkSubs::ret_localip();
    my $ipbase = $ipaddr;
    $ipbase =~ s/\.\d{1,3}$/\.0/; 
    	
# tailor the template

    if ($sipiaxfriend =~ /callerid=/) {
     		$sipiaxfriend =~ s/callerid=/callerid=\"$desc\" <$pkey>/;
    }
    else {
		$sipiaxfriend .= "\ncallerid=\"$desc\" <$pkey>";
    }
    if ($sipiaxfriend =~ /username=/) {
    		$sipiaxfriend =~ s/username=/username=$desc/;
    }
    else {
		$sipiaxfriend .= "\nusername=$desc";
    }
    if ($sipiaxfriend =~ /secret=/) {
    		$sipiaxfriend =~ s/secret=/secret=$passwd/;
    }
    else {
		$sipiaxfriend .= "\nsecret=$passwd";
    }
    if ($sipiaxfriend =~ /mailbox=/) {
		$sipiaxfriend =~ s/mailbox=/mailbox=$pkey/;
    }
    else {
		$sipiaxfriend .= "\nmailbox=$exten";
    }
    if ($sipiaxfriend =~ /pickupgroup=/) {
		$sipiaxfriend =~ s/pickupgroup=/pickupgroup=1 \ncallgroup=1/;
    }
    else {
		$sipiaxfriend .= "\npickupgroup=1\ncallgroup=1";
    }
    unless ($sipiaxfriend =~ /call-limit/) {
                $sipiaxfriend .= "\ncall-limit=3";
    }
    unless ($sipiaxfriend =~ /subscribecontext/) {
                $sipiaxfriend .= "\nsubscribecontext=extensions";
    }
    
# set ACL
	my $acl 		 = SarkSubs::SQLiteGet($dbh, "SELECT ACL FROM globals where pkey = 'global'") || 'NO'; 
	if ($acl eq "YES") {
		unless ($sipiaxfriend =~ /deny=/) {
			$sipiaxfriend .= "\ndeny=0.0.0.0/0.0.0.0";
		}
		unless ($sipiaxfriend =~ /permit=/) {
			$sipiaxfriend .= "\npermit=$ipbase/255.255.255.0";
		}
	}
	else {
		$sipiaxfriend =~ s/^.*deny=.*$//;
		$sipiaxfriend =~ s/^.*permit=.*$//;
    }
     
# set g711 as allowed codec
    $sipiaxfriend .= "\ndisallow=all ";
# allow G722 for Ast 1.8 systems
	if ( -e "/opt/sark/.astrelease") {
		$sipiaxfriend .= "\nallow=G722 ";
	}    
    $sipiaxfriend .= "\nallow=alaw\nallow=ulaw ";

    $sipiaxfriend =~ s/^\s+//;
    $sipiaxfriend =~ s/\s+$//;
    $sipiaxfriend =~ s/\r//g;
    
# substitute into provision    
    $provision =~ s/\$desc/$desc/g;
	$provision =~ s/\$ext/$pkey/g;
	$provision =~ s/\$password/$passwd/g;

    $provision =~ s/^\s+//;
    $provision =~ s/\s+$//;
    $provision =~ s/\r//g;
    $provision =~ s/¬/|/g;
    
# insert the extension   

	SarkSubs::SQLiteDo($dbh, "INSERT INTO ipphone (pkey,callerid,cluster,desc,device,devicerec,location,macaddr,passwd,provision,sipiaxfriend,technology) 	
		VALUES ('$pkey','$pkey','$cluster','$desc','$device','default','$location','$macaddr','$passwd','$provision','$sipiaxfriend','SIP')" );
	if ($debug_level >= 2) {
		print STDOUT "Inserted new extension $pkey with MAC $macaddr\n";
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
#
#			do DDI if present
#
	if ($ddi) {
		if (SarkSubs::SQLiteGet($dbh, "SELECT pkey FROM lineio where pkey = '$ddi'")) {
			if ($debug_level >= 1) {
				print STDOUT "DDI $ddi already exists.. can't create DDI\n";
			}		
		}
		else {
			my $range = 10000;
			my $peername = "peer".int(rand($range));
			my $username = "user".int(rand($range));
#			SarkSubs::SQLiteDo($dbh, "INSERT INTO lineio VALUES ('$ddi','YES',NULL,NULL,NULL,'PTT_DiD_Group',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'$pkey',NULL,NULL,'default',NULL,'$username',NULL,NULL,NULL,NULL,NULL,'NO',NULL,NULL,NULL,'NO',NULL,NULL,NULL,'NO','NO',NULL,'$pkey',NULL,NULL,NULL,'$peername',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'1','1',NULL,NULL,NULL,NULL,'NO',NULL,'DiD',NULL,NULL,NULL,NULL,NULL,'BTDDI',NULL,NULL,NULL,NULL,NULL,NULL,NULL,'NO' ) ");
			SarkSubs::SQLiteDo($dbh, "INSERT INTO lineio 
							   (pkey,active,carrier,closeroute,cluster,desc,faxdetect,lcl,moh,monitor,openroute,peername,routeclassopen,routeclassclosed,swoclip,technology,trunkname,callprogress)
						VALUES ('$ddi','YES','PTT_DiD_Group','$pkey','default','$username','NO','NO','NO','NO','$pkey','$peername','1','1','NO','DiD','BTDDI','NO' ) ");
		}
    }
}
    SarkSubs::SQLiteDisconnect($dbh);
# do a regen 
#	SarkSubs::sysCommit($q);
# and out...

