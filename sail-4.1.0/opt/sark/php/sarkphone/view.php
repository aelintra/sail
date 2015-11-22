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
require_once "../AsteriskManager.php";


Class phone {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $params = array('server' => '127.0.0.1', 'port' => '5038');
	protected $astrunning=false;
	protected $pkey;
	protected $selection;
	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}

	echo '<body>';
	echo '<form id="sarkphoneForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
print_r($_SERVER);
	$this->myPanel->pagename = 'Phone';
	$user = $_SERVER['REMOTE_USER'];
	$res = $this->dbh->query("SELECT extension,selection FROM user WHERE pkey = '" . $user . "'")->fetch(PDO::FETCH_ASSOC);
	
	if (isset($res['extension']) && $res['extension'] != 'None') {
		$this->pkey = $res['extension'];
		$this->selection = $res['selection'];
	}
	else {		
		$this->myPanel->msg .= "No phone extension associated with user " . $_SERVER['REMOTE_USER'] . " - Contact your Administrator" . PHP_EOL;
		$this->myPanel->Heading();
		exit;
	}
	
	
		
	if (isset($_GET['delete'])) {
		if (isset($_GET['pkey'])) {
			$id = $_GET['pkey'];
			$this->helper->request_syscmd ( "rm $id.*" );			
			$this->helper->logit("I'm deleting file $id ",3 );
			$this->message = " - Voicemail successfully deleted!";
			if (count(glob("/var/spool/asterisk/voicemail/default/" . $this->pkey . "/INBOX/*")) === 0) {
				$this->helper->request_syscmd ( "asterisk -rx 'sip notify clear-mwi " . $this-pkey . "'" );
			}
			if (preg_match('/INBOX/',$_GET['pkey'])) {
				echo '<input type="hidden" id="tabselect" name="tabselect" value="3" />' . PHP_EOL;
			}
			else {
				echo '<input type="hidden" id="tabselect" name="tabselect" value="4" />' . PHP_EOL;
			}	
		}
	}
		
	if (isset($_POST['newblf_x'])) { 
		$this->saveNewBlf();
		$this->showEdit();
		return;
	}	

	if (isset($_POST['delblf_x'])) { 
		$this->deleteLastBlf();
		$this->showEdit();
		return;
	}		
	
	if (isset($_POST['reboot_x'])) { 
		$this->sipNotify();
		$this->message = " - Reboot request sent to Ext" . $this->pkey;
		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;			
	}	
			
	if (isset($_POST['upload_x'])) { 
		$this->sipNotifyPush();
		$this->message = " - Config request pushed to Ext" . $this->pkey;
		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;			
	}
	
	if (isset($_POST['update_x'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}					
	}

	$this->showEdit();
	
	$this->dbh = NULL;
	return;
	
}	

