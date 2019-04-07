<?php
// +-----------------------------------------------------------------------+
// |  Copyright (c) CoCoSoft 2005-10                                  |
// +-----------------------------------------------------------------------+
// | This file is free software; you can redistribute it and/or modify     |
// | it under the terms of the GNU General Public License as published by  |
// | the Free Software Foundation; either version 2 of the License, or     |
// | (at your option) any later version.                                   |
// | This file is distributed in the hope that it will be useful           |
// | but WITHOUT ANY WARRANTY; without even the implied warranty of        |
// | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the          |
// | GNU General Public License for more details.                          |
// +-----------------------------------------------------------------------+
// | Author: CoCoSoft                                                      
// +-----------------------------------------------------------------------+
// 
//include("ip_helper_functions.php"); 
include("generated_file_banner.php");
include("/opt/sark/php/srkNetHelperClass");
include("/opt/sark/php/srkHelperClass");

$release = `sudo /usr/sbin/asterisk -rx 'core show version'`;
$vers = '1.8';
if (preg_match(' /Asterisk\s*(\d\d).*$/ ', $release,$matches)) {
	$vers = $matches[1];
}
echo 'Asterisk version is ' . $vers . "\n";

$OUT .= "[general] \n";
$OUT .= "static=yes \n";
$OUT .= "writeprotect=yes \n";
$OUT .= "[globals] \n";

$nethelper = new netHelper;	
$helper = new Helper;

$OUT.="\tLOCALIP=" . $nethelper->get_localIPV4() ."\n";

try {
    /*** connect to SQLite database ***/
    	$dbh = new PDO("sqlite:/opt/sark/db/sark.db");

    /*** set the error reporting attribute ***/
    	$dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//
//	output our main Globals	
//
	
	$sql = "select ABSTIMEOUT,ALLOWHASHXFER,BLINDBUSY,BOUNCEALERT,CALLRECORD1,EXTLEN" .
			   ",FAX,FAXDETECT,INTRINGDELAY,LANGUAGE,LTERM,OPERATOR,PLAYBEEP,PLAYBUSY,PLAYCONGESTED" .
			   ",RINGDELAY,SYSOP,SYSPASS,SPYPASS,VOIPMAX,VOICEINSTR,ASTDLIM,MAXIN,USEROTP" .
			   " from globals WHERE pkey='global'";   
	$global = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);

	if (!$global['LANGUAGE']) {
		$global['LANGUAGE'] = 'en-gb';
	}
 	foreach ($global as $k=>$v) {
 		if ($k == 'USEROTP') {
 			continue;
 		} 
		$OUT .= "\t" . $k . "=" . $v . "\n";
 	} 

//
//  globals required for Hot Desk
//
	if (file_exists("/usr/sbin/aelhdlon")) {
		$OUT .= "\tHDLOGIN=*14*\n";
		$OUT .= "\tHDLOGOUTL=*15*\n";
		$OUT .= "\tHDLOGOUTR=*16*\n";
		$OUT .= "\tHDLOGOUTRS=*17*\n";
		$OUT .= "\tHDSUPPASS=3243\n";
	}
//	
//  globals required for call recording     		/
//
	if (file_exists('/etc/debian_version')) {
       	$OUT .= "\tMONITOR_EXEC=/opt/sark/scripts/selmixd\n";
    }
    else {
		$OUT .= "\tMONITOR_EXEC=/opt/sark/scripts/selmix\n";
	}
	if (file_exists( "/opt/sark/recone" )) {
        	$OUT .= "\tSET_DYNAMIC_FEATURES=>YES\n";
	}
	else {
			$OUT .= "\tSET_DYNAMIC_FEATURES=>NO\n";
        	$OUT .= "\tDYNAMIC_FEATURES=>automon\n";
	}
	$OUT .= "\n"; 
//
//  include any customer supplied globals
//
	if ( file_exists( "/etc/asterisk/sark_customer_extensions_globals.conf" )) {
		$OUT .= ";\tCustomer supplied Globals (include file)\n";
		$OUT .= ";\n";
        	$OUT .= "#include sark_customer_extensions_globals.conf \n";
		$OUT .= ";\n";
	}
	$global = $dbh->query("SELECT * FROM globals where pkey='global'")->fetch(PDO::FETCH_ASSOC);
//
//  PIKA WARP & freePBX compatibility followed by the inside context "internal" and
//  its included contexts
//
        $OUT .= "[fxs]\t; PIKA WARP Compatibility \n";
        $OUT .= "\tinclude => internal \n";

	$OUT .= "[from-internal]\t; FPBX Compatibility \n";
	$OUT .= "\tinclude => internal \n";

	$OUT .= "[internal] \n";
	$OUT .= "\tinclude => parkedcalls\n";
	
	$OUT .= "\n\texten => MASTER,1,GoTo(extensions,\${EXTEN},1)\n";
	$sql = "select pkey from Cluster order by pkey";
 	foreach ($dbh->query($sql) as $row) {
		$OUT .= "\texten => " . $row['pkey'] . ",1,GoTo(extensions,\${EXTEN},1)\n";
	}
	$OUT .= "\texten => _vm.,1,GoTo(extensions,\${EXTEN},1)\n\n";

//
//  Class Of Service and Tenants if they are enabled
//
	if ($global['COSSTART'] == "ON") {
		$OUT .= "\texten => _[+*#0-9].,1,agi(sarkhpe,OutCos,\${EXTEN},,)\n";
	}
	elseif ($global['CLUSTER'] == "ON") {
		$OUT .= "\texten => _[+*#0-9].,1,agi(sarkhpe,OutCluster,\${EXTEN},,)\n";
	}
	else {
		$OUT .= "\tinclude => qrxvtmny \n";	
	}
	if (empty($global['OPERATOR'])) {
		$global['OPERATOR'] = "0";
	}
	$OUT .= "\n\texten => " . $global['OPERATOR'] . ",1,Goto(extensions,\${SYSOP},1)\n\n";


