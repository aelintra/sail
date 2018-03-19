<?php
//
// Developed by CoCo
// Copyright (C) 2012 CoCoSoft
//
// This program is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// This program is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//


Class sarkpci {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
		
	echo '<form id="sarkpciForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'On-board PCI Cards';
	
	if (isset($_POST['start_x'])) { 
		$this->message = $this->sark_start();	
	}
	
	if (isset($_POST['stop_x'])) { 
		$this->message = $this->sark_stop();	
	}
	
	if (isset($_POST['save_x'])) { 
		$this->saveEdit();				
	}
	
	if (isset($_POST['initialise_x'])) { 
		$this->reGen();
		$this->message = "Regen Complete!";
	}	
	
	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
		$this->saveEdit();
		$this->commitLines();
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}		
		$this->helper->sysCommit();
		$this->message = "Updates have been Committed";	
	}
			
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	
	$astfiles = array();
	$tuple = array();	


/* 
 * start page output
 */
  
	echo '<div class="buttons">' . PHP_EOL;
	$this->myPanel->Button("initialise");
	$this->globalAction();
	echo '</div>' . PHP_EOL;
	echo '<br/>';	
	
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}

	$hardware = `/usr/sbin/dahdi_hardware`;

	if ( ! isset($hardware) ) {
		if ( ! file_exists("/etc/dahdi/system.conf" ) ) {
			$rc = $this->helper->request_syscmd ("/usr/sbin/dahdi_genconf");
		}
		else if ( ! file_exists("/etc/asterisk/dahdi-channels.conf" ) ) {
			$rc = $this->helper->request_syscmd ("/usr/sbin/dahdi_genconf");
		}
	}    
	$handle = fopen("/etc/dahdi/system.conf", "r") or die('Could not read system.conf file!');
	while (!feof($handle)) {
		$dahdi_system .= fgets($handle);
	}
	fclose($handle);
	$handle = fopen("/etc/asterisk/dahdi-channels.conf", "r") or die('Could not read dahdi-channels.conf file!');
	while (!feof($handle)) {
		$dahdi_channels .= fgets($handle);
	}
	fclose($handle);
	$handle = fopen("/etc/dahdi/genconf_parameters", "r") or die('Could not read genconf_parameters file!');
	while (!feof($handle)) {
		$genconf_parameters .= fgets($handle);
	}
	fclose($handle);
	
	
	echo '<br/>'. PHP_EOL;		
	echo '<div id="pagetabs">' . PHP_EOL;	
    echo '<ul>'.  PHP_EOL;
    echo '<li><a href="#hardware">Hardware</a></li>'.PHP_EOL;
    echo '<li><a href="#system" >system.conf</a></li>'. PHP_EOL;
    echo '<li><a href="#channels" >channels.conf</a></li>'. PHP_EOL;
    echo '<li><a href="#params" >parameters</a></li>'. PHP_EOL;
    echo '</ul>'. PHP_EOL;

/*
 *  	Tab Hardware
 */
 
	echo '<div id="hardware">'. PHP_EOL;
    if ( ! isset($hardware) ) {
		$hardware = "No Asterisk hardware detected";
    }

    echo '<span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">Hardware detected by dahdi_genconf'. '</span><br/>' . PHP_EOL;
	echo '<br/>' . PHP_EOL;
	echo '<textarea rows="6" cols="100" name="hardware" id="hardware" readonly = "readonly" >' . $hardware . '</textarea>' . PHP_EOL;
 

/*
 *      TAB DIVEND
 */
    echo "</div>". PHP_EOL;


/*
 *       TAB system
 */

    echo '<div id="system">'. PHP_EOL;
    echo '<span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">/etc/dahdi/system.conf'. '</span><br/>' . PHP_EOL;
	echo '<br/>' . PHP_EOL;
	echo '<textarea rows="17" cols="100" name="dahdi_system" id="dahdi_system" >' . $dahdi_system . '</textarea>' . PHP_EOL;   
    

/*
 *       TAB DIVEND
 */
    echo "</div>". PHP_EOL;


/*
 *       TAB channels
 */
    echo '<div id="channels">'. PHP_EOL;
    echo '<span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">/etc/asterisk/dahdi-channels.conf'. '</span><br/>' . PHP_EOL;
	echo '<br/>' . PHP_EOL;
	echo '<textarea rows="17" cols="100" name="dahdi_channels" id="dahdi_channels" >' . $dahdi_channels . '</textarea>' . PHP_EOL;   
/*
 *       TAB DIVEND
 */
    echo "</div>". PHP_EOL;



/*
 *       TAB Params
 */
    echo '<div  id="params">'. PHP_EOL;
    echo '<span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">/etc/dahdi/genconf_parameters'. '</span><br/>' . PHP_EOL;
	echo '<br/>' . PHP_EOL;
	echo '<textarea rows="17" cols="100" name="genconf_parameters" id="genconf_parameters" >' . $genconf_parameters . '</textarea>' . PHP_EOL;   
