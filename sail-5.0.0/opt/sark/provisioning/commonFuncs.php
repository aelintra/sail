<?php

/*
 * FUNCTIONS
 */ 
 
/*
 *  Parse the config and recursively merge any includes  
 */
function cleanConfig($phonepkey,$rawConfig,&$db,&$retstring,&$loopCheck,$sndcreds) {
/*
 * FIXME - I need a loop check 
 */	
  global $blfKeys;

  $inline=False;
  $lines = explode("\n", $rawConfig);

  foreach ($lines as $line) {
	$line = preg_replace("/\r/", "", $line);
// skip tftp file names and square brackets from pre 4.0.1 configs
	if (preg_match(' /\["(.*)"/',$line)) {
		continue;
	}
// only process the first file if this is an old V2 multi-file group
	if (preg_match(' /^\]\s?$/',$line)) {
		break;
	}
// skip old V3 snom provisioning html tags
	if (preg_match(' /^\<\/?(html|pre)\>$/',$line)) {
		continue;
	}
	
// Don't send creds unless specifically told to	
	if ( ! $sndcreds ) {
		if (preg_match(' /\$password/ ',$line)) {
			continue;
		}
		if (preg_match(' /\$ext/ ',$line)) {
			continue;
		}		
	}				
	
// check for INCLUDE and recurse
	if (preg_match(' /^[;#]INCLUDE\s*([\w_\-\.\/\(\)\s]*)\s*$/',$line,$match)) {
		$devpkey = trim($match[1]);
//		$phonepkey = trim($phonepkey);
		if (preg_match( ' /\.[LF]key$/ ',$devpkey )) {
			genFkeys($phonepkey,$devpkey,$db,$inline);
			continue;
		}
		try {
			$configs = $db->prepare('select pkey, provision from Device where pkey = ?');
			$configs->execute(array($devpkey));
			$thisConfig = $configs->fetchObject();
		} catch (Exception $e) {
			logIt("SQL error retrieving Descriptor $devpkey");
		}
		if (!is_object($thisConfig)) {
			logIt("Unable send Descriptor file for $devpkey - skipping");
			continue;
		}
		if (isset($loopcheck[$devpkey])) {
			logIt("LOOP FOUND in INCLUDES for $devpkey - won't touch it again!");
			continue;
		}
		if (! isset ($thisConfig)) {
			logIt("Can't find INCLUDE for $devpkey - skipping");
			continue;
		}
		$loopcheck[$devpkey] = true;
		$rawConfig = $thisConfig->provision;	
		cleanConfig($devpkey,$rawConfig,$db,$retstring,$loopCheck,$sndcreds); 
		continue;
	}	
	$retstring .= $line . "\n";
  }
}