//
//	priv_sibling patch
//	
	$OUT .= "[priv_sibling] \n";
	$OUT .= "\tinclude => internal \n";
//
//  Clusters 
//
	$sql = "select * from Cluster order by pkey";
 	foreach ($dbh->query($sql) as $row) {
		if ($row['pkey'] == "default") {
			$OUT .= "[qrxvtmny]\n";    
		}
		else {
			$OUT .= "[" . $row['pkey'] . "]\n"; 
		}
		$OUT .= "\tinclude => parkedcalls\n";
		$OUT .= "\tinclude => internal-presets\n";
    	$OUT .= "\tinclude => extensions\n";
//    	$OUT .= "\tinclude => conferences\n"; 

		$sql = "SELECT * FROM Appl WHERE cluster='" . $row['pkey'] . "' ORDER BY pkey";
		foreach ($dbh->query($sql) as $applrow) {
			if ($applrow['span'] == "Both" || $applrow['span'] == "Internal") {
				if ($applrow['cluster'] == $row['pkey']) {	
					$OUT .= "\tinclude => " . $applrow['pkey'] . "\n"; 
				}
			}	
 		} 
 		if ($row['localarea'] && $row['localdplan']) {
			$OUT .= "\texten => " . $row['localdplan'] . ",1,GoTo(" . $row['localarea'] . "\${EXTEN},1)\n";
		}		 
		$sql = "SELECT * FROM Route WHERE cluster='" . $row['pkey'] . "' ORDER BY pkey";
		foreach ($dbh->query($sql) as $routerow) {
			if ($routerow['active'] == "YES") {
				$routerow['dialplan'] = preg_replace ( '/\s+/'," ", $routerow['dialplan'] );
				$routerow['dialplan'] = preg_replace ( '/\s*$/',"", $routerow['dialplan'] );
				$dialplan = explode(" ", $routerow['dialplan']);
				foreach ($dialplan as $plan) {
					$OUT .= "\texten => " . $plan . ",1,agi(sarkhpe,OutRoute," . $routerow['pkey'] . ",,)\n";		
				}
			}
		}
		$sql = "SELECT * FROM lineIO WHERE cluster='" . $row['pkey'] . "' ORDER BY pkey";
		foreach ($dbh->query($sql) as $linerow) {
			if ($linerow['active'] == "YES" && $linerow['match'] != "" ) {
				$OUT .= "\texten => _" . $linerow['match'] . "X.,1,agi(sarkhpe,OutTrunk," . $linerow['pkey'] . ",,)\n";    	
			}
		}
//
//   Tidy up clusters
//
//	$OUT .= "\n\texten => t,1,Hangup\n";
//    	$OUT .= "\texten => h,1,Hangup\n";
//    	$OUT .= "\texten => i,1,Playtones(congestion)\n";
    	if ($row['pkey'] == "default") {
        	$OUT .= "[qrxvtmny-callback]\n";
        	$OUT .= "\texten => _X.,1,DISA(no-password,qrxvtmny)\n";
    	}
    	else {
        	$OUT .= "\n[" . $row['pkey'] . "-callback]\n";
        	$OUT .= "\texten => _X.,1,DISA(no-password," . $row['pkey'] . ")\n";
    	}			
	}
//	
//  Extensions
//
	$OUT .= ";\n";
    	$OUT .= "[extensions] \n";
    	$OUT .= "\tinclude => internal-presets\n";
    	$OUT .= "\tinclude => parkedcalls\n\n";
        $OUT .= "\tinclude => conferences\n";
//
//  Call Parks
//	
	$park = `/bin/cat /etc/asterisk/sark_features_general.conf`;
	if (preg_match(' /parkpos\s*=>\s*(\d{1,3})-(\d{1,3})/ ',$park,$matches)) { 
		while ( $matches[1] <= $matches[2] ) {
			$OUT .= "\texten => " . $matches[1] . ",1,ParkedCall(" . $matches[1] . ")\n";
            		$OUT .= "\texten => " . $matches[1] . ",hint,park:" . $matches[1] . "@parkedcalls\n";
			$matches[1]++;
		}				
	}
//
//  open/closed hint
//	 

	$OUT .= "\n";
	$OUT .= "\texten => MASTER,hint,Custom:MASTER\n";
	$sql = "select pkey from Cluster order by pkey";
 	foreach ($dbh->query($sql) as $row) {
		$OUT .= "\texten => " . $row['pkey'] . ",hint,Custom:" . $row['pkey'] . "\n";
	}		
	$OUT .= "\n";

//
//  include any customer supplied hints
//
	if ( file_exists( "/etc/asterisk/sark_customer_hints.conf" )) {
		$OUT .= ";\n";
		$OUT .= ";\tCustomer supplied Hints (include file)\n";
		$OUT .= ";\n";
        $OUT .= "#include sark_customer_hints.conf \n";
		$OUT .= ";\n";
	}
//
//  pickup marks
//
	$OUT .= "\texten => _*8XX.,1,Pickup(\${EXTEN:2}@PICKUPMARK)\n";
    	$OUT .= "\texten => " . $global['OPERATOR'] . ",1,Goto(\${SYSOP},1)\n";
    	$OUT .= "\n";
