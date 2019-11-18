<?php
include "settings.php";
include "commonFuncs.php";
include "/opt/sark/php/srkNetHelperClass";

global $haclusterip;
global $hausecluster;
global $externip;
global $fqdn;
global $fqdnprov;
global $ldapbase;
global $blfKeys;
global $local;
global $loglevel;


//global $masterkey;

$descriptor=false;
$retstring = NULL;
$mac=NULL;
$frequest=NULL;
$fname=NULL;
$local=True;


try {
  $db = new PDO("sqlite:$dBFullPath");
} catch (PDOException $e) {
  die("Failed to get DB handle: " . $e->getMessage() . "\n");
}
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// get the global env settings we need
try {	  
  	$global = $db->query("select EDOMAIN,FQDN,FQDNPROV,HACLUSTERIP,HAUSECLUSTER,LDAPBASE,LOGLEVEL,TLSPORT from globals")->fetch();

} catch (Exception $e) {
  $errorMsg = $e->getMessage();
  logIt("Unable to retrieve cluster values for $mac  - DB error $errorMsg");
  send404();
  exit(1);
}
if (empty($global)) {
  logIt("Unable to retrieve cluster values - DB may be locked");
  send404();
  exit(1);
}
$haclusterip = $global['HACLUSTERIP'];
$hausecluster = $global['HAUSECLUSTER'];
$externip = $global['EDOMAIN'];
$fqdnprov = $global['FQDNPROV'];
$fqdn = $global['FQDN'];
$ldapbase = $global['LDAPBASE'];
$loglevel = $global['LOGLEVEL'];
$tlsport = $global['TLSPORT'];


// ignore polycom logging requests
if (defined('STDIN')) {
    $mac = strtolower($argv[1]);
}
else {
	if ($_SERVER["REQUEST_METHOD"] == 'PUT') {
		logIt("Ignoring PUT request - Sending 200 and giving up");
		send200();
		exit;	
	}
	logit ("processing URI " . $_SERVER["REQUEST_URI"]);
// see if we have a mac in the GET
	if (isset($_GET['mac'])) {
		$mac = strtolower($_GET['mac']);
		logit ("Found MAC " . $mac);
		logUA();
	} 
	else {	// analyze what we got
		$uri_array = explode('/', $_SERVER["REQUEST_URI"]);
		if (isset($uri_array[2])) {
			$frequest = $uri_array[2];
// check for Yealink security file request and ignore it		
			if (preg_match('/^.*_Security\.enc$/',$frequest)) {
				logIt("Yealink security request for " . $_SERVER["REQUEST_URI"] . ", Ignored - sending 404");
				send404();
				exit;
			}
// check for Yealink boot file request and ignore it		
			if (preg_match('/\.boot$/',$frequest)) {
				logIt("Yealink bootfile request for " . $_SERVER["REQUEST_URI"] . ", Ignored - sending 404");
				send404();
				exit;
			}			
// check for polycom firmware request - no longer relevant - handled by mod_rewrite
			if (preg_match('/^.*\.sip\.ld$/',$frequest)) {
				polycomFirmware($frequest);
				exit;
			}						
// check for Yealink Y file
			if (preg_match('/^y000000(.*).cfg$/',$frequest)  ) {
				logit ("Found Yealink Y file  " . $frequest . " serving as common");
				$frequest = 'yealink.Common';
				$descriptor = true;
			}
/* check for vtech .cfg file
			else if (preg_match('/^VCS|VSP(.*).cfg$/',$frequest)  ) {
				logit ("Found Vtech cfg file  " . $frequest . " serving as common");
				$frequest = 'vtech.Common';
				$descriptor = true;	
			}
*/			
			else {
// try to harvest a MAC address
				if (preg_match('/([0-9A-Fa-f]{12})(.*)$/',$frequest,$matches)  ) {
					$mac = $matches[1];
					$fname = $matches[2];
					logit ("Found MAC " . $mac);
					logUA();
					// check for polycom zero config file
					if ($mac != '000000000000') {
					}
					else {
						$mac=$frequest;
						$descriptor=true;
					}
				}
				else {
					$descriptor=true;
// check for vtech .cfg file (vtech end all their requests with .cfg)
/*
					if (preg_match('/^VCS|VSP(.*).cfg$/',$frequest)  ) {
						logit ("Found Vtech cfg file  " . $frequest . " serving as common");
					$frequest = 'vtech.Common';					
					}
*/
				}
			}
		}
		else {
			logIt("no request file found in URI for " . $_SERVER["REQUEST_URI"] . " ,  Sending 404 and giving up");
			send404();
			exit;
		}
	}
}

// see if this mac/descriptor exists, quit if not


// check for polycom sub config
if (preg_match('/^0004[Ff]2/',$mac) && strlen($fname) > 4 ) {
	polycomSubConfig($mac,$fname,$db);
	exit;
}
// if we've got a descriptor then search the template by key  
if ($descriptor) {
	$macSearch = $db->prepare('select count(*) from Device where pkey = ?');
	$macSearch->execute(array($frequest));
}
else { // else search the template by MAC
	$macSearch = $db->prepare('select count(*) from IPphone where lower(macaddr) = ? COLLATE NOCASE');
	$macSearch->execute(array($mac));
}

$rowCount = $macSearch->fetchColumn();
$macSearch = NULL;
  
if ($rowCount == 1) {
//  logIt("$mac found once so sending config");
 } elseif ($rowCount == 0) {
   logIt("$frequest not found in db(RowCount).  Sending 404 and giving up");
   send404();
   exit;
   } elseif ($rowCount > 1) {
     logIt("$mac found in db $rowCount times, not really sure what to do");
     send404();
     exit("this mac found $rowCount times!");
     } elseif ($rowCount < 0) {
       logIt('Something has gone hideously wrong');
       die("What are you trying to do to me?");
       }

