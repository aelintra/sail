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


Class sarkglobal {
	
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
			
	echo '<form id="sarkglobalForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Settings';
	
	if (isset($_POST['start_x'])) { 
		$this->message = $this->sark_start();	
	}
	
	if (isset($_POST['stop_x'])) { 
		$this->message = $this->sark_stop();	
	}
	if (isset($_POST['sipcapIsOff_x'])) { 
		$this->message = $this->sipcap_start();	
	}
	
	if (isset($_POST['sipcapIsOn_x'])) { 
		$this->message = $this->sipcap_stop();	
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
//			$this->helper->request_syscmd ("reboot");
			unset($_SESSION['user']);
			echo "reboot detected";
			$this->message = "Rebooting Now, Please wait...";
		}
	}		
	
	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}
		else {
			$this->helper->sysCommit();
			$this->message = "Updates have been Committed";	
		}		
	}
			
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	
	$tabselect = 0;
	if (isset($_POST['tabselect'])) {
		$tabselect = $_POST['tabselect'];
	}
	if (isset($_GET['tabselect'])) {
		$tabselect = $_GET['tabselect'];
	}		
	echo '<input type="hidden" id="tabselect" name="tabselect" value="'. $tabselect . '" />' . PHP_EOL;	
		
	$astfiles = array();
	$tuple = array();	
	$extensions = false;
	$res = $this->dbh->query('select count(*) from ipphone')->fetchColumn(); 
	
	if ($res > 0) {
		$extensions = true;
	} 
	
	$global = $this->dbh->query("SELECT * FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);

/* 
 * start page output
 */
	echo '<br/>' . PHP_EOL;
	echo '<div class="titlebar">' . PHP_EOL;
	echo '<div class="buttons">' . PHP_EOL;
	$this->globalAction($global);
	echo '</div>' . PHP_EOL;
	if (!empty($this->error_hash)) {
		$this->myPanel->msg = reset($this->error_hash);	
	}
	$this->myPanel->Heading();

	echo '</div>' . PHP_EOL;
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
		$global['PWDLEN'] = 12;
	}
	if ( !isset ($global['VMAILAGE']) ) {
		$global['VMAILAGE'] = 60;
	}	
/*
 *  Print System status box
 */
	$this->myPanel->printSysNotes();
 	
	echo '<div class="exttabedit">';		

	echo '<div id="pagetabs" class="mytabs"  >' . PHP_EOL;
	
    echo '<ul>'.  PHP_EOL;
    echo '<li><a href="#system">General</a></li>'. PHP_EOL;
    echo '<li><a href="#services" >Services</a></li>'.  PHP_EOL;
    echo '<li><a href="#control" >Call-Control</a></li>'. PHP_EOL;
    if ($global['CLUSTER'] == 'OFF') {
		echo '<li><a href="#ldap" >LDAP</a></li>'. PHP_EOL;
	}

    echo '<li><a href="#sysadmin" >Admin</a></li>'. PHP_EOL;
    echo '</ul>'. PHP_EOL;

	echo '<div id="sysadmin" >'. PHP_EOL;

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
    echo '<input type="text" name="ABSTIMEOUT" id="ABSTIMEOUT" size="5"  value="' . $global['ABSTIMEOUT'] . '"  />' . PHP_EOL;
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
    
    $this->myPanel->aLabelFor('camponqonoff');
    $this->myPanel->selected = $global['CAMPONQONOFF'];
    $this->myPanel->popUp('CAMPONQONOFF', array('OFF','ON'));
    $this->myPanel->aLabelFor('camponqopt');    
    echo '<input type="text" name="CAMPONQOPT" id="CAMPONQOPT" size="5"  value="' . $global['CAMPONQOPT'] . '"  />' . PHP_EOL; 
	$this->myPanel->aLabelFor('blindbusy');
	$this->myPanel->selected = $global['BLINDBUSY'];
	$this->myPanel->sysSelect('BLINDBUSY') . PHP_EOL;    
    $this->myPanel->aLabelFor('bouncealert');    
    echo '<input type="text" name="BOUNCEALERT" id="BOUNCEALERT" size="20"  value="' . $global['BOUNCEALERT'] . '"  />' . PHP_EOL; 
    $this->myPanel->aLabelFor('tlsport');    
    echo '<input type="text" name="TLSPORT" id="TLSPORT" size="5"  value="' . $global['TLSPORT'] . '"  />' . PHP_EOL;     




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
     
    $this->myPanel->aLabelFor('fqdn');    
    echo '<input type="text" name="FQDN" id="FQDN" size="25"  value="' . $global['FQDN'] . '"  />' . PHP_EOL;
    
    if (!empty($global['FQDN'])) {
    	$this->myPanel->aLabelFor('fqdnprov',false,'fqdnprovlabel');
    	$this->myPanel->selected = $global['FQDNPROV'];
    	$this->myPanel->popUp('FQDNPROV', array('NO','YES')); 
    }
     
    $this->myPanel->aLabelFor('countrycode');
    $this->myPanel->selected = $global['COUNTRYCODE'];
    $this->myPanel->popUp('COUNTRYCODE', $country);
    
    $this->myPanel->aLabelFor('voiceinstr');
    $this->myPanel->selected = $global['VOICEINSTR'];
    $this->myPanel->popUp('VOICEINSTR', array('YES','NO'));  
    
    $this->myPanel->aLabelFor('vmailage');
    $this->myPanel->selected = $global['VMAILAGE'];
    echo '<input type="text" name="VMAILAGE" id="VMAILAGE" size="3"  value="' . $global['VMAILAGE'] . '"  />' . PHP_EOL;       	   

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
                	
	$this->myPanel->aLabelFor('callrecord1');
	$this->myPanel->selected = $global['CALLRECORD1'];
	$this->myPanel->popUp('CALLRECORD1', array('None','OTR','OTRR','Inbound','Outbound','Both')); 				
 		  
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
	if ($global['CLUSTER'] == 'OFF') {
		echo '<div id="ldap" >'. PHP_EOL;
   
		$this->myPanel->aLabelFor('ldapbase');
		echo '<input type="text" name="ldapbase" id="ldapbase" size="24"  value="' . $global['LDAPBASE'] . '"  />' . PHP_EOL;
		$this->myPanel->aLabelFor('ldapou');
		echo '<input type="text" name="ldapou" id="ldapou" size="24"  value="' . $global['LDAPOU'] . '"  />' . PHP_EOL;
    
		$this->myPanel->aLabelFor('ldapuser');
		echo '<input type="text" name="ldapuser" id="ldapuser" size="12"  value="' . $global['LDAPUSER'] . '"  />' . PHP_EOL;
		$this->myPanel->aLabelFor('ldappass');
		echo '<input type="password" name="ldappass" id="ldappass" size="12"  value="' . $global['LDAPPASS'] . '"  />' . PHP_EOL;
	}

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
/*	
	if (isset($_POST['EURL'])) {
		$fvar = filter_var($_POST['EURL'], FILTER_VALIDATE_URL);
		if ($fvar) {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['extensave'] = "External URL is invalid!";
		}
	}
*/
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
    $this->validator->addValidation("TLSPORT","num","TLS Port must be numeric");    