//
//	Multicast page groups
//
	$sql = "SELECT * FROM mcast ORDER BY pkey";
	foreach ($dbh->query($sql) as $mcast) {
		$OUT .= "\texten => " . $mcast['pkey'] . ",1," . $mcast['mcasttype'] . "(MulticastRTP/";
		if (!isset ($mcast['mcastlport'])) {
			$OUT .= "basic/" . $mcast['mcastip'] . ":" . $mcast['mcastport'] . ")\n";
		}
		else {
			$OUT .= "linksys/" . $mcast['mcastip'] . ":" . $mcast['mcastport'] . 
				"/" . $mcast['mcastip'] . ":" . $mcast['mcastlport'] . ")\n";
		}
	}		
	$OUT .= "\n";
//
//  Phones
//
	$sql = "SELECT * FROM IPphone ORDER BY pkey";
	foreach ($dbh->query($sql) as $IPphone) {
		if ($IPphone['technology'] == "Analogue" ) {
			$OUT .= "\texten => " . $IPphone['pkey'] . ",hint,DAHDI/" . $IPphone['channel'] . "\n";
			$OUT .= "\texten => " . $IPphone['pkey'] . ",1,agi(sarkhpe,InCall,,,)\n";
		}
		elseif ($IPphone['technology'] == "IAX2" ) {
			$OUT .= "\texten => " . $IPphone['pkey'] . ",1,agi(sarkhpe,InCall,,,)\n";
		}
		elseif ($IPphone['technology'] == "Custom" ) {
			$OUT .= "\texten => " . $IPphone['pkey'] . ",hint," . $IPphone['dialstring'] . "\n";
			$OUT .= "\texten => " . $IPphone['pkey'] . ",1,agi(sarkhpe,InCall,,,)\n";
		}
		elseif ($IPphone['technology'] == "SIP" ) {
			$OUT .= "\texten => " . $IPphone['pkey'] . ",hint,SIP/" . $IPphone['pkey'] . "\n";
			$OUT .= "\texten => " . $IPphone['pkey'] . ",1,agi(sarkhpe,InCall,,,)\n";
		}
		if ($IPphone['dvrvmail'] != "None") {
            if ($IPphone['dvrvmail'] == "") {
              	$IPphone['dvrvmail'] = $IPphone['pkey'];
            }
          	$OUT .=  "\texten => *" . $IPphone['pkey'] . ",1,Voicemail(" . $IPphone['dvrvmail'] . ",su)\n";
          	$OUT .=  "\texten => vm" . $IPphone['pkey'] . ",hint,Custom:vm" . $IPphone['pkey'] . "\n";
          	$OUT .=  "\texten => vm" . $IPphone['pkey'] . ",1,VoicemailMain(" . $IPphone['pkey'] . ")\n";
		}
		if ($global['CAMPONQONOFF'] == "ON") {
          	$OUT .=  "\texten => **" . $IPphone['pkey'] . ",1,Set(save_caller=\${BLINDTRANSFER:4:4})\n";
	  		$OUT .=  "\texten => **" . $IPphone['pkey'] . ",n,Queue(Q" . $IPphone['pkey'] . "," . $global['CAMPONQOPT'] . ")\n";
	  		$OUT .=  "\texten => **" . $IPphone['pkey'] . ",n,Goto(extensions,\${save_caller},1) \n";  
		}
		
	}
//
//  Clean up
//
    	$OUT .= "\texten => o,1,Playback(pls-hold-while-try)\n";
    	$OUT .= "\texten => o,2,GoTo(0,1)\n";
    	$OUT .= "\texten => t,1,Hangup\n";
//    	$OUT .= "\texten => h,1,Hangup\n";
//    	$OUT .= "\texten => i,1,Playtones(congestion)\n";
//    	$OUT .= "\texten => i,2,Hangup\n";
//
//	Queues
//

	$OUT .= "[queues]\n";
	$sql = "SELECT * FROM IPphone ORDER BY pkey";
	foreach ($dbh->query($sql) as $IPphone) {
		if ($IPphone['technology'] == "Analogue" ||
				$IPphone['technology'] == "IAX2"  ||
				$IPphone['technology'] == "SIP"   ) {
			$OUT .= "\texten => " . $IPphone['pkey'] . ",1,agi(sarkhpe,Dial," . $IPphone['pkey'] . ",queue,)\n";
	  	}
    }
//
//	Clean up
//
//    	$OUT .= "\texten => t,1,Hangup\n";
//    	$OUT .= "\texten => h,1,Hangup\n";
//    	$OUT .= "\texten => i,1,Playtones(congestion)\n\n";