private function showEdit() {
	
	$extension = $this->dbh->query("SELECT * FROM IPphone WHERE pkey = '" . $this->pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$printline = "Ext " . $extension['technology'] . "/" . $this->pkey . ', "' . $extension['desc'] . '"(' . $extension['device'] . ')';
	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}	
		
	if ($this->astrunning) {
		$ami = new ami($this->params);
		$amiconrets  = $ami->connect();
		if ( !$amiconrets ) {
			$this->myPanel->msg .= "  (AMI Connect failed)";
		}
		else {
			$ami->login('sark','mysark');
			$cfim = $ami->GetDB('cfim', $this->pkey);
			$cfbs = $ami->GetDB('cfbs', $this->pkey);
			$ringdelay = $ami->GetDB('ringdelay', $this->pkey);
			$ami->logout();
		}
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}
	
	if (isset($this->pkey)) {
		echo '<div class="buttons">';
		$this->myPanel->override="newblf";
		$this->myPanel->buttonName["new"]["title"] = "Add a new BLF key";
		$this->myPanel->Button("new");
		$this->myPanel->override="delblf";
		$this->myPanel->overrideClick="";
		$this->myPanel->buttonName["delete"]["title"] = "Delete the last BLF key";
		$this->myPanel->Button("delete");		
		$this->myPanel->override="update";
		$this->myPanel->Button("save");
		$this->myPanel->buttonName["reboot"]["title"] = "Reboot the phone";
		$this->myPanel->Button("reboot");
		if (preg_match('/^Snom/',$extension['device'])) {
			$this->myPanel->buttonName["upload"]["title"] = "Update without reboot(Snom phones only)";
			$this->myPanel->Button("upload");
		}
		echo '</div>';
	} 	
				
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	

	echo '<div class="datadivtabedit">';
	echo '<div id="pagetabs" >' . PHP_EOL;
	echo '<ul>' . PHP_EOL;
	echo '<li><a href="#general">General</a></li>'. PHP_EOL;
	if (isset($extension['macaddr']) && $this->selection != 'enduser') {
		echo  '<li><a href="#blf">BLF/DSS Keys</a></li>' . PHP_EOL;
	}			
	echo  '<li><a href="#vmail">Vmail Settings</a></li>' . PHP_EOL;
    echo '<li><a href="#INBOX">New Vmail</a></li>'. PHP_EOL;
    echo '<li><a href="#Old">Old Vmail</a></li>'.  PHP_EOL;
    if ( $this->astrunning ) {
		if ( $amiconrets ) {
			echo '<li><a href="#cfwd" >CFWD</a></li>' . PHP_EOL;
		}
	}

    echo '</ul>' . PHP_EOL;
    	
/*
 * 	TAB General
 */  
    echo '<div id="general" >';
    
    $this->myPanel->aLabelFor('rule');
	echo '<input type="text" style = "background-color: lightgrey" readonly="readonly" name="newkey" size="4" id="newkey" value="' . $extension['pkey'] . '"  />' . PHP_EOL;
	
	$this->myPanel->aLabelFor('calleridname');
	echo '<input type="text" style = "background-color: lightgrey" readonly="readonly" name="desc" id="desc" value="' . $extension['desc'] . '" />' . PHP_EOL;

	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		$this->myPanel->aLabelFor('macaddr');
		echo '<input type="text" style = "background-color: lightgrey" readonly="readonly" name="macaddr" id="macaddr" size="14" ';
		echo 'value="' . $extension['macaddr'] . '"  />' . PHP_EOL;
	}						
	if ($this->astrunning) {
		$this->myPanel->aLabelFor('ringdelay');
	}
    echo '<input type="text" name="ringdelay" id="ringdelay" size="2"  value="' . $ringdelay . '"  />' . PHP_EOL;
    			
	echo '</div>' . PHP_EOL;
	
