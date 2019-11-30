<?php
//
// Developed by CoCo
// Copyright (C) 2018 CoCoSoft
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
	
	protected $message=NULL;
	protected $head="Globals"; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $HA;
	protected $myBooleans = array(
		'acl',
		'lterm',
		'camponqonoff',
		'cfwdanswer',
		'cfwdextrnrule',
		'cfwdprogress',
		'clusterstart',
		'cosstart',		
		'playbeep',
		'playtransfer',		
		'proxy',
		'sipmulticast',
		'usercreate',		
		'voiceinstr',
		'ztp'		
	);
	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
			
	$this->myPanel->pagename = 'Globals';
 
//	Debugging		
//	$this->helper->logit(print_r($_POST, TRUE));
//    print_r($this->error_hash);

	if (isset($_POST['start'])) { 
		$this->message = $this->sark_start();	
	}
	
	if (isset($_POST['stop'])) { 
		$this->message = $this->sark_stop();	
	}
	if (isset($_POST['sipcapIsOff'])) { 
		$this->message = $this->sipcap_start();	
	}
	
	if (isset($_POST['sipcapIsOn'])) { 
		$this->message = $this->sipcap_stop();	
	}
		
	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}					
	}
	
	if (isset($_POST['reboot'])) { 
//		$this->saveEdit();
		if ( ! $this->invalidForm) {
			$this->helper->request_syscmd ("reboot");
			unset($_SESSION['user']);
			echo "reboot detected";
			$this->message = "Rebooting Now, Please wait...";
		}
	}		
	
	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}
		else {
			$this->helper->sysCommit();
			$this->message = "Committed!";	
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
	
	$astfiles = array();
	$tuple = array();

//  check if any extensions are defined		
	$extensions = false;
	$res = $this->dbh->query('select count(*) from ipphone')->fetchColumn(); 
	
	if ($res > 0) {
		$extensions = true;
	} 

// 	fetch the global tuple 	
	$global = $this->dbh->query("SELECT * FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);

	// set OTP if required

 	if ( empty($global['USEROTP'] )) {
 		$global['USEROTP'] = $this->helper->ret_password (6);
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

	
	if ( !$global['PWDLEN'] ) {
		$global['PWDLEN'] = 12;
	}
	if ( !isset ($global['VMAILAGE']) ) {
		$global['VMAILAGE'] = 60;
	}	

  	$buttonArray=array();
	$buttonArray['reboot'] = "";


	if ( file_exists("/opt/sark/service/srk-ua-siplog/down" )) {    
			$buttonArray['sipcapIsOff'] = "w3-text-green";
	}
	else {
		$buttonArray['sipcapIsOn'] = "w3-text-blue";
	}	

	if ( ! $this->helper->check_pid() ) {    
		if ( $global['BINDADDR'] != "ON") {   
				$buttonArray['start'] = "w3-text-light-green";
		}
	}
	else {
		$buttonArray['stop'] = "w3-text-red";
	}

  	$this->myPanel->actionBar($buttonArray,"sarkglobalForm",false,true,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveTwoCol();

	echo '<form id="sarkglobalForm" action="' . htmlspecialchars($_SERVER['PHP_SELF']) . '" method="post">' . PHP_EOL;
/*
 *       TAB System 
 */
	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("General Settings");
  	

  	$this->myPanel->displayPopupFor('countrycode',$global['COUNTRYCODE'],$country);  
	
//	if ( ! $extensions ) {	
//		$this->myPanel->displayPopupFor('extlen',$global['EXTLEN'],array('3','4','5','6'));
//	} 

//    $this->myPanel->displayInputFor('sipiaxstart','number',$global['SIPIAXSTART']);

    if ( ! $global['VCL'] ) {
    	$this->myPanel->radioSlide('natdefault',$global['NATDEFAULT'],array('local','remote'));
    }
/*
  	$this->myPanel->displayInputFor('agentstart','number',$global['AGENTSTART']);   	                  		  
	$this->myPanel->displayInputFor('operator','text',$global['OPERATOR']);
*/
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('sysop');
	echo '</div>'; 
	$this->myPanel->selected = $global['SYSOP'];
	$this->myPanel->sysSelect('SYSOP',false,true) . PHP_EOL;
	$this->myPanel->aHelpBoxFor('sysop');

	$this->myPanel->displayInputFor('emergency','text',$global['EMERGENCY']);
    $this->myPanel->displayInputFor('pwdlen','number',$global['PWDLEN']);
    $this->myPanel->displayInputFor('syspass','number',$global['SYSPASS']);
    $this->myPanel->displayInputFor('spypass','number',$global['SPYPASS']);
    $this->myPanel->displayInputFor('supemail','email',$global['SUPEMAIL']);
    $this->myPanel->displayInputFor('loglevel','number',$global['LOGLEVEL']);
    echo '</div>';

/*
 *       TAB Services
 */
 	$this->myPanel->internalEditBoxStart();
    $this->myPanel->subjectBar("PBX Services");
    $this->myPanel->displayBooleanFor('sipmulticast',$global['SIPMULTICAST']);
    $this->myPanel->displayBooleanFor('ztp',$global['ZTP']);
//    $this->myPanel->displayBooleanFor('proxy',$global['PROXY']);
    $this->myPanel->displayBooleanFor('acl',$global['ACL']);
    $this->myPanel->displayBooleanFor('cosstart',$global['COSSTART']);
    $this->myPanel->displayBooleanFor('clusterstart',$global['CLUSTER']);
    $this->myPanel->displayBooleanFor('camponqonoff',$global['CAMPONQONOFF']);    
    $this->myPanel->displayInputFor('camponqopt','text',$global['CAMPONQOPT']);

	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('blindbusy');
	echo '</div>';	
	$this->myPanel->selected = $global['BLINDBUSY'];
	$this->myPanel->sysSelect('BLINDBUSY') . PHP_EOL; 
	$this->myPanel->aHelpBoxFor('blindbusy');

	$this->myPanel->displayInputFor('bouncealert','text',$global['BOUNCEALERT']);
	echo '</div>';
		
	$this->myPanel->internalEditBoxStart();
 	$this->myPanel->subjectBar("Continuous SIP PCAP Logging");   
    $this->myPanel->displayInputFor('logsipfilesize','number',$global['LOGSIPFILESIZE']);
    $this->myPanel->displayInputFor('logsipnumfiles','number',$global['LOGSIPNUMFILES']);
    $this->myPanel->displayInputFor('logsipdispsize','number',$global['LOGSIPDISPSIZE']);
    echo '<div class="w3-container w3-padding w3-margin-top">' . PHP_EOL;
	echo '<button class="w3-button w3-blue w3-small w3-round-xxlarge w3-padding w3-right" type="submit" name="sipcapClear">Clear PCAP logs</button>';
	echo '</div>' . PHP_EOL;
    echo '</div>';	

#
#       TAB DIVEND
#
//    echo '</div>' . PHP_EOL;

	$this->myPanel->responsiveTwoColRight();
/*
 *       TAB Control
 */
 	$this->myPanel->internalEditBoxStart();
 	$this->myPanel->subjectBar("Control");   
    $this->myPanel->displayBooleanFor('lterm',$global['LTERM']);
    $this->myPanel->displayBooleanFor('cfwdanswer',$global['CFWDANSWER']);
    $this->myPanel->displayBooleanFor('cfwdextrnrule',$global['CFWDEXTRNRULE']);
    $this->myPanel->displayBooleanFor('cfwdprogress',$global['CFWDPROGRESS']);
    $this->myPanel->displayBooleanFor('voiceinstr',$global['VOICEINSTR']);
    $this->myPanel->displayBooleanFor('playbeep',$global['PLAYBEEP']);
	$this->myPanel->displayBooleanFor('playtransfer',$global['PLAYTRANSFER']);
    $this->myPanel->radioSlide('playcongested',$global['PLAYCONGESTED'],array('YES','NO','SIGNAL'));
    $this->myPanel->radioSlide('playbusy',$global['PLAYBUSY'],array('YES','NO','SIGNAL'));
    $this->myPanel->radioSlide('callrecord1',$global['CALLRECORD1'],array('None','OTR','OTRR','In','Out','Both'));	
    $this->myPanel->displayInputFor('vmailage','number',$global['VMAILAGE']);
    $this->myPanel->displayInputFor('intringdelay','number',$global['INTRINGDELAY']);
    $this->myPanel->displayInputFor('abstimeout','number',$global['ABSTIMEOUT']);
    $this->myPanel->displayInputFor('voipmax','number',$global['VOIPMAX']);
    $this->myPanel->displayInputFor('ringdelay','number',$global['RINGDELAY']);
    echo '</div>';

/*
 *       TAB DIVEND
 */
//    echo "</div>". PHP_EOL;
	

	if ($global['CLUSTER'] == 'OFF') {
		$this->myPanel->internalEditBoxStart();
		$this->myPanel->subjectBar("LDAP Settings");   
		$this->myPanel->displayInputFor('ldapbase','text',$global['LDAPBASE']);
		$this->myPanel->displayInputFor('ldapou','text',$global['LDAPOU']);
		$this->myPanel->displayInputFor('ldapuser','text',$global['LDAPUSER']);
		$this->myPanel->displayInputFor('ldappass','password',$global['LDAPPASS']);
		echo '</div>';
	}

	$this->myPanel->internalEditBoxStart();
 	$this->myPanel->subjectBar("User Services");   
    $this->myPanel->displayBooleanFor('usercreate',$global['USERCREATE']);
    $this->myPanel->displayInputFor('userotp','text',$global['USEROTP']);    
    echo '</div>';
	
	$this->myPanel->responsiveClose();
    echo '<div class="w3-left w3-container"></div>' . PHP_EOL;
	
	$endButtonArray['update'] = "endupdate";
	$this->myPanel->endBar($endButtonArray);

    echo '</form>' . PHP_EOL; // close the form  
    
    
}

private function saveEdit() {
// save the data away
// '
	$this->myPanel->xlateBooleans($this->myBooleans);

// print_r($_POST);

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
	
	$this->validator->addValidation("pwdlen","num","Extension password length must be numeric"); 
	$this->validator->addValidation("pwdlen","lt=19","Extension password length must be 18 or less");
	$this->validator->addValidation("pwdlen","gt=5","Extension password length must be 6 or more"); 
	$this->validator->addValidation("syspass","num","Sys password must be numeric"); 
	$this->validator->addValidation("syspass","maxlen=4","Sys password must be 4 digits"); 
	$this->validator->addValidation("syspass","minlen=4","Sys password must be 4 digits");
	$this->validator->addValidation("spypass","num","Spy password password must be numeric");
	$this->validator->addValidation("spypass","maxlen=4","Spy password must be 4 digits"); 
	$this->validator->addValidation("spypass","minlen=4","Spy password must be 4 digits");		
    $this->validator->addValidation("supemail","email","Supervisor email is malformed");    
    $this->validator->addValidation("intringdelay","req","Ringtime must be entered"); 
    $this->validator->addValidation("intringdelay","num","Ringtime must be numeric");
    $this->validator->addValidation("intringdelay","maxlen=3","Ringtime must be 3 digits or less");     
    $this->validator->addValidation("abstimeout","req","Call timeout must be entered");
    $this->validator->addValidation("abstimeout","num","Call timeout must be numeric");    
    $this->validator->addValidation("voipmax","req","Max Outbound VOIP Calls must be entered");
    $this->validator->addValidation("voipmax","num","Max Outbound VOIP Calls must be numeric");    
//  $this->validator->addValidation("extlen","num","Extension Length must be numeric");
//  $this->validator->addValidation("extlen","gt=2","Extension Length must 3 to 6");
//  $this->validator->addValidation("extlen","lt=7","Extension Length must 3 to 6");    
//  $this->validator->addValidation("sipiaxstart","req","Extension start must be entered");
//  $this->validator->addValidation("sipiaxstart","num","Extension start must be numeric");
//  $this->validator->addValidation("sipiaxstart","maxlen=6","Extension start must be 3 to 6 digits (same as extension length)"); 
//	$this->validator->addValidation("sipiaxstart","minlen=3","Extension start must be 3 to 6 digits (same as extension length)");	   
//    $this->validator->addValidation("agentstart","num","Agent start must be numeric");
//   $this->validator->addValidation("agentstart","maxlen=4","Agent start must be 4 digits");
//    $this->validator->addValidation("agentstart","minlen=4","Agent start must be 4 digits");    
    $this->validator->addValidation("operator","num","Operator must be numeric");   
/*
    $this->validator->addValidation("EDOMAIN",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"External IP address is invalid");
*/
   
    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 *	compensate for short description in recording opts
 */   
		if (isset($_POST['callrecord1'])) {
			if ($_POST['callrecord1'] == 'In' || $_POST['callrecord1'] == 'Out') {
				$_POST['callrecord1'] .= 'bound';
			}
		}
/*
 * 	call the tuple builder to create a table row array 
 */		
		$custom = array ('LINUXTZ' => True,'clusterstart' => True);		
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
		$tuple['pkey'] = 'global';
		$tuple['cluster'] = $_POST['clusterstart'];
//		print_r($tuple);
		
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
			if ($tuple['cluster'] == 'OFF') {
				$active = 'no';
				$ldap = 'yes';				
			}
			$res=$this->dbh->exec("UPDATE Panel SET active='" . $active . "' WHERE pkey=210");
			$res=$this->dbh->exec("UPDATE Panel SET active='" . $ldap . "' WHERE pkey=265");
/*
 * 
 */ 			
			$this->goodEditOutcome($tuple);
			$this->message = "Updated!";
			unset ($this->validator);
			return;
		}
    }
    $this->invalidForm = True;
	$this->error_hash = $this->validator->GetErrors();
//	$this->message = "Error!";
	unset ($this->validator);		
    
}

private function goodEditOutcome($tuple) {
/* 
 * do all of the ancillary stuff that is necessary after a globals update
 */ 

/*
 * update the countrycode in indications
 */ 
	$rc = $this->helper->request_syscmd ("/bin/sed -i 's/country=\\w\\w/country=" . $tuple['countrycode'] . "/' /etc/asterisk/indications.conf");
/*
 * 	start/stop multicast listener
 */
		
	if ($tuple['sipmulticast'] == "enabled" ) {
			$this->helper->request_syscmd ( "/bin/rm -f  /etc/service/srk-ua-responder/down" ); 
	}			
	else {
		$this->helper->request_syscmd ( "/usr/bin/sv d  srk-ua-responder" );
		$this->helper->request_syscmd ( "/bin/touch  /etc/service/srk-ua-responder/down" );							
    }
    $this->helper->request_syscmd ( "/usr/bin/sv k  srk-ua-responder" );
}

/*
 *	stop Asterisk
 */

private function sark_stop () {
	

	`/usr/bin/sudo /etc/init.d/asterisk stop`;


	return ("PBX stopped");
}

/*
 *	start Asterisk
 */

private function sark_start () {

	`/usr/bin/sudo /etc/init.d/asterisk start`;

	return ("PBX started");	
}

private function sipcap_clear () {

	$ret = ($this->helper->request_syscmd ('/usr/bin/touch /opt/sark/service/srk-ua-siplog/down'));
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv d srk-ua-siplog'));
	$ret = ($this->helper->request_syscmd ('rm -rf /var/log/siplog/*'));
	return ("SIP PCAP Logs cleared");	
}

private function sipcap_stop () {

	$ret = ($this->helper->request_syscmd ('/usr/bin/touch /opt/sark/service/srk-ua-siplog/down'));
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv d srk-ua-siplog'));
	return ("Siplog stopped");	
}

private function sipcap_start () {

	$ret = ($this->helper->request_syscmd ('/bin/rm /opt/sark/service/srk-ua-siplog/down'));
	$ret = ($this->helper->request_syscmd ('/usr/bin/sv u srk-ua-siplog'));
	return ("Siplog started");
}


}