//
//  Class Of Service
//
	if ($global['COSSTART'] == "ON") {
		$orideopenarray = array();
		$orideclosedarray = array();
//
//      select the overrides first
//
        $sql = "SELECT * FROM COS ORDER BY pkey";
		foreach ($dbh->query($sql) as $mycos) {
			if ($mycos['orideopen'] == 'YES') {
				$orideopenarray[$mycos['pkey']] =	"YES";
			}
			if ($mycos['orideclosed'] == 'YES') {
				$orideclosedarray[$mycos['pkey']] =	"YES";
			}			
		}
//
//		now do the process 
//							
		$sql = "SELECT * FROM IPphone ORDER BY pkey";
		foreach ($dbh->query($sql) as $IPphone) {
			$OUT .= "[" . $IPphone['pkey'] . "opencos]\n";
			$OUT .= "\tinclude => Emergency\n"; 
//			
//			print the overrides
//			 
			foreach ($orideopenarray as $key => $value) {
				$OUT .= "\tinclude => " . $key . "\n";
			} 	
	 
			$sqlcos = 	"SELECT COS_pkey FROM IPphoneCOSopen where IPphone_pkey='" . $IPphone['pkey'] . "'";
			foreach ($dbh->query($sqlcos) as $coskeys) {
//
//          don't print if already overridden
// 
				if (! $orideopenarray [$coskeys['COS_pkey']]) {
					$OUT .= "\tinclude => " . $coskeys['COS_pkey'] . "\n";
				} 	
			}
			$OUT .= "\tinclude => Cosend\n";	
			$OUT .= "[" . $IPphone['pkey'] . "closedcos]\n";
			$OUT .= "\tinclude => Emergency\n"; 
			
			foreach ($orideclosedarray as $key => $value) {
				$OUT .= "\tinclude => " . $key . "\n";
			}
			  
			$sqlcos = 	"SELECT COS_pkey FROM IPphoneCOSclosed where IPphone_pkey='" . $IPphone['pkey'] . "'";
			foreach ($dbh->query($sqlcos) as $coskeys) {
//
//          don't print if already overridden
// 
				if (! $orideclosedarray [$coskeys['COS_pkey']]) {
					$OUT .= "\tinclude => " . $coskeys['COS_pkey'] . "\n";
				} 	
			} 
			$OUT .= "\tinclude => Cosend\n";
		}  
	}		
	$sql = "SELECT * FROM COS ORDER BY pkey";
	foreach ($dbh->query($sql) as $COS) {
		$OUT .= "[" . $COS['pkey'] . "]\n";
		$COS['dialplan'] = preg_replace ( '/\s+/'," ", $COS['dialplan'] );
		$COS['dialplan'] = preg_replace ( '/\s*$/',"", $COS['dialplan'] );
		$dialplan = explode(" ", $COS['dialplan']);
		foreach ($dialplan as $plan) {
			$OUT .= "\texten => $plan,1,Playtones(congestion)\n";
    		$OUT .= "\texten => $plan,2,Hangup\n";
		}
	}
	$OUT .= "\n[Emergency]\n"; 		
	$global['EMERGENCY'] = preg_replace ( '/\s+/'," ", $global['EMERGENCY'] );
	$global['EMERGENCY'] = preg_replace ( '/\s*$/',"", $global['EMERGENCY'] );
	$dialplan = explode(" ", $global['EMERGENCY']);
	foreach ($dialplan as $plan) {
			$OUT .= "\texten => $plan,1,agi(sarkhpe,OutCluster,\${EXTEN},,)\n";
	}
    	$OUT .= "\n[Cosend]\n";
/*
    	$OUT .= "\texten => _X.,1,agi(sarkhpe,OutCluster,\${EXTEN},,)\n";
    	$OUT .= "\texten => _[+*]X.,1,agi(sarkhpe,OutCluster,\${EXTEN},,)\n";
    	$OUT .= "\texten => _**X.,1,agi(sarkhpe,OutCluster,\${EXTEN},,)\n";
    	$OUT .= "\texten => _***X.,1,agi(sarkhpe,OutCluster,\${EXTEN},,)\n";
*/    	
    	$OUT .= "\texten => _[*#0-9]!,1,agi(sarkhpe,OutCluster,\${EXTEN},,)\n";
/*
 * Conferences
 * There are two types; the pre Ast11 MeetMe and the post ConfBridge
 * We will use the correct one for the release
 * 
 */  
	$OUT .= "\n[conferences]\n";	
	if ($vers < 11) {
		meetMe($OUT,$global,$dbh);
	}
	else {
		confBridge($OUT,$dbh);
	}
		

	   	
	   
//
//  begin mainmenu (inbound context) 
//	

	$OUT .= <<<HERE

[from-trunk]   ; FPBX Compatibility
	include => mainmenu

[digital]     ; PIKA Compatibility
        include => mainmenu

[fxo]	   ; PIKA Compatibility	
	exten => s,1,Set(chan=\${CUT(CHANNEL,/,3)})
	exten => s,n,GoTo(mainmenu,fxo\${chan},1)
    	
[gsm]	   ; PIKA Compatibility
	exten => s,1,Set(chan=\${CUT(CHANNEL,/,2)})
	exten => s,n,GoTo(mainmenu,GSM\${chan},1)
        
[from-pstn]    ; FPBX Compatibility 
	include => mainmenu
	exten => s,1,Set(chan=\${CUT(CHANNEL,/,2)})  
	exten => s,n,Set(chan=\${CUT(chan,-,1)})               
	exten => s,n,GoTo(mainmenu,DAHDI\${chan},1)


HERE;

$OUT .= "\n[mainmenu]\n";
//
//  include custom apps
//

	$OUT .= "\n\texten => sipsak,1,NoOp()\n";
	
	$sql = "select * from Appl";
 	foreach ($dbh->query($sql) as $row) {
		if ($row['span'] == "Both" || $row['span'] == "External") {
			$OUT .= "\tinclude => " . $row['pkey'] . "\n";    
		}
 	}
	$OUT .= "\n";
        $linenum = 1;
	$sql = "select * from lineIO order by pkey";
//	$canroute = FALSE;
 	foreach ($dbh->query($sql) as $row) {
	 	$sqlcar = "select * from Carrier where pkey ='" . $row['carrier'] . "'";
		$Carrier = $dbh->query($sqlcar)->fetch(PDO::FETCH_ASSOC);
		if ($Carrier['carriertype'] == "group" && $row['routeable'] != "YES") {
			continue;
		}
		if ($row['active'] != "YES") {
			continue;
		}
//
//  skip DAHDI trunks (handled in from-pstn - see above)
//
		if ($row['method'] == "DAHDI") {
			continue;
		}
// 	check for routable inbound trunks (i.e. a DiD number)
//		if (is_numeric($row['pkey'])) {
//			$canroute = TRUE;
//		}

		$OUT .= "\texten => " . $row['pkey'] . ",1,agi(sarkhpe,Inbound," . $row['pkey'] . ",,)\n";
	}
