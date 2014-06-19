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
require_once "../srkPageClass";
require_once "../srkDbClass";
require_once "../srkHelperClass";
require_once "../formvalidator.php";


Class globals {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $distro = array(); 
	protected $HA;

	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->helper->qDistro($this->distro);
	
		
	echo '<body>';
	echo '<form id="sarkglobalForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Globals';
	
	if (isset($_POST['start_x'])) { 
		$this->message = $this->sark_start();	
	}
	
	if (isset($_POST['stop_x'])) { 
		$this->message = $this->sark_stop();	
	}
	
	if (isset($_POST['save_x'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}					
	}
	
	if (isset($_POST['reboot_x'])) { 
//		$this->saveEdit();
		if ( ! $this->invalidForm) {
			$this->helper->request_syscmd ("reboot");
			$this->message = "Rebooting Now, Please wait...";
		}
	}		
	
	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
		$this->saveEdit();
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
	$warp = false;
	$warpsdcard = false;
	$extensions = false;
	$res = $this->dbh->query('select count(*) from ipphone')->fetchColumn(); 
	
	if ($res > 0) {
		$extensions = true;
	} 
	
	$global = $this->dbh->query("SELECT * FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);

/* 
 * start page output
 */
  
	echo '<div class="buttons">' . PHP_EOL;
	$this->globalAction($global);
	echo '</div>' . PHP_EOL;	
	
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
/*
 * get countrycode from indications.conf
 */ 
	$indications = array();
	$country = array();
	$handle = fopen("/etc/asterisk/indications.conf", "r") or die('Could not read file!');
	while (!feof($handle)) {
		array_push ($indications, fgets($handle));
	}
	fclose($handle);
	foreach ($indications as $ccode) {
		if (preg_match(' /\[(\w\w)\]/ ',$ccode,$matches)) {
			array_push ($country, $matches[1]);
		}
	}

	
		
/*
 * read the global row and display it 
 */ 

	
	
	if ( !$global['PWDLEN'] ) {
		$global['PWDLEN'] = 8;
	}
	if ( !isset ($global['VMAILAGE']) ) {
		$global['VMAILAGE'] = 60;
	}	
	
	echo '<div class="datadivtabedit">';		

	echo '<div id="pagetabs"  >' . PHP_EOL;
	
    echo '<ul>'.  PHP_EOL;
    echo '<li><a href="#system">General</a></li>'. PHP_EOL;
    echo '<li><a href="#services" >Services</a></li>'.  PHP_EOL;
    echo '<li><a href="#control" >Call-Control</a></li>'. PHP_EOL;
    echo '<li><a href="#ldap" >LDAP</a></li>'. PHP_EOL;
    echo '<li><a href="#sysadmin" >Admin</a></li>'. PHP_EOL;
    echo '</ul>'. PHP_EOL;

/*
 *  Print System status box
 */
	$this->printSysNotes();
 
	echo '<div id="sysadmin" >'. PHP_EOL;

	if ( ! $warp ) {
		$this->myPanel->aLabelFor('astdlim');
		$this->myPanel->selected = $global['ASTDLIM'];
		$this->myPanel->popUp('ASTDLIM', array(',','|'));
    }
    $this->myPanel->aLabelFor('emergency');
    echo '<input type="text" name="EMERGENCY" id="EMERGENCY" size="12"  value="' . $global['EMERGENCY'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('pwdlen');
    echo '<input type="text" name="PWDLEN" id="PWDLEN" size="2"  value="' . $global['PWDLEN'] . '"  />' . PHP_EOL;    
    $this->myPanel->aLabelFor('syspass');
    echo '<input type="text" name="SYSPASS" id="SYSPASS" size="4"  value="' . $global['SYSPASS'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('spypass');
    echo '<input type="text" name="SPYPASS" id="SPYPASS" size="4"  value="' . $global['SPYPASS'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('supemail');
    echo '<input type="text" name="SUPEMAIL" id="SUPEMAIL" size="20"  value="' . $global['SUPEMAIL'] . '"  />' . PHP_EOL;

/*
 *      TAB DIVEND
 */
    echo "</div>". PHP_EOL;

/*
 *       TAB Control
 */
    echo '<div id="control" >'. PHP_EOL;
    
    $this->myPanel->aLabelFor('lterm');
    $this->myPanel->selected = $global['LTERM'];
    $this->myPanel->popUp('LTERM', array('YES','NO'));      
    $this->myPanel->aLabelFor('playcongested');
    $this->myPanel->selected = $global['PLAYCONGESTED'];
    $this->myPanel->popUp('PLAYCONGESTED', array('YES','NO','SIGNAL'));    
    $this->myPanel->aLabelFor('playbusy');
    $this->myPanel->selected = $global['PLAYBUSY'];
    $this->myPanel->popUp('PLAYBUSY', array('YES','NO','SIGNAL'));    
    $this->myPanel->aLabelFor('playbeep');
    $this->myPanel->selected = $global['PLAYBEEP'];
    $this->myPanel->popUp('PLAYBEEP', array('YES','NO'));    
    $this->myPanel->aLabelFor('intringdelay');
    echo '<input type="text" name="INTRINGDELAY" id="INTRINGDELAY" size="4"  value="' . $global['INTRINGDELAY'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('abstimeout');    
    echo '<input type="text" name="ABSTIMEOUT" id="ABSTIMEOUT" size="4"  value="' . $global['ABSTIMEOUT'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('voipmax');    
    echo '<input type="text" name="VOIPMAX" id="VOIPMAX" size="4"  value="' . $global['VOIPMAX'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('vringdelay');
    $this->myPanel->selected = $global['RINGDELAY'];
    $this->myPanel->popUp('RINGDELAY', array('0','1','2','3','4','5','6','7','8','9','10'));    
    $this->myPanel->aLabelFor('cfwdanswer');
    $this->myPanel->selected = $global['CFWDANSWER'];
    $this->myPanel->popUp('CFWDANSWER', array('enabled','disabled'));
    $this->myPanel->aLabelFor('cfwdextrnrule');
    $this->myPanel->selected = $global['CFWDEXTRNRULE'];
    $this->myPanel->popUp('CFWDEXTRNRULE', array('enabled','disabled'));
    $this->myPanel->aLabelFor('cfwdprogress');
    $this->myPanel->selected = $global['CFWDPROGRESS'];
    $this->myPanel->popUp('CFWDPROGRESS', array('enabled','disabled'));                  	                	                    	
    $this->myPanel->aLabelFor('fax');      
	$this->myPanel->selected = $global['FAX'];
	$this->myPanel->sysSelect('SYSOP',true,true) . PHP_EOL; 	   
    $this->myPanel->aLabelFor('faxdetect');
    $this->myPanel->selected = $global['FAXDETECT'];
    $this->myPanel->popUp('FAXDETECT', array('0','1','2','3','4','5','6','7','8','9'));  

/*
 *       TAB DIVEND
 */
    echo "</div>". PHP_EOL;


/*
 *       TAB Services
 */
    echo '<div id="services" >'. PHP_EOL;
/*
 *  TFTP gone in 4.0.1    
 *    $this->myPanel->aLabelFor('tftp');
 *    $this->myPanel->selected = $global['TFTP'];
 *   $this->myPanel->popUp('TFTP', array('enabled','disabled'));
 */ 
    $this->myPanel->aLabelFor('sipmulticast');
    $this->myPanel->selected = $global['SIPMULTICAST'];
    $this->myPanel->popUp('SIPMULTICAST', array('enabled','disabled'));     
    $this->myPanel->aLabelFor('ztp');
    $this->myPanel->selected = $global['ZTP'];
    $this->myPanel->popUp('ZTP', array('enabled','disabled'));  
	if (preg_match( '/$Apache/',$_SERVER{'SERVER_SOFTWARE'})) {
	    $this->myPanel->aLabelFor('proxy');
		$this->myPanel->selected = $global['PROXY'];
		$this->myPanel->popUp('PROXY', array('NO','YES'));
    }
    $this->myPanel->aLabelFor('cosstart');
    $this->myPanel->selected = $global['COSSTART'];
    $this->myPanel->popUp('COSSTART', array('OFF','ON'));
    $this->myPanel->aLabelFor('clusterstart');
    $this->myPanel->selected = $global['CLUSTER'];
    $this->myPanel->popUp('CLUSTER', array('OFF','ON'));
    $this->myPanel->aLabelFor('bouncealert');    
    echo '<input type="text" name="BOUNCEALERT" id="BOUNCEALERT" size="20"  value="' . $global['BOUNCEALERT'] . '"  />' . PHP_EOL; 

	$this->myPanel->aLabelFor('blindbusy');
	$this->myPanel->selected = $global['BLINDBUSY'];
	$this->myPanel->sysSelect('BLINDBUSY') . PHP_EOL;
    $this->myPanel->aLabelFor('camponqonoff');
    $this->myPanel->selected = $global['CAMPONQONOFF'];
    $this->myPanel->popUp('CAMPONQONOFF', array('OFF','ON'));
    $this->myPanel->aLabelFor('camponqopt');    
    echo '<input type="text" name="CAMPONQOPT" id="CAMPONQOPT" size="5"  value="' . $global['CAMPONQOPT'] . '"  />' . PHP_EOL; 

/*
 *       TAB DIVEND
 */
    echo "</div>". PHP_EOL;

/*
 *       TAB System  style="background:#cccccc;"
 */
    echo '<div  id="system" >'. PHP_EOL;

    $this->myPanel->aLabelFor('edomain');    
    echo '<input type="text" name="EDOMAIN" id="EDOMAIN" size="16"  value="' . $global['EDOMAIN'] . '"  />' . PHP_EOL; 
// NB 
    $this->myPanel->aLabelFor('countrycode');
    $this->myPanel->selected = $global['COUNTRYCODE'];
    $this->myPanel->popUp('COUNTRYCODE', $country);
    
    if ( $warp ) {
		$this->myPanel->aLabelFor('linuxtz');    
		echo '<input type="text" name="LINUXTZ" id="LINUXTZ" size="16"  value="' . $global['LINUXTZ'] . '"  />' . PHP_EOL; 
	}
    $this->myPanel->aLabelFor('voiceinstr');
    $this->myPanel->selected = $global['VOICEINSTR'];
    $this->myPanel->popUp('VOICEINSTR', array('YES','NO'));  
    
    $this->myPanel->aLabelFor('vmailage');
    $this->myPanel->selected = $global['VMAILAGE'];
    echo '<input type="text" name="VMAILAGE" id="VMAILAGE" size="3"  value="' . $global['VMAILAGE'] . '"  />' . PHP_EOL;       	   
 
	if ( ! $warp ) {
		$this->myPanel->aLabelFor('conftype');
		$this->myPanel->selected = $global['CONFTYPE'];
		$this->myPanel->popUp('CONFTYPE', array('simple','hosted'));
	}
	$this->myPanel->aLabelFor('extlen');
	if ( ! $extensions ) {		
		$this->myPanel->selected = $global['EXTLEN'];
		$this->myPanel->popUp('EXTLEN', array('3','4')); 
	} 	
    else {
		echo '<input type="text" name="EXTLEN" size="1" style = "background-color: lightgrey" readonly="readonly" id="EXTLEN" value="' . $global['EXTLEN'] . '"  />' . PHP_EOL;
    }
    $this->myPanel->aLabelFor('acl');
    $this->myPanel->selected = $global['ACL'];
    $this->myPanel->popUp('ACL', array('YES','NO'));  
    $this->myPanel->aLabelFor('sipiaxstart');    
    echo '<input type="text" name="SIPIAXSTART" id="SIPIAXSTART" size="4"  value="' . $global['SIPIAXSTART'] . '"  />' . PHP_EOL;             
    $this->myPanel->aLabelFor('agentstart');    
    echo '<input type="text" name="AGENTSTART" id="AGENTSTART" size="4"  value="' . $global['AGENTSTART'] . '"  />' . PHP_EOL;              
                	
	if ($warp) {
		if ($warpsdcard) {
			$this->myPanel->aLabelFor('callrecord1');
			$this->myPanel->selected = $global['CALLRECORD1'];
			$this->myPanel->popUp('CALLRECORD1', array('None','OTR','OTRR','Inbound','Outbound','Both')); 
		}
	}				
    else {
		$this->myPanel->aLabelFor('callrecord1');
		$this->myPanel->selected = $global['CALLRECORD1'];
		$this->myPanel->popUp('CALLRECORD1', array('None','OTR','OTRR','Inbound','Outbound','Both')); 				
    }		  
    $this->myPanel->aLabelFor('operator');    
    echo '<input type="text" name="OPERATOR" id="OPERATOR" size="4"  value="' . $global['OPERATOR'] . '"  />' . PHP_EOL;                   	        		

	$this->myPanel->aLabelFor('sysop');
	$this->myPanel->selected = $global['SYSOP'];
	$this->myPanel->sysSelect('SYSOP',false,true) . PHP_EOL;

#
#       TAB DIVEND
#
    echo '</div>' . PHP_EOL;
    
#
#  LDAP tab
#
	echo '<div id="ldap" >'. PHP_EOL;
   
    $this->myPanel->aLabelFor('ldapbase');
    echo '<input type="text" name="ldapbase" id="ldapbase" size="32"  value="' . $global['LDAPBASE'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('ldapou');
    echo '<input type="text" name="ldapou" id="ldapou" size="32"  value="' . $global['LDAPOU'] . '"  />' . PHP_EOL;    
    $this->myPanel->aLabelFor('ldapuser');
    echo '<input type="text" name="ldapuser" id="ldapuser" size="12"  value="' . $global['LDAPUSER'] . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('ldappass');
    echo '<input type="password" name="ldappass" id="ldappass" size="12"  value="' . $global['LDAPPASS'] . '"  />' . PHP_EOL;

/*
 *      TAB DIVEND
 */
    echo "</div>". PHP_EOL;

#
#  end of TABS DIV
#
    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;
}


private function saveEdit() {
// save the data away

	$tuple = array();

	$this->validator = new FormValidator();
	
	$this->validator->addValidation("PWDLEN","num","Ext password length must be numeric"); 
	$this->validator->addValidation("PWDLEN","lt=19","Ext password length must be 18 or less");
	$this->validator->addValidation("PWDLEN","gt=5","Ext password length must be 6 or more"); 
	$this->validator->addValidation("SYSPASS","num","Keyops password must be numeric"); 
	$this->validator->addValidation("SYSPASS","maxlen=4","Keyops must be 4 digits"); 
	$this->validator->addValidation("SYSPASS","minlen=4","Keyops must be 4 digits");
	$this->validator->addValidation("SPYPASS","num","Spy password must be numeric");
	$this->validator->addValidation("SPYPASS","maxlen=4","Spy password must be 4 digits"); 
	$this->validator->addValidation("SPYPASS","minlen=4","Spy password must be 4 digits");		
    $this->validator->addValidation("SUPEMAIL","email","Supervisor email is malformed");    
    $this->validator->addValidation("INTRINGDELAY","req","Ringtime must be entered"); 
    $this->validator->addValidation("INTRINGDELAY","num","Ringtime must be numeric");
    $this->validator->addValidation("INTRINGDELAY","maxlen=3","Ringtime must be 3 digits or less");     
    $this->validator->addValidation("ABSTIMEOUT","req","Call timeout must be entered");
    $this->validator->addValidation("ABSTIMEOUT","num","Call timeout must be numeric");    
    $this->validator->addValidation("VOIPMAX","req","Max Outbound VOIP Calls must be entered");
    $this->validator->addValidation("VOIPMAX","num","Max Outbound VOIP Calls must be numeric");    
    $this->validator->addValidation("EXTLEN","req","Extension Length must be entered");
    $this->validator->addValidation("EXTLEN","num","Extension Length must be numeric");
    $this->validator->addValidation("EXTLEN","gt=2","Extension Length must 3 or 4");
    $this->validator->addValidation("EXTLEN","lt=5","Extension Length must 3 or 4");    
    $this->validator->addValidation("SIPIAXSTART","req","Extension start must be entered");
    $this->validator->addValidation("SIPIAXSTART","num","Extension start must be numeric");
    $this->validator->addValidation("SIPIAXSTART","maxlen=4","Extension start must be 3 or 4 digits (to match extension length)"); 
	$this->validator->addValidation("SIPIAXSTART","minlen=3","Extension start must be 3 or 4 digits (to match extension length)");	   
    $this->validator->addValidation("AGENTSTART","num","Agent start must be numeric");
    $this->validator->addValidation("AGENTSTART","maxlen=4","Agent start must be 4 digits");
    $this->validator->addValidation("AGENTSTART","minlen=4","Agent start must be 4 digits");    
    $this->validator->addValidation("OPERATOR","num","Operator must be numeric");    
    $this->validator->addValidation("EDOMAIN",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"External IP address is invalid");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  
		$custom = array ('LINUXTZ' => True);		
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
		$tuple['pkey'] = 'global';
/*
 * call the setter
 */ 
		$ret = $this->helper->setTuple("globals",$tuple);
/*
 * flag errors
 */ 	
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->goodEditOutcome($tuple);
			$this->message = "Updated Globals!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash[speed] = $ret;	
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function goodEditOutcome($tuple) {
/* 
 * do all of the ancillary stuff that is necessary after a globals update
 */ 

/*
 * update the countrycode in indications
 */ 
	$rc = $this->helper->request_syscmd ("/bin/sed -i 's/country=\\w\\w/country=" . $tuple['COUNTRYCODE'] . "/' /etc/asterisk/indications.conf");
/*
 * 	start/stop multicast listener
 */
		
	if ($tuple['SIPMULTICAST'] == "enabled" ) {
		$this->helper->request_syscmd ( "/usr/bin/sv u  srk-ua-responder" );	
		if ( $this->distro['rhel']  ) {					
			$this->helper->request_syscmd ( "/bin/rm -f  /service/srk-ua-responder/down" ); 
		}
		if ( $this->distro['debian'] ) {
			$this->helper->request_syscmd ( "/bin/rm -f  /etc/service/srk-ua-responder/down" ); 
		}
	}			
	else {
		$this->helper->request_syscmd ( "/usr/bin/sv d  srk-ua-responder" );
		if ( $this->distro['rhel'] ) {							
			$this->helper->request_syscmd ( "/bin/touch  /service/srk-ua-responder/down" );
		}
		if ( $this->distro['debian'] ) {
			$this->helper->request_syscmd ( "/bin/touch  /etc/service/srk-ua-responder/down" );
		}							
    }
    
/*
 * No TFTP Here anymore 
 *  
	if ($tuple['TFTP'] == "enabled") {		
		if ( $this->distro['debian'] ) {
			$rc =($this->helper->request_syscmd ("/etc/init.d/xinetd start"));
		}
		if ($this->distro['sme']) {
			$rc =($this->helper->request_syscmd ("/etc/init.d/tftpd start"));
		}		
    }
	if ($tuple['TFTP'] == "disabled")  {
		if ( $this->distro['debian'] ) {
			$rc =($this->helper->request_syscmd ("/etc/init.d/xinetd stop"));
		}
		if ($this->distro['sme']) {
			$rc =($this->helper->request_syscmd ("/etc/init.d/tftpd stop"));
		}		
    }
*/   
    $this->helper->request_syscmd ( "/usr/bin/sv k  srk-ua-responder" );
}

private function printSysNotes () {
#
#   prints sysinfo Tab
#
    echo '<div  class="notes">' . PHP_EOL;
    echo '<p class="last">' . PHP_EOL;


    $localip = $this->helper->ret_localip ();
	$updays=false;
	$commip=NULL;
	$virtualip=NULL;
	
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		$systemName		=  gethostname();
	}
	else {
		$systemName = php_uname('n');
	}

//    $rlse  = `/bin/cat /opt/sark/.sail-release`;
    $free =  array();
    $free = `/usr/bin/free`;
    $uptime = `/usr/bin/uptime`;
    if (preg_match( ' /up\s(\d+\sdays)/ ', $uptime,$matches)) {
		$updays = $matches[1];
	}
	$disk = array();
    $disk = `/bin/df -h`;
    $diskusage = preg_match ( '/(\d{1,2}\%)/', $disk,$matches);
    $diskusage = $matches[1];
    $systemMedia = 'disk';
	if (preg_match( '/Mem:\s+(\d{1,7})\s+(\d{1,7})\s+(\d{1,7})\s+(\d{1,7})\s+(\d{1,7})/',$free,$matches )) {
			$totmem = $matches[1];
			$usedmem = $matches[2];
			$freemem = $matches[3];
			$sharedmem = $matches[4];
			$buffers = $matches[5];
    } 
    $mac = strtoupper(`ip link show eth0 | awk '/ether/ {print $2}'`);  
    	
    if ( $this->helper->check_pid() ) {
        $runstate = "RUNNING";
    }
    else {
        $runstate = "STOPPED";
    }
    if ( file_exists ("/etc/corosync/corosync.conf")) { 
		if  (`/bin/ps -e | /bin/grep corosync | /bin/grep -v grep`) {
        	$harunstate = "RUNNING";
        	$work = `/sbin/ip addr show eth0 | grep secondary`;    
			if (preg_match(" /inet *?([\d.]+)/",$work,$matches)) { 
				$virtualip = $matches[1]; 	 		
			}
			$work = `/sbin/ip addr show eth1 | grep inet`;
			if (preg_match(" /inet *?([\d.]+)/",$work,$matches)) { 
				$commip = $matches[1]; 	 		
			}			
    	}
		else {
        	$harunstate = "STOPPED";
    	}
    }
	
    echo '<span style="color: #696969;" >';
    echo '<span style="font-weight:bold; font-size:small; ">Sysinfo</span><br />';
//    $sno = $this->rets();
    
    $rlse=''; 
    if ( $this->distro['rhel']  ) {	
		$rlse = `/bin/rpm -q sail`;
	}
	else {
		$rlse = `dpkg-query -W -f '\${version}\n' sail`;
	}
	echo "Distro: <strong>" . $this->distro['name'] . "</strong><br/>";
    echo "SAIL Release: <strong>$rlse</strong><br/>";
    if ( $this->distro['debian'] ) {
		$rlse = `dpkg-query -W -f '\${version}\n' sailhpe`;
		echo "HPE Release: <strong>$rlse</strong><br/>";
	}
		    
    preg_match ( '/^(\w*)\b/', $_SERVER['SERVER_SOFTWARE'], $matches);
    $server = $matches[1];        
    echo "Web Server: <strong>$server</strong><br/>";
    
    if (preg_match ( ' /(MSIE)/ ',$_SERVER['HTTP_USER_AGENT'],$matches) ||
		preg_match ( ' /(Chrome)/ ',$_SERVER['HTTP_USER_AGENT'],$matches) ||
		preg_match ( ' /(Firefox)/ ',$_SERVER['HTTP_USER_AGENT'],$matches) ||
		preg_match ( ' /(Safari)/ ',$_SERVER['HTTP_USER_AGENT'],$matches)) {
		if ($matches[1]) {
			print "Browser: <strong>$matches[1]</strong><br/>";
		}
		else {
			print "Browser: <strong>Unknown</strong><br/>";
		}
	}
	

    echo '<span style="font-weight:bold; font-size:small; ">Network</span><br />';
    echo "MAC: <strong>$mac</strong><br/>";
    echo "hostname: <strong>$systemName</strong><br/>";
    if ( $localip ) {
		echo "LAN IP: <strong>$localip</strong><br/>";
	}
	
    if ( $virtualip ) {
        echo "Virtual IP: <strong>$virtualip</strong><br/>";
	}
	if ( $commip ) {
		echo "Comms IP: <strong>$commip</strong><br/>";
	}
//    print "Netmask: <strong>$snmask</strong><br/>";


    echo '<span style="font-weight:bold; font-size:small; ">Resource</span><br />';
    
    echo "System Media: <strong>$systemMedia</strong><br/>";
    
    echo "Disk Usage: <strong>$diskusage</strong><br/>";

    echo "RAM Size: <strong>$totmem</strong><br/>";
    echo "RAM Free: <strong>$freemem</strong><br/>";

    echo '<span style="font-weight:bold; font-size:small; ">Status</span><br />';
    echo "PBX State: <strong>$runstate</strong><br/>";
    
    echo "SysTime: <strong>" . `date '+%H:%M:%S'` . "</strong><br/>" . PHP_EOL;
    if ($updays) {
    	echo "System Uptime: <strong>$updays</strong><br/>";
    }
    echo '</p></span></div>' . PHP_EOL;
}

private function rets() {
	
	$flg=false; 
	if ($handle = opendir('/opt/sark/passwd')) {
		while (false !== ($entry = readdir($handle))) {
			if (preg_match( '/\.(\d+)$/',$entry,$matches)) {
				$flg =  $matches[1];
				break;
			}
		}
		closedir($handle);
	}  
	if (! $flg) {
        $flg = rand(100000,1000000);
		`/bin/touch /opt/sark/passwd/.$flg`;
	}
	return ($flg);
}


private function sark_stop () {
	
if ( $this->distro['debian'] ) {
	`/usr/bin/sudo /etc/init.d/asterisk stop`;
}
else {
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv d sark')); 
   	$ret = ($this->helper->request_syscmd ('/usr/bin/sv k sark')); 
}
	return ("Stop signal sent");
}

private function sark_start () {

//	
if ( $this->distro['debian'] ) {	
	`/usr/bin/sudo /etc/init.d/asterisk start`;
}
else {
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv u sark'));
}
	return ("Start signal sent");	
}

private function globalAction ($global)
{

    $this->myPanel->Button("save");
    $this->myPanel->commitButton();
    
	if ( ! $this->helper->check_pid() ) {    
		if ( $global['BINDADDR'] != "ON") {   
				$this->myPanel->Button("start");
		}
	}
	else {
		$this->myPanel->Button("stop");
	}
	$this->myPanel->Button("reboot");
}


}
