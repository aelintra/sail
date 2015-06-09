<?php
// +-----------------------------------------------------------------------+
// |  Copyright (c) CoCoSoft 2007-12                                  	   |
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
// Incident module
//
// Originate calls to conference rooms and/or send sms messages to targets
//
// build a set of callfiles according to /etc/asterisk/sark_mcstcnf.conf
// Once built, move them to the asterisk outgoing spool file for despatch
// After the calls have been sent away (if there were any)
// send any sms messages using the specified sms service.  
// sms services providers can be added to suit your taste
// but we offer three examples below.
//

$defltcallfile=array (		
		archive=>"no",
		callerid=>"",
		context=>"mcstcnfxfer",
		extension=>"s",
		greeting=>"beep",
		maxretries=>0,
		priority=>1,
		retrytime=>60,
		room=>300,
		waittime=>45
);

$defltsms=array(
		smsapiid=>'',	// clickatel only
		smscpath=>"DAHDI/g0/17094009",
		smshandler=>"smsc",
		smsmsg=>"Incident - call incident room",
		smsinfomsg=>"",
		smsoriginator=>'AsteriskPBX',
		smspassword=>'smspassword',
		smsuser=>'smsuid',
		smsretries=>3		
);

$smsvars=array();

/*
 * get the parameters and do checks
 */ 
$options = getopt("n:r::");
$conf = parse_ini_file('/etc/asterisk/sark_mcstcnf.conf',1);
var_dump($conf);
// check that a short code has been passed as -n
if (!$options ["n"]) {
	syslog(LOG_WARNING, "mstcaller.php called without n parameter " );
	exit;
}
else {
	$sc = $options ["n"];
}
// check if a room number has been passed as -r
if (!$options ["r"]) {
	syslog(LOG_WARNING, "mstcaller.php called without r parameter " );
}
else {
	$defltcallfile["room"] = $options["r"];
}

// check the short code exists in the conf file	
if (!is_array($conf["$sc"])) {
	syslog(LOG_WARNING, "mstcaller.php called with invalid n parameter $sc" );
	exit;
}
// OK - build the callfiles for this list 
if (is_array($conf["$sc"]["number"])) {
	foreach ($conf["$sc"]["number"] as $channel) {
		buildCallfile($conf,$sc,$channel,$defltcallfile);
	}
}
// Now send any sms messages requested
$smsvars = array_merge($smsvars,$defltsms);
if (is_array($conf["$sc"]["smsnum"])) {
	foreach ($conf["$sc"] as $key=>$value) {		
		if (preg_match(' /^sms/ ',$key)) {
				$smsvars[$key] = $value;
		}
	}
	$smsvars['smshandler']($smsvars);
}
// and out...
exit;


function buildCallfile($conf,$sc,$channel,&$defltcallfile=array()) {
/*
 * create a call file in /tmp and then move it to /var/spool/asterisk/outgoing.
 * This is the way that Digium advises because otherwise Asterisk may start executing the 
 * callfile before we've finished writing it.  A move is atomic.
 */ 
	$vars=null;
	$callfile=array();
	
// set defaults for the callfile
	$callfile = array_merge($callfile,$defltcallfile);
// set overrides in the callfile from the multicast list (ignore sub-arrays)
	foreach ($conf["$sc"] as $key=>$value) {
		if ($key=="number" || preg_match(' /^sms/ ',$key)) {
			continue;
		}
		$callfile[$key] = $value;
	}	
// xlate 'room' and 'greeting' to setvars
	if (array_key_exists("room", $callfile)) {
		$vars .= "setvar: room=".$callfile["room"]."\n";
		unset($callfile["room"]);
	}
	if (array_key_exists("greeting",$callfile)) {
		$vars .= "setvar: greeting=".$callfile["greeting"]."\n";
		unset($callfile["greeting"]);
	}	
 		
// create the callfile
	$OUT  .= "channel: ".$channel."\n";
	foreach ($callfile as $key=>$value) {
		if ($value) {
			$OUT .= $key.': '.$value."\n";
		}
	}
// append the setvars
	$OUT .= $vars;
// write it out to disk
	$file="callfile_".rand(10000, 99999);
	$fh = fopen("/tmp/$file", 'w') or die('Could not open file!');
	fwrite($fh,$OUT) or die('Could not write to file');
	fclose($fh); 
// move it to the asterisk spool
//	exec ("/bin/mv /tmp/$file /var/spool/asterisk/outgoing");	
}