//  if there are no routeable inbound trunks then ring all the phones 
//	if ($canroute == FALSE) {
//		$OUT .= "\texten => s,1,Goto(extensions,\${SYSOP},1)\n";
//	}
    $OUT .= "\n";
    
	$OUT .= <<<THERE
 	exten => fax,1,GoToIf($["\${FAX}" = ""]?3:2)     ;no FAX defined - Hangup
 	exten => fax,2,GoTo(extensions,\${FAX},1)
	exten => fax,3,Playtones(congestion)

	exten => t,1,GotoIf($["\${OPEN}" = "YES"]?t,4)
	exten => t,2,Voicemail(\${SYSOP},su)
	exten => t,3,Hangup
	exten => t,4,Goto(extensions,\${SYSOP},1)
;	exten => t,5,Hangup
;	exten => h,1,Hangup
	exten => i,1,Goto(extensions,\${SYSOP},1)
	
[macro-clear]
        exten=>s,1,System(/bin/touch /opt/sark/var/spool/asterisk/monitor/\${filename})

[macro-pause]
        exten=>s,1,GoToIf($["\${CHANNEL}" = "\${channame}"]?:3)
        exten=>s,2,PauseMonitor
        exten=>s,3,NoOp(channel is \${CHANNEL} and channame is \${channame})

[macro-resume]
        exten=>s,1,GoToIf($["\${CHANNEL}" = "\${channame}"]?:3)
        exten=>s,2,UnPauseMonitor
        exten=>s,3,NoOp

[internal-presets]

THERE;
//
//  Internal presets
//
//  build pageall/ringall group 

    $dialstr = '';
    $ringall = '';
    $count = 1;
	$sql = "select * from IPphone order by pkey"; 
    foreach ($dbh->query($sql) as $row) { 
    	if ($count > 30) {
    		break;
    	}    
		$dialstr .= "SIP/" . $row['pkey'] .'&';
		$ringall .= "SIP/" . $row['pkey'] .'&';
		$count++;
    } 
//  strip trailing space and generate ringall
	$ringall = preg_replace ('/&/'," ", $ringall );
	$ringall = preg_replace ( '/\s*$/',"", $ringall ); 

	if ($ringall != '') {
		$OUT .= "\texten => RINGALL,1,agi(sarkhpe,Alias,$ringall,\${EXTEN},)\n"; 
//		$count = $dbh->exec("UPDATE speed SET out = '". $ringall . "' WHERE pkey = 'RINGALL'" );  
	}
//  strip last & and save pageall
    $dialstr = preg_replace ( '/&$/',"", $dialstr ); 
//    $res = $dbh->prepare("UPDATE page SET pagegroup = '". $dialstr . "' WHERE pkey = ?");
// 	$res->execute(array('pageall')); 
//	$count = $dbh->exec("UPDATE page SET pagegroup = '". $dialstr . "' WHERE pkey = 'pageall'" );   
	
//  done with pageall/ringall


	$speedflag = false;
	$sql = "select * from speed";
	$dialstr = "";
	$pagearray = array();
 	foreach ($dbh->query($sql) as $row) { 
		$outlist = synthAlias($dbh, $row['pkey']);
		foreach ($outlist as $ep) {
			if (strlen($ep) == $global['EXTLEN'] ) {
				$sql2 = "select * from IPphone WHERE pkey = '" . $ep . "'";                
				$IPphone = $dbh->query($sql2)->fetch(PDO::FETCH_ASSOC);
				if ($IPphone['technology'] == "Analogue") {
					$IPphone['technology'] = "DAHDI";
					$dialstr .= $IPphone['technology'] . "/" . $IPphone['channel'] ." ";
				}
				elseif ($IPphone['technology'] == "Custom") {
					continue;
//					$dialstr .= $IPphone['dialstring'] ." ";
				}
				else { 
					$dialstr .= $IPphone['technology'] . "/" . $ep . " ";	
				}	
			}
			elseif (preg_match(' /\// ',$ep)) {
				$dialstr .= $ep . " ";
			}
			else {
				$dialstr .= "Local/$ep@internal ";	 		
			}
		}
		$dialstr = preg_replace ( '/\s*$/',"", $dialstr );
		if ($row['grouptype'] != "Page" && $row['pkey'] != 'RINGALL') {
	/*
		This stanza looks redundant.  
		It may have been an old optimization to handle single extension call groups but the preg_match can't succeed.
		The else will always be taken. 
	 */			
			if (preg_match(' /^\d{4}$/ ',$dialstr) && $row['grouptype'] != "Alias") {
				$OUT .= "\texten => " . $row['pkey'] . ",1,agi(sarkhpe,InCall," . $row['out'] . ",,)\n";
                $OUT .= "\texten => *" . $row['pkey'] . ",1,GoTo(extensions,*" . $row['out'] . ",1)\n";
                $OUT .= "\texten => **" . $row['pkey'] . ",1,GoTo(extensions,**" . $row['out'] . ",1)\n";
			}
			else {
				
				if (empty($row['divert'])) {
					$OUT .= "\texten => " . $row['pkey'] . ",1,agi(sarkhpe,Alias,$dialstr,\${EXTEN},)\n";
				}
				else {
					$OUT .= "\texten => **" . $row['pkey'] . ",hint,Custom:**" . $row['pkey'] . "\n"; 
					$OUT .= "\texten => **" . $row['pkey'] . ",1,NoOp()\n";
					$OUT .= "\texten => **" . $row['pkey'] . ',n,GoToIf($["${DEVICE_STATE(Custom:${EXTEN})}" = "NOT_INUSE"]?set' . $row['pkey'] . ':free' . $row['pkey'] . ")\n";

					$OUT .= "\texten => **" . $row['pkey'] . ',n(set' . $row['pkey'] . '),NoOp()' ."\n";
					$OUT .= "\texten => **" . $row['pkey'] . ',n,Set(DEVICE_STATE(Custom:${EXTEN})=BUSY)' . "\n";
					$OUT .= "\texten => **" . $row['pkey'] . ',n,Playback(activated)' . "\n";
					$OUT .= "\texten => **" . $row['pkey'] . ',n,Hangup()' . "\n";

					$OUT .= "\texten => **" . $row['pkey'] . ',n(free' . $row['pkey'] . '),NoOp()' ."\n";					
					$OUT .= "\texten => **" . $row['pkey'] . ',n,Set(DEVICE_STATE(Custom:${EXTEN})=NOT_INUSE)' . "\n";
					$OUT .= "\texten => **" . $row['pkey'] . ',n,Playback(de-activated)' . "\n";
					$OUT .= "\texten => **" . $row['pkey'] . ',n,Hangup()' . "\n";

					$OUT .= "\texten => " . $row['pkey'] . ',1,NoOp(Custom:**${EXTEN} state is ${DEVICE_STATE(Custom:**${EXTEN})}))' . "\n";
					$OUT .= "\texten => " . $row['pkey'] . ',n,GoToIf($["${DEVICE_STATE(Custom:**${EXTEN})}"="BUSY"]?divert' . $row['pkey'] . ":normal" . $row['pkey'] . ")\n"; 
					$OUT .= "\texten => " . $row['pkey'] . ',n(normal' . $row['pkey'] . '),NoOp()' . "\n";
					$OUT .= "\texten => " . $row['pkey'] . ",n,agi(sarkhpe,Alias,$dialstr,\${EXTEN},)\n";
					$OUT .= "\texten => " . $row['pkey'] . ',n,Hangup()' . "\n";
					$OUT .= "\texten => " . $row['pkey'] . ',n(divert' . $row['pkey'] . '),NoOp()' . "\n";
					$OUT .= "\texten => " . $row['pkey'] . ',n,GoTo(internal,' . $row['divert'] . ",1)\n";
				}

			}
		}
		else {
			// build array of page groups (key & dialstring)
				$dialstr = preg_replace ( '/\s/',"&", $dialstr );
				$pagearray [$row['pkey']] =	$dialstr; 			
		} 
		$dialstr = "";
	}