/*
 * 	TAB BLF/DSS Keys
 */  
	if (isset($extension['macaddr']) && $this->selection != 'enduser') {
		echo '<div id="blf">';
       
		echo '<table id="blftable">' ;
		echo '<thead>' . PHP_EOL;	
		echo '<tr>' . PHP_EOL;
	
		$this->myPanel->aHeaderFor('blfkey',false); 	
		$this->myPanel->aHeaderFor('blftype',false);
		$this->myPanel->aHeaderFor('blflabel',false);
		$this->myPanel->aHeaderFor('blfvalue',false);
	
		echo '</tr>' . PHP_EOL;
		echo '</thead>' . PHP_EOL;
		echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
		$sql = "select * from Ipphone_FKEY where pkey='" . $this->pkey . "'";
		$rows = $this->helper->getTable("ipphone_fkey",$sql,false);
		foreach ($rows as $row ) {
			echo '<tr id="' . $row['seq'] . '~' . $row['pkey'] . '">'. PHP_EOL; 
			echo '<td>' . $row['seq'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['type'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['label'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['value'] . '</td>' . PHP_EOL;	
			echo '</tr>'. PHP_EOL;
		}

		echo '</tbody>' . PHP_EOL;
		echo '</table>' . PHP_EOL;	
		echo '</div>' . PHP_EOL;

	}	


/* ToDo - Twinning (needs AGI support)
    echo '<h2>Cellphone Twinning</h2>' . PHP_EOL;
    $this->myPanel->aLabelFor('twin');
    echo '<input type="text" name="twin" id="twin" size="11"  value="' . $extension['twin'] . '"  />' . PHP_EOL;
*/

		
/*
 * 	TAB Vmail
 */ 
	echo '<div id="vmail"  >' . PHP_EOL;
//	echo '<h2>Voicemail Settings</h2>' . PHP_EOL;
	$this->myPanel->aLabelFor('vmailfwd');
	echo '<input type="text" name="vmailfwd" id="vmailfwd" size="30"  value="' . $extension['vmailfwd'] . '"  />' . PHP_EOL;

	$this->myPanel->aLabelFor('dvrvmail');
	echo '<input type="text" style = "background-color: lightgrey" readonly="readonly" name="dvrvmail" id="dvrvmail" size="4"  value="' . $extension['dvrvmail'] . '"  />' . PHP_EOL;	
	    
	$this->myPanel->aLabelFor('vdelete');
    echo '<input type="checkbox"   name="vdelete" value="vdelete" />'; 
	$this->myPanel->aLabelFor('vreset');
    echo '<input type="checkbox"   name="vreset" value="vreset" />';	
	echo '</div>' . PHP_EOL;
	   
/*
 * 		TAB newmail
 */   
 
	$this->genMail('INBOX');
	
/*
 * 		TAB oldmail
 */   
 
	$this->genMail('Old');	
	
/*
 * 	TAB Call Forwards
 */ 
	echo '<div id="cfwd"  >' . PHP_EOL;
    echo '<h2>Internal PBX Call Forwards</h2>' . PHP_EOL;
    $this->myPanel->aLabelFor('cfim');
    echo '<input type="text" name="cfim" id="cfim" value="' . $cfim . '"  />' . PHP_EOL;
    $this->myPanel->aLabelFor('cfbs');
    echo '<input type="text" name="cfbs" id="cfbs" value="' . $cfbs . '"  />' . PHP_EOL;

/* ToDo - needs AGI support
    echo '<h2>Cellphone Twinning</h2>' . PHP_EOL;
    $this->myPanel->aLabelFor('twin');
    echo '<input type="text" name="twin" id="twin" size="11"  value="' . $extension['twin'] . '"  />' . PHP_EOL;
*/	

	echo '</div>' . PHP_EOL;
	
    
/*
 *      TAB TABEND DIV
 */    
    echo '</div>' . PHP_EOL;			
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $this->pkey . '" />' . PHP_EOL;	
}

private function saveEdit() {
// save the data away

	$tuple = array();
	
	$extension = $this->dbh->query("SELECT * FROM IPphone WHERE pkey = '" . $this->pkey . "'")->fetch(PDO::FETCH_ASSOC);
		
	$this->validator = new FormValidator();
    $this->validator->addValidation("vmailfwd","email","Invalid email address format");  
    $this->validator->addValidation("desc","regexp=/^[0-9a-zA-Z_-]+$/","Caller name is invalid - must be [0-9a-zA-Z_-]"); 
    $this->validator->addValidation("cfim","num","Call forward must be numeric"); 
    $this->validator->addValidation("cfbs","num","Call forward must be numeric"); 
    $this->validator->addValidation("ringdelay","num","Ring Time must be numeric"); 
		
    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$custom = array (
						'cfim' => True,
						'cfbs' => True,
						'ringdelay' => True,
						'opencos' => True,
						'closedcos' => True,
						'newkey' => True,
						'vdelete' => True,
						'vreset' => True
//						'sipiaxfriend' => True
		);
		
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
		$tuple['pkey'] = $this->pkey;
		$tuple['sipiaxfriend'] = $extension['sipiaxfriend'];

/*	
 * update the asterisk internal database (callforwards and ringdelay)
 */ 
		if ($this->astrunning) {			
			$ami = new ami($this->params);
			$amiconrets  = $ami->connect();
			if ( !$amiconrets ) {
				$this->myPanel->msg .= "  (AMI Connect failed)";
			}
			else {				
				$ami->login('sark','mysark');
				if (isset($_POST['cfim'])) {
					$cfim			= strip_tags($_POST['cfim']);
					if ($cfim) {
						$ami->PutDB('cfim', $this->pkey, $cfim);
					}
					else {
						$ami->DelDB('cfim', $this->pkey);
					}
				}
				if (isset($_POST['cfbs'])) {
					$cfbs			= strip_tags($_POST['cfbs']);
					if ($cfbs) {
						$ami->PutDB('cfbs', $this->pkey, $cfbs);
					}
					else {
						$ami->DelDB('cfbs', $this->pkey);
					}					
				}
				if (isset($_POST['ringdelay'])) {
					$ringdelay		= strip_tags($_POST['ringdelay']);	
					$ami->PutDB('ringdelay', $this->pkey, $ringdelay);				
				}					
				$ami->logout();
			}
		}
/*
 * reset/empty voicemail if requested
 */

		if (isset($_POST['vdelete'])) { 
			$rc = $this->helper->request_syscmd ("/bin/rm -rf /var/spool/asterisk/voicemail/default/" . $_POST['pkey']."/*");	
			$this->message = "Voicemail for Ext " . $this->pkey . " deleted";
		}	
	
		if (isset($_POST['vreset'])) { 
			$skey = $this->pkey;
			$rc = $this->helper->request_syscmd ("/bin/sed -i 's/^$skey => [0-9]*\(.*\)/$skey => $skey\\1/' /etc/asterisk/voicemail.conf");	
			$this->message = "Voicemail password for Ext " . $this->pkey . " reset";	
		}
 		
		$ret = $this->helper->setTuple("ipphone",$tuple,$this->pkey);
		if ($ret == 'OK') {
			$this->message = " - Updated extension ";
/*
 * do a very limited "commit"
 * just regen voicemail.conf and do a reload
 */
    
			$rc = $this->helper->request_syscmd ("php /opt/sark/generator/vmailSQL.php");
			$rc = $this->helper->request_syscmd ("asterisk -rx 'voicemail reload'");
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['extensave'] = $ret;	
		}
		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function sipNotify () {
/*
 * send a notify to a phone to reboot it
 */ 

		$res = $this->dbh->query("SELECT technology,device  FROM IPphone WHERE pkey ='" . $this->pkey . "'" )->fetch(PDO::FETCH_ASSOC);
		
		if ($res['technology'] != 'SIP') {
			$this->message = "Ext " . $this->pkey . " is not a SIP UA!!.";
			return;
		}
#
#	figure out what it is 
#
		$chk = false;
		if (preg_match ( " /Aastra/ ", $res['device'])) {
			$chk = 'aastra-check-cfg';
		}
		if (preg_match ( " /Linksys/ ", $res['device'])) {	
			$chk = 'sipura-check-cfg';
		}
		if (preg_match ( " /Cisco/ ", $res['device'])) {	
			$chk = 'cisco-check-cfg';
		}		
		if (preg_match ( " /Polycom/ ", $res['device'])) {	
			$chk = 'polycom-check-cfg';
		}	
		if (preg_match ( " /Snom/ ", $res['device'])) {
			$chk = 'snom-reboot';
		}
		if (preg_match ( " /Yealink/ ", $res['device'])) {
			$chk = 'yealink-reboot';
		}
		if (preg_match ( " /Grandstream/ ", $res['device'])) {
			$chk = 'grandstream-check-cfg';
		}
		if ( ! $chk ) {
			$this->message = "No notify data available for ext $this->pkey ";
			return;
		}
		$this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'sip notify $chk $this->pkey' ");
    	$this->message = "Issued SIP Notify to Ext " . $this->pkey . "(" . $res['device'] . ")" ;
}

private function sipNotifyPush () {
/*
 * send a notify to a phone to reboot it
 */ 

		$res = $this->dbh->query("SELECT technology,device  FROM IPphone WHERE pkey ='" . $this->pkey . "'" )->fetch(PDO::FETCH_ASSOC);
		
		if ($res['technology'] != 'SIP') {
			$this->message = "Ext " . $this->pkey . " is not a SIP UA!!.";
			return;
		}
#
#	Only for Snoms 
#
		$chk = false;
	
		if (preg_match ( " /Snom/ ", $res['device'])) {
			$chk = 'snom-check-cfg';
		}
		if ( ! $chk ) {
			$this->message = "No notify data available for ext $this->pkey ";
			return;
		}
		else {
			$this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'sip notify $chk $this->pkey' ");
    	}
    	$this->message = "Issued SIP Notify to Ext " . $this->pkey . "(" . $res['device'] . ")" ;
}

private function saveNewBlf() {
// save the data away
	echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
	
	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $this->pkey . "'")->fetchColumn();
	$seq++;
	$res=$this->dbh->prepare('INSERT INTO ipphone_fkey(pkey,seq,type,label,value) VALUES(?,?,?,?,?)');
	$res->execute(array( $this->pkey,$seq,'Default','None','None'));
	
}

private function deleteLastBlf() {
// save the data away
	echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
	
	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $this->pkey . "'")->fetchColumn();
	if ($seq) {
		$res=$this->dbh->prepare('DELETE FROM ipphone_fkey WHERE pkey=? AND seq=?');
		$res->execute(array( $this->pkey,$seq));
	}
	
}

private function build_peer_array($amirets) {
/*
 * build an array of peers by cleaning up the AMI output
 * (which contains stuff we don't want).
 */ 
	$peer_array=array();
	$lines = explode("\r\n",$amirets);	
	$peer = 0;
	foreach ($lines as $line) {
		// ignore lines that aren't couplets
		if (!preg_match(' /:/ ',$line)) { 
				continue;
		}
		
		// parse the couplet	
		$couplet = explode(': ', $line);
		
		// ignore events and ListItems
		if ($couplet[0] == 'Event' || $couplet[0] == 'ListItems') {
			continue;
		}
		
		//check for a new peer and set a new key if we have one
		if ($couplet[0] == 'ObjectName') {
			preg_match(' /^(.*)\// ',$couplet[1],$matches);
			if (isset($matches[1])) {
				$peer = $matches[1];
			}
			else {
				$peer = $couplet[1];
			}
		}
		else {
			if (!$peer) {
				continue;
			}
			else {
				$peer_array [$peer][$couplet[0]] = $couplet[1];
			}
		}
	}
	return $peer_array;	
}

private function genMail($mailbox) {
/*
 * gen a mailbox table
 */ 	
	$maildir = array();
	$path = "/var/spool/asterisk/voicemail/default/". $this->pkey . "/$mailbox/";
	
	if (file_exists($path)) {
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..') {
						array_push($maildir, $entry);
				}
			}	
			closedir($handle);
		}
	}
		
   	echo '<div id="' . $mailbox . '" >'. PHP_EOL;
   	
 	echo '<table class=display id="' . $mailbox . 'table"  >' ;	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>'. PHP_EOL; 
	$this->myPanel->aHeaderFor('callerid');
	$this->myPanel->aHeaderFor('Duration');		
	$this->myPanel->aHeaderFor('astfiledate');	
	$this->myPanel->aHeaderFor('dl');
	$this->myPanel->aHeaderFor('play');    		
	$this->myPanel->aHeaderFor('del');	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