/***********************************************************************
 * Below here you can add support for different sms services
 * You simply supply the function name in smshandler and it
 * will be invoked automatically and given the sms data 
 * in the $smsvars array (a merge of the $defltsms array and
 * any sms variables you have supplied in the .conf file ).
 **********************************************************************/
 
/* 
 * example 1 - call a TDM SMSC using 
 * Asterisk's sms wrapper 'smsq'
 */  
function smsc(&$smsvars) {

	$message = $smsvars['smsmsg'];
	$channel = $smsvars['smscpath'];
	$retries = $smsvar['smsretries'];
	foreach ($smsvars['smsnum'] as $smsnum ) {
		$rc = `/usr/sbin/smsq $smsnum $message --motx-channel $channel --motx-retries $retries`;
		syslog (LOG_WARNING, "mcstcaller sms to $smsnum via SMSC $channel"); 
	}
	return 0;
}

/*
 * example 2 - call the dynmark SMS gateway using HTTPS SOAP 
 */ 
function dynmark(&$smsvars) {
/*
 * this function requires php-soap in order to run.
 * On RHEL/CentOS do  -  
 * 			yum install php-soap
 * 
 */

define("DYNMARKWEBSERVICE", "https://services.dynmark.com/WebServices/MessagingServicesWS.asmx?wsdl");
define("RESPONSE_TIMEOUT", 130); // Dynmark recommended timeout of 130 seconds

try {

	ini_set('default_socket_timeout', RESPONSE_TIMEOUT); 

	$options = array(
		'soap_version'=>SOAP_1_2,
		'exceptions'=>true,
		'cache_wsdl'=>WSDL_CACHE_NONE
	);

	$client = new SoapClient(DYNMARKWEBSERVICE, $options);
	$response = $client->SendMessages(
		array(
			'name' => $smsvars['smsuser'],
			'password' => $smsvars['smspassword'],
			'originator' => $smsvars['smsoriginator'],
			'text' => $smsvars['smsmsg'],
			'recipients' => array(
				'string' => $smsvars['smsnum']
			)
		)
	);
	foreach ($smsvars['smsnum'] as $smsnum) {
		syslog (LOG_WARNING, "mcstcaller sms to $smsnum Dynmark Transaction ID: " . $response->SendMessagesResult );
	}
}

catch (SoapFault $fault) {
	echo 'Caught SoapFault: ', $fault->getMessage(), "\n";
}

catch (Exception $ex) {
	echo 'Caught exception: ', $fault->getMessage(), "\n";
}

}

/*
 * Example 3 - Clickatel
 */

function clickatel(&$smsvars) { 
/*
 * call the Clickatel gateway using HTTP GET
 */
  	
	$message = $smsvars['smsmsg'];
	foreach ($smsvars['smsnum'] as $smsnum ) {
        $result = file_get_contents('http://api.clickatell.com/http/sendmsg?api_id='.
			$smsvars['smsapiid'].'&user='.$smsvars['smsuser'].'&password='.$smsvars['smspassword'].
			'&to='.$smsnum.'&text='.substr(urlencode($message),0,160) );
		syslog (LOG_WARNING, "mcstcaller sms to $smsnum Clickatel responded: " . $result );
    } 
}
/*
 * Example 4 - aql (needs testing)
 */

function aql(&$smsvars) {

        $message = $smsvars['smsmsg'];
        foreach ($smsvars['smsnum'] as $smsnum ) {
        $result = file_get_contents('https://gw.aql.com/sms/postmsg.php?to_num=' . $smsnum .
                        '&message=' . substr(urlencode($message),0,160)  . '&flash=0&username=' . $smsvars['smsuser'] .
                        '&password=' . $smsvars['smspassword'] );
                syslog (LOG_WARNING, "mcstcaller sms to $smsnum aql responded: " . $result );
    }
}

?>