function genFkeys($phonepkey,$devpkey,$db,&$inline) {

	global $blfKeys;
	
	$xLateType = Array (
		"aastra" => Array (
			"None" => ""
		),
		"panasonic" => Array(
			"blf" => "CONTACT",
			"speed" => "ONETOUCH",
			"line" => "DN",
			"None" => "DN"
		),	
		"panasonicHDV" => Array(
			"blf" => "BLF",
			"speed" => "ONETOUCH",
			"line" => "LINE",
			"None" => ""
		),					
		"polycom" => Array (
			"None" => ""
		),				
		"snom" => Array (
			"None" => "Line",
		),
		"yealink" => Array (
			"blf" => "16",
			"speed" => "13",
			"line" => "15",
			"None" => "0"
		),
	);
	$xLateTypeValue = Array (
		"snom" => Array (
			"blf" => '<sip:$value@' . ret_localip() . ';user=phone>|*8'		
		)	
	);	

	preg_match(' /^(\w+)\.[LF]key/ ',$devpkey,$match);
	$mfg = $match[1];
		
    $template = $db->prepare('select provision from Device where pkey = ?');
	$template->execute(array($devpkey));
	$thistemplate = $template->fetchObject();
	
	$phoneFKeys = $db->prepare('select * from IPphone_FKEY where pkey = ?');
	$phoneFKeys->execute(array($phonepkey));
	$fKeys = $phoneFKeys->fetchAll();
	
	$keyseq_offset = 0;
	if (preg_match(' /^[Ss]nom/ ', $devpkey)) {
		$keyseq_offset = -1;
	}

	foreach ($fKeys as $row) {
		if (preg_match ('/Default/', $row['type'] )) {
			continue;
		}		
		if (preg_match ('/None/', $row['value'] )) {
			$row['value'] = '';
		}		
		if (preg_match ('/None/', $row['label'] )) {			
			$row['label'] = '';
		}
		if (preg_match ('/None/', $row['type'] )) {
			$row['value'] = '';
			$row['label'] = '';
		}		
				
		if ( isset ($xLateType[$mfg][ $row['type'] ] ) ) {
			$row['type'] = $xLateType[$mfg][$row['type']];
		}
				
		$seq = $row['seq'] + $keyseq_offset;
		if (isset($thistemplate->provision)) {  	
			$blfKeys[$seq] = $thistemplate->provision;
			if ( isset ($xLateTypeValue[$mfg][ $row['type'] ] ) ) {
				$xlatevalue = preg_replace('/\$value/m', $row['value'], $xLateTypeValue[$mfg][$row['type']]);
				$blfKeys[$seq] = preg_replace ( '/\$value/m', $xlatevalue, $blfKeys[$seq]);
			}
			else {
				$blfKeys[$seq] = preg_replace ( '/\$value/m', $row['value'], $blfKeys[$seq]);
			}			
			$blfKeys[$seq] = preg_replace ( '/\$seq/m', $seq, $blfKeys[$seq]);
			$blfKeys[$seq] = preg_replace ( '/\$type/m', $row['type'], $blfKeys[$seq]);			
			$blfKeys[$seq] = preg_replace ( '/\$label/m', $row['label'], $blfKeys[$seq]);
			$blfKeys[$seq] = preg_replace ( '/\$localip/m', ret_localip(), $blfKeys[$seq]);
/*
 * Cisco BLFs have to be handled differently; we need to build the fnc variable 
 * for each fkey type.
 */
			 if ($mfg == 'cisco') {				 
				if ($row['type']=='blf') {
					$blfKeys[$seq] .= "\n<Extended_Function_" . $seq . '_ ua="na">' . 
						'fnc=sd+blf+cp;sub=' . $row['value'] . '@$PROXY;ext=' . $row['value'] . 
						'@$PROXY;nme=' . $row['label'] . '</Extended_Function_' . $seq . "_>";
				}
				if ($row['type']=='speed') {
					$blfKeys[$seq] .= "\n<Extended_Function_" . $seq . '_ ua="na">' . 
						'fnc=sd;ext=' . $row['value'] . '@$PROXY;nme=' . $row['label'] . '</Extended_Function_' . $seq . "_>";
				}
				if ($row['type']=='line' ) {
					$blfKeys[$seq] = preg_replace ( '/Disabled/m', "1", $blfKeys[$seq]);
				}			
			}													 
		} 
	}
}
/*
 *  Polycoms are different
 */ 