/**** table rows ****/	
	foreach ($maildir as $file ) {
		$type = explode( '.', $file);
		if ($type[1] == 'txt') {
			continue;
		}
		$fname = $type[0];
		$finfofile = $path . $fname . '.txt';
		$finfo = file($finfofile) or die("Could not read file $finfofile !");
		$fullpath = $path . $file;
		$deletepath = $path . $fname; 		
		$rdate = date("F d Y H:i:s.", filectime($fullpath));
		$fsize = filesize($fullpath);
		echo '<input type="hidden" id="fname" name="fname" value="' . $file . '" />' . PHP_EOL;
		echo '<tr>' . PHP_EOL; 
		preg_match(' /<(\d+)>/ ', $finfo[11], $matches);
		echo '<td>' . $matches[1] . '</td>' . PHP_EOL;		
		preg_match(' /duration=(\d+)/ ', $finfo[16], $matches);
		echo '<td>' . $matches[1] . ' seconds</td>' . PHP_EOL;
		preg_match(' /origtime=(\d+)/ ', $finfo[13], $matches);				
		echo '<td>' . date("F d Y H:i:s.", $matches[1]) . '</td>' . PHP_EOL;
		echo '<td  class="center"><a href="/php/downloadvm.php?dfile=' . $fullpath . '"><img src="/sark-common/actions/down.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;									
		echo '<td  class="center"><a href=/server-vmail/default/' . $this->pkey . "/$mailbox/" . $file . '><img src="/sark-common/player_play.png" border=0 title = "Click to play" ></a></td>' . PHP_EOL; 
		$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$deletepath);
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;  	
   	
/*
 *      TAB DIVEND
 */
    echo '</div>' . PHP_EOL;	
}
}