// insert page group dialstrings into their tables 
/*	
	foreach ($pagearray as $pkey=>$dialstr) {
		$count = $dbh->exec("UPDATE speed SET pagegroup = '". $dialstr . "' WHERE pkey = '" . $pkey . "'" );
 	}
*/  
	syslog(LOG_WARNING, "Done ring groups" . "\n");
	
// V4 open/closed lamps
	

	$OUT .= <<<ANDHERE
;
;   open/close custom devices
;
		
	exten => MASTER,1,Set(state=\${DB(STAT/OCSTAT)})
	exten => MASTER,n,GoToIf(\$["\${state}" = "AUTO"]?closeup:openup)

	exten => MASTER,n(closeup),Set(DB(STAT/OCSTAT)=CLOSED)
	exten => MASTER,n,Set(DEVICE_STATE(Custom:MASTER)=INUSE)
	exten => MASTER,n,Playback(activated)
	exten => MASTER,n,Hangup

	exten => MASTER,n(openup),Set(DB(STAT/OCSTAT)=AUTO)
	exten => MASTER,n,Set(DEVICE_STATE(Custom:MASTER)=NOT_INUSE)
	exten => MASTER,n,Playback(de-activated)
	exten => MASTER,n,Hangup
	
ANDHERE;

	$OUT .= "\n";
	$sql = "select pkey from Cluster order by pkey";
 	foreach ($dbh->query($sql) as $row) {
		$OUT .= "\texten => " . $row['pkey'] . ",1,Set(state=\${DB(" . $row['pkey'] . "/OCSTAT)})\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n,GoToIf(\$[\"\${state}\" = \"AUTO\"]?" . $row['pkey'] . "close:" . $row['pkey'] . "open)\n";
		$OUT .= "\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n(" . $row['pkey'] . "close),Set(DB(" . $row['pkey'] . "/OCSTAT)=CLOSED)\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n,Set(DEVICE_STATE(Custom:" . $row['pkey'] . ")=INUSE)\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n,Playback(activated)\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n,Hangup\n";
		$OUT .= "\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n(" . $row['pkey'] . "open),Set(DB(" . $row['pkey'] . "/OCSTAT)=AUTO)\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n,Set(DEVICE_STATE(Custom:" . $row['pkey'] . ")=NOT_INUSE)\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n,Playback(de-activated)\n";
		$OUT .= "\texten => " . $row['pkey'] . ",n,Hangup\n";	
		$OUT .= "\n";	
	}	
	

	$OUT .= <<<ANDTHERE