#
#       TAB DIVEND
#
    echo '</div>' . PHP_EOL;

#
#  end of TABS DIV
#
    echo '</div>' . PHP_EOL;
}

private function saveEdit() {
// save the data away

	$tuple = array();
	
	$dahdi_system = $_POST['dahdi_system'];
    $dahdi_channels = $_POST['dahdi_channels'];
    $genconf_parameters = $_POST['genconf_parameters'];
    
    $dahdi_system = preg_replace ( "/\\\/", '', $dahdi_system);
	$dahdi_channels = preg_replace ( "/\\\/", '', $dahdi_channels);
	$genconf_parameters = preg_replace ( "/\\\/", '', $genconf_parameters);	
/*		
	$dahdi_system = strip_tags($_POST['dahdi_system']);
    $dahdi_channels = strip_tags($_POST['dahdi_channels']);
    $genconf_parameters = strip_tags($_POST['genconf_parameters']);
    
    $dahdi_system = preg_replace ( "/\\\/", '', $dahdi_system);
	$dahdi_channels = preg_replace ( "/\\\/", '', $dahdi_channels);
	$genconf_parameters = preg_replace ( "/\\\/", '', $genconf_parameters);
*/
/*
 * set correct permissions and ownership on the Asterisk files
 */
 
	$rc = $this->helper->request_syscmd ("/bin/chown asterisk:asterisk /etc/dahdi/*");
	$rc = $this->helper->request_syscmd ("/bin/chown asterisk:asterisk /etc/asterisk/*");
	$rc = $this->helper->request_syscmd ("/bin/chmod 664 /etc/dahdi/*");
	$rc = $this->helper->request_syscmd ("/bin/chmod 664 /etc/asterisk/*");
	
	
	$fh = fopen("/etc/dahdi/system.conf", 'wb') or die('Could not open system.conf!');
	fwrite($fh,$dahdi_system) or die('Could not write to system.conf');
	fclose($fh);
	
	$fh = fopen("/etc/asterisk/dahdi-channels.conf", 'wb') or die('Could not open dahdi-channels.conf!');
	fwrite($fh,$dahdi_channels) or die('Could not write to dahdi-channels.conf');
	fclose($fh);
	
	$fh = fopen("/etc/dahdi/genconf_parameters", 'wb') or die('Could not open genconf_parameters!');
	fwrite($fh,$genconf_parameters) or die('Could not write to genconf_parameters');
	fclose($fh);		

# clean up the files

	$rc = $this->helper->request_syscmd ("/bin/sed -i '/^\s*$/d' /etc/dahdi/system.conf");
	$this->helper->request_syscmd ("/usr/bin/dos2unix /etc/dahdi/system.conf");
	$this->helper->request_syscmd ("/usr/bin/dos2unix /etc/asterisk/dahdi-channels.conf");
	$this->helper->request_syscmd ("/usr/bin/dos2unix /etc/dahdi/genconf_parameters");
    $this->setDahdiPerms();	
	$this->message = "Updated Dahdi files!";
		
}

private function sark_stop () {

    if ( $warp ) {
    	$ret = ($this->helper->request_syscmd ('/bin/asterisk -rx "stop now"'));
    }
    else {  
    	$ret = ($this->helper->request_syscmd ('/usr/bin/sv d sark'));
    }
	return ("Stop signal sent");

}

private function sark_start () {

    if ( $warp ) {
       $ret = ($this->helper->request_syscmd ("/persistent/autorun/S98asterisk"));
    }
    else { 
		$ret = ($this->helper->request_syscmd ('/usr/bin/sv u sark'));    
    }
	return ("Start signal sent");	
}


private function globalAction () {

    $this->myPanel->Button("save");
    $this->myPanel->commitButton();
/*    
    if ( ! $this->check_pid() ) {
		if ( ! $this->check_hapid() ) {
			$this->myPanel->Button("start");
       }
    }
    else {
		$this->myPanel->Button("stop");
    }

    if ( file_exists ("/etc/init.d/heartbeat" )) {
    	if ( $this->check_hapid() ) {
        	$this->myPanel->Button("ha-stop");
    	}
        else {
        	$this->myPanel->Button("ha-start");
		}
    }
*/
}
private function check_pid()  {
    if ( $warp ) {
    	if  (`/bin/ps -e | /bin/grep '/bin/asterisk' | /bin/grep -v grep`) {
    		return (true);
    	}
    }
    else {
    	if  (`/bin/ps -e | /bin/grep asterisk | /bin/grep -v grep`) {
    		return(true);
    	}
    }
	return (false);
}

private function check_hapid() {
	if  (`/bin/ps -e | /bin/grep heartbeat | /bin/grep -v grep`) {
    	return(true);
    }
	return (false);
}

private function check_hapid_installed () {
    if  ( file_exists ("/usr/lib/heartbeat/heartbeat") ) {
    	return(true);
    }
	return (false);
}  