// if we are here then it's good to go (almost)

header('Content-type: text/plain');

// fetch the template
try {
  if ($descriptor) {
	$configs = $db->prepare('select pkey,provision from Device where pkey = ?'); 
	$configs->execute(array($frequest));
	$thisConfig = $configs->fetchObject();
	$configs = NULL;
  }
  else {
	$configs = $db->prepare('select pkey,provision,device,protocol,provisionwith,transport,devicemodel,desc,firstseen,lastseen,location,passwd,sndcreds from IPphone where lower(macaddr) = ? COLLATE NOCASE limit 1');
	$configs->execute(array($mac));
	$thisConfig = $configs->fetchObject();
	$configs = NULL;
// update the phone model (it may have changed or it may not be present yet)
  	$model = logUA();
  	if (!empty($model)) {
  		if ($model != $thisConfig->devicemodel) {
// set the model in the extension record  	
 			logIt("Device model differs between UA and DB.  UA = $model, DB = " . $thisConfig->devicemodel);
  	  		$sql = $db->prepare('UPDATE ipphone SET devicemodel=? WHERE pkey = ?');
			$sql->execute(array($model,$thisConfig->pkey));
			$sql = NULL;
		}
	}

// create and/or set the model in netphone record
/*
		$sql = $db->prepare('select * from netphone WHERE pkey = ?');
		$sql->execute(array($mac));
		$thisNetphone = $sql->fetchObject();
		If (!empty($thisNetphone->pkey)) {
			$sql = $db->prepare('UPDATE netphone SET model=? WHERE pkey = ?');
			$sql->execute(array($model,$mac));
		}
		else {
			$sql = $db->prepare('INSERT INTO netphone (pkey,model) VALUES(?,?)');
			$sql->execute(array($mac,$model));
		}
*/
  }	
   
//  $masterkey = $thisConfig->pkey;
} catch (Exception $e) {
  $errorMsg = $e->getMessage();
  logIt("Unable send mac file for $mac  - DB error $errorMsg");
  send404();
  exit;
}
// check if we are going to send creds
$sndcreds=false;
if (isset ($thisConfig->sndcreds)) {
	if (preg_match( '/(Always|Once)/',$thisConfig->sndcreds)) {
		$sndcreds=true;
	}
}


//create an empty loopcheck array 
$loopCheck = array();
$rawConfig = $thisConfig->provision . "\n";

// expand the template stack	
cleanConfig($thisConfig->pkey,$rawConfig,$db,$retstring,$loopcheck,$sndcreds);

// get ready to substitute into it from the values we have

if (isset($thisConfig->location) && $thisConfig->location == 'remote') {
	$local=false;
}

// substitute real values into the output	
if (preg_match('/\$localip/',$retstring)) {
	$retstring = preg_replace ( '/\$localip/', ret_localip($thisConfig->provisionwith, $thisConfig->protocol), $retstring); 
}
$retstring = preg_replace ( '/\$tlsport/', $tlsport, $retstring);
$retstring = preg_replace ( '/\$ldapbase/', $ldapbase, $retstring);

if (!$descriptor) {
	$retstring = preg_replace ( '/\$desc/', $thisConfig->desc, $retstring);
	$retstring = preg_replace ( '/\$password/', $thisConfig->passwd, $retstring);
	$retstring = preg_replace ( '/\$ext/', $thisConfig->pkey, $retstring);
// If its a Gigaset do their stupid double quoted desc field
	if (preg_match('/^7c2f80/',$mac) ) {
		$retstring = preg_replace ( '/\$quotedDesc/', '\'"' . $thisConfig->desc . '"\'', $retstring);
	}
}

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
	
// and, if we got this far,  finally ship it out

if ($frequest) {
	logIt("sending config $frequest");
}
else {
	logIt("sending config $mac$fname");
	logit ("endpoint to be provisioned using " . $thisConfig->provisionwith, $thisConfig->protocol);
}
$retstring = str_replace("\r", "", $retstring);
$pvarray = explode(PHP_EOL,$retstring);
logit ("====================provisioning stream================>",4);
foreach($pvarray as $row) {
	logit (trim($row),4);
}
logit ("====================End of stream======================>",4);
// send it
echo $retstring; 

// try to update lasteen
try {
	$time = time();
	if (isset($_SERVER["REMOTE_ADDR"])) {
    	if (empty($thisConfig->firstseen)) {
      		$sql = $db->prepare('UPDATE ipphone SET firstseen=?,lastseen=? WHERE pkey = ?');
			$sql->execute(array($time,$time,$thisConfig->pkey));
		}
		else {
    		$sql = $db->prepare('UPDATE ipphone SET lastseen=? WHERE pkey = ?');
			$sql->execute(array($time,$thisConfig->pkey));
		}
		$sql = NULL;
	}	

} catch (Exception $e) {
		$errorMsg = $e->getMessage();
  		logIt("Unable to update lastseen/firstseen - DB error $errorMsg");
  		send404();
  		exit(1);
}

// disable auth send for next time
if (!$descriptor) {
	if ($thisConfig->sndcreds != 'Always') { 
		try {
			$update = $db->prepare("update ipphone set sndcreds='No' where  pkey = '" . $thisConfig->pkey . "'");
			$update->execute();
			$update = NULL;
		} catch (Exception $e) {
			$errorMsg = $e->getMessage();
  			logIt("Unable to update extension sndcreds  - DB error $errorMsg");
  			exit(1);
		}
	}
}
$db=NULL;
/*
 * MAINLINE ENDS
 */
?>