;
;	SARK Service Codes
;
;
        exten => _*12[*]XXXX,1,agi(sarkhpe,\${EXTEN},,)	; SYSOP Redir
        exten => _*12[*]XXX,1,agi(sarkhpe,\${EXTEN},,)	; SYSOP Redir
        exten => _*12[*],1,agi(sarkhpe,\${EXTEN},,)		; SYSOP OFF
        exten => _*1[89][*],1,agi(sarkhpe,\${EXTEN},,)		; DND ON/OFF
        exten => _*20[*],1,agi(sarkhpe,\${EXTEN},,)		; DND TOGGLE
        exten => _*2[12789][*]XX.,1,agi(sarkhpe,\${EXTEN},,)	; CF ONs
        exten => _*3[89][*]XX.,1,agi(sarkhpe,\${EXTEN},,)	; CFxxCL ON
        exten => _*3[89][*],1,agi(sarkhpe,\${EXTEN},,)		; CFxxCL OFF
        exten => _*2[123789][*],1,agi(sarkhpe,\${EXTEN},,)	; CF OFFs
        exten => _*26[*],1,agi(sarkhpe,\${EXTEN},,)		; RingDelay
        exten => _*26[*]X,1,agi(sarkhpe,\${EXTEN},,)		; RingDelay
        exten => _*26[*]XX,1,agi(sarkhpe,\${EXTEN},,)		; RingDelay
        exten => _*3[012345][*],1,agi(sarkhpe,\${EXTEN},,)	; TIMERS
        exten => _*5[012567][*],1,agi(sarkhpe,\${EXTEN},,)	; VMAIL, TIME etc.
        exten => _*60*XXXX,1,agi(sarkhpe,\${EXTEN},,)		; Greetings
        exten => _*61*XXXX,1,Playback(usergreeting\${EXTEN,4})	; Greetings
        exten => _*6[34][*],1,agi(sarkhpe,\${EXTEN},,)		; Agent pause/unpause(63 64)
        exten => _*6[56][*],1,agi(sarkhpe,\${EXTEN},,)		; Agent Login/out(65 66)
        exten => _*67[*]XXXX,1,agi(sarkhpe,\${EXTEN},,)		; ChanSpy (Whisper)
        exten => _*67[*]XXX,1,agi(sarkhpe,\${EXTEN},,)		; ChanSpy (Whisper)        
        exten => _*68[*]XXXX,1,agi(sarkhpe,\${EXTEN},,)		; ChanSpy
        exten => _*68[*]XXX,1,agi(sarkhpe,\${EXTEN},,)		; ChanSpy
        exten => _*40[*],1,agi(sarkhpe,\${EXTEN},,)           	; Page
        exten => _*40[*]XXXX,1,agi(sarkhpe,\${EXTEN},,)       	; Page Group
        exten => _*40[*]XXX,1,agi(sarkhpe,\${EXTEN},,)		; Page Group
        exten => _*4[12][*],1,agi(sarkhpe,\${EXTEN},,)		; ProVu DND
;
;   RSSH support sessions - requires rssh licence keys to be installed
;
		exten=>_*44*XXX.,1,Authenticate(\${SYSPASS})
		exten=>_*44*XXX.,n,system(echo "PORT1=\${EXTEN:4}" > /opt/sark/service/rssh/serviceport1)
		exten=>_*44*XXX.,n,system(sudo /usr/bin/sv o srk-ua-rssh)
		exten=>_*44*XXX.,n,Playback(activated)
		exten=>*_44*XXX.,n,Hangup

		exten=>*44*,1,Authenticate(\${SYSPASS})
		exten=>*44*,n,system(sudo /usr/bin/sv d srk-ua-rssh)
		exten=>*44*,n,Playback(de-activated)
		exten=>*44*,n,Hangup

;
;	Wakeup call 
;
		exten => _*24[*],1,agi(kwakeup)
		exten => _*24[*]XXX,1,Authenticate(\${SYSPASS})
		exten => _*24[*]XXX,n,agi(kwakeup,EXT\${EXTEN:4})
		exten => _*24[*]XXXX,1,Authenticate(\${SYSPASS})
		exten => _*24[*]XXXX,n,agi(kwakeup,EXT\${EXTEN:4})
		exten => _***XXX,1,Set(CHANNEL(language)=\${LANGUAGE})
		exten => _***XXX,n,Dial(SIP/\${EXTEN:3},60)
		exten => _***XXXX,1,Set(CHANNEL(language)=\${LANGUAGE})
		exten => _***XXXX,n,Dial(SIP/\${EXTEN:3},60)

;
;	NANP Vertical Service Code Compatibility
;
       	exten => *60,1,agi(sarkhpe,*55*,,)
       	exten => *65,1,agi(sarkhpe,*56*,,)

       	exten => _*72X.,1,agi(sarkhpe,*21*\${EXTEN:3},,)
       	exten => *73,1,agi(sarkhpe,*21*,,)
       	exten => _*77XXXX,1,agi(sarkhpe,*60*\${EXTEN:3},,)
       	exten => *78,1,agi(sarkhpe,*18*,,)
       	exten => *79,1,agi(sarkhpe,*19*,,)

       	exten => _*90X.,1,agi(sarkhpe,*22*\${EXTEN:3},,)
       	exten => *91,1,agi(sarkhpe,*22*,,)
       	exten => *97,1,agi(sarkhpe,*50*,,)
       	exten => *98,1,agi(sarkhpe,*51*,,)
       	exten => _*99XXXX,1,agi(sarkhpe,*61*\${EXTEN:3},,)

;       	exten => h,1,Hangup
;       	exten => t,1,Hangup
       	exten => i,1,Playtones(congestion)

[defaultOpenGreet]
        include => extensions
	include => internal-presets
;	include => conferences

        exten => s,1,Background(if-u-know-ext-dial)
        exten => s,2,Background(otherwise)
        exten => s,3,Background(pls-hold-while-try)
        exten => s,4,Background(silence/5)
        exten => s,5,Goto(defaultOpenGreet,t,1)

        exten => t,1,Goto(extensions,\${SYSOP},1)                ;to operator
        exten => t,2,Hangup

	exten => i,1,Background(invalid)
	exten => i,2,Goto(defaultOpenGreet,s,4)

[defaultClosedGreet]
        include => extensions
	include => internal-presets
;	include => conferences

	exten => s,1,Background(were-sorry)
	exten => s,2,Background(nbdy-avail-to-take-call)
        exten => s,3,Background(if-u-know-ext-dial)
        exten => s,4,Background(otherwise)
        exten => s,5,Background(pls-hold-while-try)
        exten => s,6,Background(silence/5)
        exten => s,7,Goto(defaultClosedGreet,t,1)

        exten => t,1,Goto(extensions,\${CLUSTEROP},1)                ;to operator
        exten => t,2,Hangup

	exten => i,1,Background(invalid)
	exten => i,2,Goto(defaultClosedGreet,s,6)