private function commitLines () {

// set perms/ownership for us

    $this->setDahdiPerms();
    
// initialise the db
	$this->helper->predDelTuple("lineIO","carrier","DAHDIGroup");
	$this->helper->predDelTuple("IPphone","technology","Analogue");	
    $tuple = array(
		'pkey' => 'global',
		'NUMGROUPS' => 0
	); 
    $ret = $this->helper->setTuple('globals',$tuple);

// check the extension lengths        
    $res = $this->dbh->query("SELECT EXTLEN FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
    $extlen = $res['EXTLEN'];
    
    $dahdi_array = array();
	$handle = fopen("/etc/asterisk/dahdi-channels.conf", "r") or die('Could not read file!');	
	while (!feof($handle)) {
		array_push ( $dahdi_array,fgets($handle) );
	}
	fclose($handle);
	 
    $channel = false;
    $extension = false;
    $signalling = false;
 
//      look for groups and analogue extensions.

	foreach ( $dahdi_array as $line )   {
		if ( preg_match ( ' /group=(\d{1,2})/ ',$line,$matches )) {
			if ($matches[1] != 1) {
				$this->makegroup($matches[1]);
			}
       	}
        if (  preg_match ( ' /^signalling/ ',$line)) {
            if ( preg_match ( ' /fxo_ks/ ',$line )) {
               	$signalling = True;
				$channel = false;
                $extension = false;
            }
            else {
               	$signalling = false;
            }
		}
        if (  preg_match ( ' /<(\d{3,4})>$/ ',$line,$matches )) {
			$extension = $matches[1];
			if ( strlen($extension) != $extlen ) {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['extlen'] = "ERROR! - generated extension $extension (in channels) is incorrect length - system is set  " . $extlen . " digit extens";
                break;
            }
        }
        if (  preg_match ( ' /^\s*channel\s*=>?\s*(\d{1,})/ ',$line,$matches ))  {
            $channel = $matches[1];
        }
        if ($signalling && $channel && $extension) {
            $ret = $this->makephone($channel,'Analogue','AnalogFXS',$extension);
            if ( $ret != 'OK' ) {
				break;
			}
            $channel = false;
            $extension = false;
            $signalling = false;
        }
    }
}

private function makephone ($channel,$technology,$device,$exten) {
	$ret = false;
	$tuple = array(
		'pkey' => $exten,
		'cluster' => 'default',
		'desc' => 'Channel ' . $channel,
		'technology' => $technology,
		'device' => $device,
		'channel' => $channel,
		'location' => 'RJ11'
	);
	$ret = $this->helper->createTuple("ipphone",$tuple);
	if ( $ret != 'OK' ) {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash['ext'] = $ret;
    }
    return ($ret);
}

private function makegroup ($group) {

	$pkey = "DAHDIGroup".$group;
    $res = $this->dbh->query("SELECT NUMGROUPS FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
    $numgroups = $res['NUMGROUPS'];
    
    $tuple = array(
		'pkey' => $pkey,
		'carrier' => 'DAHDIGroup',
		'active' => 'YES',
		'desc' => 'DAHDI Group channel',
		'technology' => 'group',
		'predial' => 'DAHDI/g' . $group
	);
	
	$ret = $this->helper->createTuple("lineio",$tuple);
	
	
    $numgroups++;
    unset($tuple);
    $tuple = array(
		'pkey' => 'global',
		'NUMGROUPS' => $numgroups
	);    
    $ret = $this->helper->setTuple('globals',$tuple);  
      
    return ($group);
}

private function reGen () {

	$this->helper->predDelTuple("lineIO","carrier","DAHDIGroup");
	$this->helper->predDelTuple("IPphone","technology","Analogue");	
    $tuple = array(
		'pkey' => 'global',
		'NUMGROUPS' => 0
	); 
    $ret = $this->helper->setTuple('globals',$tuple);
    $this->helper->request_syscmd ("/bin/cat /dev/null > /etc/dahdi/system.conf");
	$this->helper->request_syscmd ("/bin/cat /dev/null > /etc/asterisk/dahdi-channels.conf");
	$this->helper->request_syscmd ("/usr/sbin/dahdi_genconf");
	$this->setDahdiPerms();   	
}

private function setDahdiPerms() {
	$this->helper->request_syscmd ("chown asterisk:asterisk /etc/dahdi/system.conf");
    $this->helper->request_syscmd ("chown asterisk:asterisk /etc/asterisk/dahdi-channels.conf");
    $this->helper->request_syscmd ("chown asterisk:asterisk /etc/dahdi/genconf_parameters");
	$this->helper->request_syscmd ("chmod 664 /etc/dahdi/system.conf");
    $this->helper->request_syscmd ("chmod 664 /etc/asterisk/dahdi-channels.conf");
    $this->helper->request_syscmd ("chmod 664 /etc/dahdi/genconf_parameters"); 
}
}