/*
    $this->validator->addValidation("EDOMAIN",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"External IP address is invalid");
*/
    //Now, validate the form
    if ($this->validator->ValidateForm()) {

/*
 * 	call the tuple builder to create a table row array 
 */  
		$custom = array ('LINUXTZ' => True);		
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
		$tuple['pkey'] = 'global';

/* 
 * deal with fqdn
 */
 			if (empty($tuple['FQDN'])) {
 				$tuple['FQDNPROV'] = 'NO';
 			}		
		
/*
 * call the setter
 */ 
		$ret = $this->helper->setTuple("globals",$tuple);
		
/*
 * flag errors
 */ 	
		if ($ret == 'OK') {
/*
 * deal with multi tenant
 */
			$active = 'yes';
			$ldap = 'no';
			if ($tuple['CLUSTER'] == 'OFF') {
				$active = 'no';
				$ldap = 'yes';				
			}
			$res=$this->dbh->exec("UPDATE Panel SET active='" . $active . "' WHERE pkey=210");
			$res=$this->dbh->exec("UPDATE Panel SET active='" . $ldap . "' WHERE pkey=265");
/*
 * 
 */ 			
			$this->goodEditOutcome($tuple);
			$this->message = "Updated Globals!";
			unset ($this->validator);
			return;
		}
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Error! - ";
		unset ($this->validator);		
    }
    
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

    $this->helper->request_syscmd ( "/usr/bin/sv k  srk-ua-responder" );
}

private function sark_stop () {
	
if ( $this->distro['debian'] ) {
//	$ret = ($this->helper->request_syscmd ( "/etc/init.d/asterisk stop"));
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
//	$ret = ($this->helper->request_syscmd ( "/etc/init.d/asterisk start"));
	`/usr/bin/sudo /etc/init.d/asterisk start`;
}
else {
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv u sark'));
}
	return ("Start signal sent");	
}

private function sipcap_stop () {

	$ret = ($this->helper->request_syscmd ('/usr/bin/touch /opt/sark/service/srk-ua-siplog/down'));
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv d srk-ua-siplog'));
	return ("SIP packet log stopped");	
}

private function sipcap_start () {

	$ret = ($this->helper->request_syscmd ('/bin/rm /opt/sark/service/srk-ua-siplog/down'));
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv u srk-ua-siplog'));
	return ("SIP packet log started");
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
	if ( file_exists("/opt/sark/service/srk-ua-siplog/down" )) {    
			$this->myPanel->Button("sipcapIsOff");
	}
	else {
		$this->myPanel->Button("sipcapIsOn");
	}	
	$this->myPanel->Button("reboot");
}


}