[customOpenGreet]
        include => extensions
	include => internal-presets
;	include => conferences

        exten => s,1,Background(\${CUSTOMGREET})
        exten => s,2,Background(silence/3)
        exten => s,3,Goto(customOpenGreet,t,1)

        exten => t,1,Goto(extensions,\${SYSOP},1)                ;to operator
        exten => t,2,Hangup

	exten => i,1,Background(invalid)
	exten => i,2,Goto(customOpenGreet,s,2)

[customClosedGreet]
        include => extensions
	include => internal-presets
;	include => conferences

	exten => s,1,Background(\${CUSTOMGREET})
        exten => s,2,Background(silence/1)
        exten => s,3,Goto(customClosedGreet,t,1)

        exten => t,1,Voicemail(\${CLUSTEROP},s)                ;to operator
        exten => t,2,Hangup

	exten => i,1,Background(invalid)
	exten => i,2,Goto(customClosedGreet,s,2)
;
;#####################################################################
;
;	Customer Supplied Contexts below this line (if any).
;
;#####################################################################

ANDTHERE;

//
// Custom Apps
//	
	$OUT .= "\n"; 
	$sql = "select * from Appl";
 	foreach ($dbh->query($sql) as $row) {
		$OUT .= ";\n";
		$OUT .= ";\tCustomer Supplied Context " .  $row['pkey'] . "\n";
		$OUT .= ";\n";
    		$OUT .= "[" .  $row['pkey'] . "]\n";
    		$OUT .= $row['extcode'];
		$OUT .= ";\n";
	}
		   
// write the generated file 

	$fh = fopen("/etc/asterisk/extensions.conf", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh); 
// clean it
	`dos2unix /etc/asterisk/extensions.conf >/dev/null 2>&1`;
//
//	All done.	
//
}
catch(PDOException $e) {

//    	echo $e->getMessage();
    	$errorMsg = $e->getMessage();
    	syslog(LOG_WARNING, "DB error in extension generate - $errorMsg" );
}

function meetMe(&$OUT,&$global,$dbh) {
/*
 * conventional conferences in meetme -> these cannot be managed
 */
		$file = '/etc/asterisk/meetme.conf' or die('Could not read file!');
		$rec = file($file) or die('Could not read file!');
		$conferences = array(); 
			foreach ($rec as $line) {
			if (!preg_match(' /^\s*;/ ',$line)) {   
				if (preg_match(' /conf\s*=>?\s*(\d{3,4})/ ',$line,$matches)) {
					array_push($conferences, $matches[1]);
				}
			} 	
		}
	foreach ($conferences as $conf) {
		if ($global['CONFTYPE'] == "simple" ) {
			$OUT .= "\texten => $conf,1,Meetme($conf,Mp)\n";
		}
		else {
			$OUT .= "\texten => $conf,1,Meetme($conf,MpI)\n";
		}
		$OUT .= "\texten => $conf,hint,Meetme:$conf\n";
	}	

/*
 * 4.0.1 browser managed conferences
 */
	$sql = "SELECT * FROM meetme ORDER BY pkey";
	foreach ($dbh->query($sql) as $room) { 
		if ($room['type'] == "simple" ) {
			$OUT .= "\texten => " . $room['pkey'] . ",1,Meetme(" . $room['pkey'] . ",Mp)\n";
		}
		else {
			$OUT .= "\texten => " . $room['pkey'] . ",1,Meetme(" . $room['pkey'] . ",MpI)\n";
		}
		$OUT .= "\texten => " . $room['pkey'] . ",hint,Meetme:" . $room['pkey'] . "\n";
	}	   		
	
}

function confBridge(&$OUT,$dbh) {
	
/* 
 * 4.2 managed conferences
 */
	$profile = 'sark_user';
 	$sql = "SELECT * FROM meetme ORDER BY pkey";
	foreach ($dbh->query($sql) as $room) {
		$OUT .= "\texten => " . $room['pkey'] . ",1,NoOp(conference " . $room['pkey'] . ")\n";
		$OUT .= "\tsame => n,Answer(500)\n";
		if ($room['pin']) {
			$OUT .= "\tsame => n,Authenticate(" . $room['pin'] . ")\n";
		}		 
		if ($room['type'] == "hosted" ) {			
			$profile = 'sark_hosted_user';
		}
		else {
			$profile = 'sark_user';
		}		
		$OUT .= "\tsame => n,ConfBridge(\${EXTEN},,$profile)\n";
		$OUT .= "\tsame => n,Hangup()\n";
	}	   		 	
}	

function synthAlias($dbh, $alias)  {
//
//  Recursive function to build a callgroup.
//  input - db handle & callgroup number
//  output - unordered array of extension numbers or an empty array
//	
	$array=array();
    $sql = "select count(*) from speed WHERE pkey = '" . $alias . "'";
	$res = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
	if ($res['count(*)'] != 0) {
		$sql = "select * from speed WHERE pkey = '" . $alias . "'";
		$row = $dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
		$row['out'] = preg_replace ( '/\s+/'," ", $row['out'] );
		$row['out'] = preg_replace ( '/\s*$/',"", $row['out'] );
		$extension = explode(" ", $row['out']);
        	foreach ($extension as $ext) {
			$array = array_merge((array)$array, (array)synthAlias($dbh, $ext));
        	}
    	}
	else {
		array_push($array,$alias);
	}
    	return (isset($array) ? $array : false);
}
?>