function polycomSubConfig($mac,$fname,$db) {

	global $haclusterip;
	global $hausecluster;
	global $local;
	
	$retstring = NULL;		
	$fname = preg_replace('/^-/','',$fname);

	header('Content-type: text/plain');
	try {
		$extConfig = $db->prepare('select pkey,provision,device,desc,location,passwd,sndcreds from IPphone where technology=? AND lower(macaddr) = ? limit 1');
		$extConfig->execute(array('SIP',$mac));
		
	} catch (Exception $e) {
		logIt("Unable get mac file for Polycom $mac");
		send404();
		exit;
	}
	
	$thisextConfig = $extConfig->fetchObject();
	
	if (!isset ($thisextConfig->pkey)) {
		logIt("$mac-$fname MAC user not found in db - suspicious;  Sending 404 and giving up");
		send404();
		exit;		
	}
	if (isset($thisextConfig->location) && $thisextConfig->location == 'remote') {
		$local=false;
	}		

	try {	
		$configs = $db->prepare('select provision from Device where pkey = ?'); 
		$configs->execute(array($fname));
		
	} catch (Exception $e) {
		logIt("Unable get Polycom sub-file $fname");
		send404();
		exit;
	}
	$thisConfig = $configs->fetchObject();
	if (! isset($thisConfig->provision)) {
		logIt("$mac-$fname provisioning data not found in db.  Sending 404 and giving up");
		send404();
		exit;		
	}

	$sndcreds=false;
	if (isset ($thisextConfig->sndcreds)) {
		if (preg_match( '/(Always|Once)/',$thisextConfig->sndcreds)) {
			$sndcreds=true;
		}
	}
	
	if (! $sndcreds ) {
		if ($fname == 'polycom-phone1.cfg' ) {
			logIt("$mac-$fname Sending 404 because creds already sent");
			send404();
			exit;
		}
	}	

//create an empty loopcheck array 
	$loopCheck = array();	
	$rawConfig = $thisConfig->provision . "\n";	
	cleanConfig($thisextConfig->pkey,$rawConfig,$db,$retstring,$loopCheck,$sndcreds);

	try {	  
		$global = $db->query("select * from globals")->fetch();

	} catch (Exception $e) {
		logIt("Unable to retrieve cluster values");
	}
	$haclusterip = $global['HACLUSTERIP'];
	$hausecluster = $global['HAUSECLUSTER'];

// substitute real values into the output	
	$retstring = preg_replace ( '/\$localip/', ret_localip(), $retstring);
	$retstring = preg_replace ( '/\$desc/', $thisextConfig->desc, $retstring);
	$retstring = preg_replace ( '/\$password/', $thisextConfig->passwd, $retstring);
	$retstring = preg_replace ( '/\$ext/', $thisextConfig->pkey, $retstring);
	logIt("sending config $mac-$fname");

/*
 * if the provisioning stream contains a positioning marker
 * for the fkeys, put them there, otherwise just append them
 */ 
	if (preg_match('/\$fkey/',$retstring) ) {
		$retstring = preg_replace ( '/\$fkey/', getBlf(), $retstring);
	}
	else {
		$retstring .= getBlf();
	}
	echo $retstring;
// disable auth send for next time
	if ($fname == 'polycom-phone1.cfg') {
		if ($thisextConfig->sndcreds != 'Always') { 
			try {
				$update = $db->prepare("update ipphone set sndcreds='No' where  pkey = '" . $thisextConfig->pkey . "'");
				$update->execute();
			} catch (Exception $e) {
				logIt("Unable to update extension sndcreds");
			}
		}
	}		
} 

/*
 * Polycom firmware download - not used - mod_rewrite handles instead 
 */
function polycomFirmware($frequest) {
	logit ("dealing with Polycom firmware request for $frequest");

	if (! file_exists("/opt/sark/www/" . $frequest)) {
		logit ("No available image for $frequest");
		send404();
		exit;
	} 
	logit ("Found image for $frequest - sending");
	
	header("Pragma: public");
	header("Expires: -1");
	header("Cache-Control: public, must-revalidate, post-check=0, pre-check=0");
	header('Content-Disposition: inline;');	
	header("Content-Type: application/octet-stream");
	
	$file = @fopen("/opt/sark/www/" . $frequest,"rb");
	while(!feof($file))	{
		print(@fread($file, 1024*8));
		ob_flush();
		flush();
	}	
}
/*
 * return the BLF table
 */
function getBlf() { 
	global $blfKeys;
	$keystring=Null;
	if (is_array($blfKeys)) {
		ksort($blfKeys);
		foreach ($blfKeys as $blf) {
			$keystring .= $blf . "\n";
		}
	}
	return $keystring;	 
}	  

/*
 * get the IP
 */	
function ret_localip() {
	
  global $haclusterip;
  global $hausecluster;
  global $local;
  global $externip;
  
  if (!$local) {
	  return $externip;
  }
	  

  if ($haclusterip) {
	if ( $hausecluster == "YES" ) {
	  return $haclusterip;
	}
  }
  
  $work = `/sbin/ifconfig eth0`;
  if (preg_match(" /inet addr:*?([\d.]+)/",$work,$matches)) {
    return $matches[1];
  }
  return -1;
}

function logIt($someText) {
  syslog(LOG_WARNING, $_SERVER["REMOTE_ADDR"] . " " . $someText . "\n");	
}

function send404() {
  header('HTTP/1.0 404 Not Found');
  echo "Not Found (404)";
}

function send200() {
  header('HTTP/1.0 200 OK');
  echo "OK";
}
                                                      
