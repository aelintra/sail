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
require_once 'Net/IPv4.php';


Class extension {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $params = array('server' => '127.0.0.1', 'port' => '5038');
	protected $astrunning=false;

public function showForm() {
//	$params = array('server' => '127.0.0.1', 'port' => '5038');
//    print_r($_POST);	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}

	echo '<body>';
	echo '<form id="sarkextensionForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->pagename = 'Extensions';
	
	if (isset($_POST['new_x'])) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
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

	if (isset($_GET['notify'])) { 
		$this->sipNotify();
		$this->message = " - Reboot request sent to Ext" . $_GET['pkey'];
		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;			
		$this->showEdit();
		return;			
	}
	
	if (isset($_POST['reboot_x'])) { 
		$this->sipNotify();
		$this->message = " - Reboot request sent to Ext" . $_POST['pkey'];
		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
		$this->showEdit();
		return;		
	}	
			
	if (isset($_POST['upload_x'])) { 
		$this->sipNotifyPush();
		$this->message = " - Config request pushed to Ext" . $_POST['pkey'];
		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
		$this->showEdit();
		return;			
	}
	
	if (isset($_POST['save_x'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (isset($_POST['update_x'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit($_POST['pkey']);
			return;
		}					
	}

	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
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
/*
 * sign on to the AMI and build a peer array
 */
		
	$sip_peers = array(); 	
	if ( $this->astrunning ) {			
		$ami = new ami($this->params);
		$amiconrets = $ami->connect();
		if ( !$amiconrets ) {
			$this->myPanel->msg .= "  (AMI Connect failed)";
		}
		else {
			$ami->login('sark','mysark');
			$amisiprets = $ami->getSipPeers();
			$sip_peers = $this->build_peer_array($amisiprets);
			$ami->logout();
		}
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}

/* 
 * start page output
 */
  
	$res = $this->dbh->query("SELECT PROXY FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$proxy = $res['PROXY'];
	echo '<div class="buttons">';
	$ret = $this->helper->getLc(); 
	if (!$ret) {	
		$this->myPanel->Button("new");
	}
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();

	echo '<div class="datadivmax">';
	
	echo '<table class="display" id="extensiontable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('ext'); 	
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('uname');
	$this->myPanel->aHeaderFor('device');
	$this->myPanel->aHeaderFor('macaddr');
	$this->myPanel->aHeaderFor('ipaddr');
	$this->myPanel->aHeaderFor('headlocation');	
	$this->myPanel->aHeaderFor('Ping');
	$this->myPanel->aHeaderFor('sndcreds');
	$this->myPanel->aHeaderFor('bt');
//	$this->myPanel->aHeaderFor('push');
	$this->myPanel->aHeaderFor('tstate');	
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');	
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("ipphone","select ip.*, de.noproxy from ipphone ip inner join device de on ip.device=de.pkey");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="center">' . $row['pkey'] . '</td>' . PHP_EOL;		
		echo '<td >' . $row['cluster'] . '</td>' . PHP_EOL;
		
		$display = $row['desc'];
		if ( strlen($row['desc']) > 7 ) {
			$display = substr($row['desc'] , 0, 5);
			$display .= '.';
		}			 
		echo '<td  title = "' . $row['desc'] . '" >' . $display  . '</td>' . PHP_EOL;
		
		$display = $row['device'];
		preg_match('/^(\w+)\s+/',$display,$matches);
		if (isset($matches[1])) {
			if (strlen($matches[1]) > 8) {
//		if ( strlen($row['device']) > 8 ) {
				$display = substr($row['device'] , 0, 7);
				$display .= '.';
			}
			else {
				$display = $matches[1];
			}
		}
		echo '<td  title = "' . $row['device'] . '" >' . $display  . '</td>' . PHP_EOL;	
		
		$display_macaddr = 'N/A';
		if (!empty ($row['macaddr'])) {
			$display_macaddr = $row['macaddr'];
		}		
		echo '<td >' . $display_macaddr . '</td>' . PHP_EOL;
		
		$display_ipaddr = 'N/A';		
		if ($row['technology'] != 'SIP') {
			$display_ipaddr = $row['technology'];
		}		
		else if (isset ($sip_peers [$row['pkey']]['IPaddress']) && $sip_peers [$row['pkey']]['IPaddress'] == '-none-') {		
			if (preg_match(' /(..)(..)(..)(..)(..)(..)/ ',$row['macaddr'],$matches)) {
				$formalmac = strtoupper($matches[1] . ':' . $matches[2] . ':' . $matches[3] . ':' . $matches[4] . ':' . $matches[5] . ':' . $matches[6]);		
				$mac = `/bin/grep $formalmac /proc/net/arp`;			
				if (!empty ($mac)) {
					preg_match(' /^(\d+\.\d+\.\d+\.\d+)/ ',$mac,$match);
					$display_ipaddr = $match[1];
				}
			}
		}
		else if (isset ($sip_peers [$row['pkey']]['IPaddress'])) {
			$display_ipaddr = $sip_peers [$row['pkey']]['IPaddress'];
		}		
		
		echo '<td >' . $display_ipaddr . '</td>' . PHP_EOL;
		echo '<td >' . $row['location'] . '</td>' . PHP_EOL;
		
		$latency = 'N/A';
		if (isset($sip_peers [$row['pkey']]['Status'])) {
			if (preg_match(' /\((\d+)\sms/ ',$sip_peers [$row['pkey']]['Status'],$matches)) {
				$latency = 	$matches[1] . 'ms';
			}
		}	
		echo '<td >' . $latency . '</td>' . PHP_EOL;
		echo '<td >' . $row['sndcreds'] . '</td>' . PHP_EOL;
		
		if ($row['technology'] == 'SIP' && $latency != 'N/A') {		
			$get = '?notify=yes&amp;pkey=';
			$get .= $row['pkey'];	
			$this->myPanel->notifyClick($_SERVER['PHP_SELF'],$get);	
		}
		else {
			echo '<td class="center" >N/A</td>' . PHP_EOL;
		}
/*				
		if ($row['technology'] == 'SIP' && preg_match('/^Snom/', $row['device']) && $latency != 'N/A' ) {		
			$get = '?notifypush=yes&amp;pkey=';
			$get .= $row['pkey'];	
			$this->myPanel->notifypushClick($_SERVER['PHP_SELF'],$get);	
		}
		else {
			echo '<td class="center">N/A</td>' . PHP_EOL;
		}
*/				
		if (isset($matches[1]) && is_numeric($matches[1])) {
			if ($row['location'] == "remote" || $proxy == "NO" || $row['noproxy']  ) {
                echo '<td class="center"><img src="/sark-common/actions/apply.png" border=0 title = "Device is on-line" ></td>' . PHP_EOL;		
			}
			else {
				echo '<td class="center"><a href="/DPRX' . $display_ipaddr . '/"><img src="/sark-common/connected.png" border=0 title = "Device is online and may accept proxy login"></a></td>' . PHP_EOL;	
			} 
		}
		else {
			if ($row['technology'] != 'SIP') {
				echo '<td class="center"><img src="/sark-common/actions/apply.png" border=0 title = "Device is on-line" ></td>' . PHP_EOL;
			}
			else {
				if ($display_ipaddr != 'N/A') {
					echo '<td class="center"><a href="/DPRX' . $display_ipaddr . '/"><img src="/sark-common/warning.png" border=0 title = "Device is online but not registered (this may be OK)"></a></td>' . PHP_EOL;
				}
				else { 
					echo '<td class="center"><img src="/sark-common/actions/no.png" border=0 title = "Device is Offline. Satus is ' . $sip_peers [$row['pkey']]['Status'] . '" ></td>' . PHP_EOL;
				}
			}
		}
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];
		$get .= '&amp;latency=';
		$get .= $latency;	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);
		echo '</tr>'. PHP_EOL;

	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';	
}

private function showNew() {
	$this->myPanel->msg .= "Add an Extension "; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->Button("save");
	echo '</div>';			
	
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	
// find the next available ext#
	$pkey = $this->helper->getNextFreeExt();
	
	$res = $this->dbh->query("SELECT pkey from device WHERE (technology='SIP' OR technology='IAX2' OR technology='Custom') AND legacy IS NULL ORDER BY pkey");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$devices = $res->fetchAll(); 
	
	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('rule');
	echo '<input type="text" name="pkey" size="4" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('device');
	$this->myPanel->popUp('device', $devices);
	$this->myPanel->aLabelFor('devicerec');
	$this->myPanel->popUp('devicerec', array('default','None','OTR','OTRR','Inbound','Outbound','Both'));
	$this->myPanel->aLabelFor('vmailfwd');
	echo '<input type="text" name="vmailfwd" id="vmailfwd" size="20"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('macaddr');
	echo '<input type="text" name="macaddr" id="macaddr" size="14"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('calleridname');
	echo '<input type="text" name="desc" id="desc"  value="Ext' . $pkey . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('location');
	$this->myPanel->popUp('location', array('local','remote'));
	$this->myPanel->aLabelFor('cluster');
	$this->myPanel->displayCluster();
	$this->myPanel->aLabelFor('extalert');
	echo '<input type="text" name="extalert" id="extalert" size="20"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('callerid');
	echo '<input type="text" name="callerid" id="callerid" size="10"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('cdialstring');
	echo '<input type="text" name="dialstring" id="dialstring" size="20"  />' . PHP_EOL;
	echo '</div>';	
}

private function saveNew() {
// save the data away
	
	$tuple = array();
	
	$res = $this->dbh->query("SELECT EXTLEN,ACL,PWDLEN FROM globals WHERE pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$acl = $res['ACL'];
	$extlen = $res['EXTLEN'];
	$pwdlen = $res['PWDLEN'];
	if (!$pwdlen) {
		$pwdlen=8;
	}
	
	$this->validator = new FormValidator();
	$this->validator->addValidation("pkey","req","Please fill in extension number");
	$this->validator->addValidation("pkey","num","extension number must be numeric");	
    $this->validator->addValidation("vmailfwd","email","Invalid email address format");
    $this->validator->addValidation("callgroup","alnum","Call Group name must be alphanumeric(no spaces)");  
    $this->validator->addValidation("macaddr","regexp=/^[0-9a-fA-F]{12}|\s*$/","Mac address is invalid");  

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
		
		$tuple['pkey'] 			= strip_tags($_POST['pkey']);
		$tuple['device'] 		= $_POST['device'];
		$tuple['devicerec'] 	= $_POST['devicerec'];
		$tuple['vmailfwd'] 		= strip_tags($_POST['vmailfwd']);
		$tuple['dvrvmail']		= $tuple['pkey'];
		if (isset($_POST['macaddr'])) {		
			$tuple['macaddr']		= strip_tags($_POST['macaddr']);
		}
		if (isset ($_POST['desc'])) {
			if ($_POST['desc']) {
				$tuple['desc']	= strip_tags($_POST['desc']);			
			}
			else {
				$tuple['desc']	= "Ext".$tuple['pkey'];
			}
		}	
				
		$tuple['location'] 		= $_POST['location'];		

		if (isset ($_POST['cluster'])) {
			$tuple['cluster']	= strip_tags($_POST['cluster']);
		}
		else {
			$tuple['cluster']	='default';
		}		
		$tuple['extalert']		= strip_tags($_POST['extalert']);
		
		if (isset ($_POST['callerid'])) {
			$tuple['callerid']	= strip_tags($_POST['callerid']);
		}
		else {
			$tuple['callerid']	= $tuple['pkey'];
		}
				
		$tuple['dialstring']	= strip_tags($_POST['dialstring']);
		
		$resdevice 		= $this->dbh->query("SELECT sipiaxfriend,technology,blfkeyname FROM device WHERE pkey = '" . $tuple['device'] . "'")->fetch(PDO::FETCH_ASSOC);
		$tuple['sipiaxfriend'] 	= $resdevice['sipiaxfriend'];
		if ($resdevice['technology'] == 'SIP') {
		// special code to encapsulate cisco XML - not nice - should be data driven
			if (preg_match( '/^[Cc]isco/',$tuple['device'])) {
				$tuple['provision']	= "<flat-profile>\n";
			}
			$tuple['provision']	.= "#INCLUDE " . $tuple['device'];
			
			if (!preg_match('/^[Pp]olycom/', $tuple['device']) ) {
				if ( $resdevice['blfkeyname'] ) {
					$tuple['provision']	.= "\n#INCLUDE " . $resdevice['blfkeyname'];
				}
			}
		// special code to encapsulate cisco XML - not nice - should be data driven
			if (preg_match( '/^[Cc]isco/',$tuple['device'])) {
				$tuple['provision']	.= "\n</flat-profile>\n";
			}			
		}
		$tuple['technology']	= $resdevice['technology'];			
		$password 		= $this->helper->ret_password ($pwdlen);
		$tuple['passwd']	=  $password;
			
//		$localip = $this->helper->ret_localip ();
/*			
		$astclistr = 'callerid="$desc" <$ext>';
		if ( preg_match(' /callerid=/ ',$tuple['sipiaxfriend']))	{			
			$tuple['sipiaxfriend'] = preg_replace ( '/callerid=/', $astclistr, $tuple['sipiaxfriend']);
		}
		else {
			$tuple['sipiaxfriend'] .= "\n";
			$tuple['sipiaxfriend'] .= $astclistr;
		}
			
		$astuser = 'username=$desc';	
		if ( preg_match(' /username=/ ',$tuple['sipiaxfriend']))	{			
			$tuple['sipiaxfriend'] = preg_replace ( '/username=/', $astuser, $tuple['sipiaxfriend']);
		}
		else {
			$tuple['sipiaxfriend'] .= "\n";
			$tuple['sipiaxfriend'] .= $astuser;
		}	
			
		$astpassword = 'secret=$password';	
		if ( preg_match(' /secret=/ ',$tuple['sipiaxfriend'])) {				
			$tuple['sipiaxfriend'] = preg_replace ( '/secret=/', $astpassword, $tuple['sipiaxfriend']);
		}
		else {
			$tuple['sipiaxfriend'] .= "\n";
			$tuple['sipiaxfriend'] .= $astpassword;
		}	
			
		$astmailbox = 'mailbox=' .  $tuple['pkey'];				
		if ( preg_match(' /mailbox=/ ',$tuple['sipiaxfriend']))	{			
			$tuple['sipiaxfriend'] = preg_replace ( '/mailbox=/', $astmailbox, $tuple['sipiaxfriend']);
		}
		else {
			$tuple['sipiaxfriend'] .= "\n";
			$tuple['sipiaxfriend'] .= $astmailbox;
		}
			
		if ( preg_match(' /pickupgroup=/ ',$tuple['sipiaxfriend']))	{			
			$tuple['sipiaxfriend'] = preg_replace ( '/pickupgroup=/', 'pickupgroup=1', $tuple['sipiaxfriend']);
		}
		else {
			$tuple['sipiaxfriend'] .= "\npickupgroup=1";
		}
		if ( preg_match(' /callgroup=/ ',$tuple['sipiaxfriend']))	{			
			$tuple['sipiaxfriend'] = preg_replace ( '/callgroup=/', 'callgroup=1', $tuple['sipiaxfriend']);
		}
		else {
			$tuple['sipiaxfriend'] .= "\ncallgroup=1";
		}
*/
		if ($acl == 'YES' && $tuple['location'] == 'local') {
			$ret = $this->helper->request_syscmd ("ifconfig eth0 | grep 'inet addr:' | cut -d: -f2 | awk '{ print $1}'");
			$ipaddr = trim(preg_replace('/<<EOT>>$/', '', $ret));	
			$ret = $this->helper->request_syscmd ("ifconfig eth0 | grep 'inet addr:' | cut -d: -f4 | awk '{ print $1}'");
			$netmask = trim(preg_replace('/<<EOT>>$/', '', $ret));
			$ip_calc = new Net_IPv4();
			$ip_calc->ip = $ipaddr;
			$ip_calc->netmask = $netmask;
			$ipcalcret = $ip_calc->calculate();
			$network = $ip_calc->network;
			if ( !preg_match(' /deny=/ ',$tuple['sipiaxfriend'])) {
				$tuple['sipiaxfriend'] .= "\ndeny=0.0.0.0/0.0.0.0";
			}
			if ( !preg_match(' /permit=/ ',$tuple['sipiaxfriend'])) {
				$tuple['sipiaxfriend'] .= "\npermit=" . $network. '/' . $netmask;
			}				
		}
/*
		if ( !preg_match(' /call-limit/ ',$tuple['sipiaxfriend'])) {
			$tuple['sipiaxfriend'] .= "\ncall-limit=3";
		}
		if ( !preg_match(' /subscribecontext/ ',$tuple['sipiaxfriend'])) {
			$tuple['sipiaxfriend'] .= "\nsubscribecontext=extensions";
		}
		$tuple['sipiaxfriend'] .= "\ndisallow=all ";
		$tuple['sipiaxfriend'] .= "\nallow=alaw\nallow=ulaw ";
*/
		if ($tuple['location'] == 'remote') {
			$tuple['sipiaxfriend'] .= "\nnat=yes";	
		}
		$tuple['sipiaxfriend'] = trim($tuple['sipiaxfriend']);

		
		$sql = "SELECT * FROM COS ORDER BY pkey";
		foreach ($this->dbh->query($sql) as $cos) {
			if ($cos['defaultopen'] == 'YES') {
				$res=$this->dbh->prepare('INSERT INTO IPphoneCOSopen(IPphone_pkey,COS_pkey) VALUES(?,?)');
				$res->execute(array( $tuple['pkey'],$cos['pkey'] ));
			}
			if ($cos['defaultclosed'] == 'YES') {
				$res=$this->dbh->prepare('INSERT INTO IPphoneCOSclosed(IPphone_pkey,COS_pkey) VALUES(?,?)');
				$res->execute(array( $tuple['pkey'],$cos['pkey'] ));
				
			}			
		}
/*
		if ( $resdevice['blfkeys'] ) {
			for ($seq = 1; $seq <= $resdevice['blfkeys']; $seq++) {
				$res=$this->dbh->prepare('INSERT INTO IPphone_Fkey(pkey,seq,type,label,value) VALUES(?,?,?,?,?)');
				$res->execute(array( $tuple['pkey'],$seq,'None','None','None'));	
			}
		}
*/				
		$ret = $this->helper->createTuple("ipphone",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved new extension " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['exteninsert'] = $ret;	
		}	
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function saveNewBlf() {
// save the data away
	$pkey = $_POST['pkey'];
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $pkey . '" />' . PHP_EOL;
	echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
	
	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $pkey . "'")->fetchColumn();
	$seq++;
	$res=$this->dbh->prepare('INSERT INTO ipphone_fkey(pkey,seq,type,label,value) VALUES(?,?,?,?,?)');
	$res->execute(array( $pkey,$seq,'Default','None','None'));
	
}

private function deleteLastBlf() {
// save the data away
	$pkey = $_POST['pkey'];
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $pkey . '" />' . PHP_EOL;
	echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
	
	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $pkey . "'")->fetchColumn();
	if ($seq) {
		$res=$this->dbh->prepare('DELETE FROM ipphone_fkey WHERE pkey=? AND seq=?');
		$res->execute(array( $pkey,$seq));
	}
	
}

private function showEdit($key=False) {

	if ($key != False) {
		$pkey=$key;
	}
	else if (isset($_POST['pkey'])) {
		$pkey = $_POST['pkey'];
	}
	else {
		$pkey = $_GET['pkey'];
	}
	if (isset($_GET['latency'])) {
		$latency = $_GET['latency'];
	}
	elseif (isset($_POST['latency'])) {
		$latency = $_POST['latency'];
	}		
	else {
		$latency = 'N/A';
	}
	$extension = $this->dbh->query("SELECT * FROM ipphone WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
	$extlist=array();
	array_push($extlist,"None");	
	$res = $this->helper->getTable("ipphone","select pkey from ipphone ORDER BY pkey",false);
	foreach ($res as $row) {
		array_push($extlist,$row['pkey']);
	}
	
	$classOfService = $this->helper->getTable("cos",$sql='',$filter=false);
	
	$printline = "Ext " . $extension['technology'] . "/" . $extension['pkey'] . ', "' . $extension['desc'] . '"(' . $extension['device'] . ')';
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
			$cfim = $ami->GetDB('cfim', $pkey);
			$cfbs = $ami->GetDB('cfbs', $pkey);
			$ringdelay = $ami->GetDB('ringdelay', $pkey);
			$ami->logout();
		}
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}
	
	$xref = $this->xRef($pkey);
	
	echo '<div class="buttons">';
	
	$this->myPanel->Button("cancel");
	$this->myPanel->override="update";
	$this->myPanel->Button("save");
	$this->myPanel->override="newblf";
	$this->myPanel->buttonName["new"]["title"] = "Add a new BLF key";
	$this->myPanel->Button("new");
	$this->myPanel->override="delblf";
	$this->myPanel->overrideClick="";
	$this->myPanel->buttonName["delete"]["title"] = "Delete the last BLF key";
	$this->myPanel->Button("delete");			
	
	if ($latency != 'N/A') {
		$this->myPanel->buttonName["reboot"]["title"] = "Send a reboot request to the endpoint";
		$this->myPanel->Button("reboot");
		if (preg_match('/^Snom/',$extension['device'])) {
			$this->myPanel->buttonName["upload"]["title"] = "Send config without reboot(Snom only)";
			$this->myPanel->Button("upload");
		}
	} 
	
	echo '</div>';	
		
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
	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		if (isset($extension['macaddr'])) {
			if (preg_match(' /\.[FL]key/m ', $extension['provision'])) {
				echo  '<li><a href="#blf">BLF/DSS Keys</a></li>' . PHP_EOL;
			}
		}
		echo  '<li><a href="#vmail">Vmail</a></li>' . PHP_EOL;
	}
	if (! empty ($classOfService)) {
		echo '<li><a href="#cos" >CoS</a></li>' . PHP_EOL;
	}
	if ( $this->astrunning ) {
		if ( $amiconrets ) {
			echo '<li><a href="#cfwd" >CFWD</a></li>' . PHP_EOL;
		}
	}
    echo '<li><a href="#xref" >XREF</a></li>' . PHP_EOL;
	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {		
		if ( $_SERVER['REMOTE_USER'] == 'admin' ) {
			echo  '<li><a href="#asterisk">Asterisk</a></li>' . PHP_EOL;
			if (isset($extension['macaddr'])) {
				echo  '<li><a href="#provisioning">Provisioning</a></li>' . PHP_EOL;
			}						
		}
	}    
    echo '</ul>' . PHP_EOL;
    
/*
 *   TAB Provisioning
 */
    if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		if ( $_SERVER['REMOTE_USER'] == 'admin' ) {
			if (isset($extension['macaddr'])) {
				echo '<div id="provisioning" >';
				echo '<textarea class="databox" name="provision" id="provision">' . htmlspecialchars($extension['provision']) . '</textarea>' . PHP_EOL;
				echo '</div>' . PHP_EOL;
			}
		}
	}
	
/*
 *   TAB Asterisk
 */
	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		if ( $_SERVER['REMOTE_USER'] == 'admin' ) {
			echo '<div id="asterisk" >';
			echo '<textarea class="databox" name="sipiaxfriend" id="sipiaxfriend">' . htmlspecialchars($extension['sipiaxfriend']) . '</textarea>' . PHP_EOL;
			echo '</div>' . PHP_EOL;
		}
	}
	
/*
 * 	TAB General
 */  
    echo '<div id="general" >';
    
    $this->myPanel->aLabelFor('rule');
	echo '<input type="text" name="newkey" size="4" id="newkey" value="' . $extension['pkey'] . '"  />' . PHP_EOL;
	
	$this->myPanel->aLabelFor('calleridname');
	echo '<input type="text" name="desc" id="desc" value="' . $extension['desc'] . '" />' . PHP_EOL;

	$this->myPanel->aLabelFor('password');
	echo '<input type="text" name="passwd" id="passwd" value="' . $extension['passwd'] . '" />' . PHP_EOL;	

	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		$this->myPanel->aLabelFor('macaddr');
		echo '<input type="text" name="macaddr" id="macaddr" size="14" ';
		
			if ( preg_match(' /VXT/ ',$extension['device']))	{
			echo 'style = "background-color: lightgrey" readonly="readonly" '; 
			 
		}
		echo 'value="' . $extension['macaddr'] . '"  />' . PHP_EOL;
	}
    
	
	$this->myPanel->aLabelFor('cluster');
	$this->myPanel->selected = $extension['cluster'];
	$this->myPanel->displayCluster();
 	$this->myPanel->aLabelFor('devicerec');
 	$this->myPanel->selected = $extension['devicerec'];
 	$this->myPanel->popUp('devicerec', array('default','None','OTR','OTRR','Inbound','Outbound','Both'));	
	if ($extension['technology'] == 'SIP' ) {
		$this->myPanel->aLabelFor('location');
		$this->myPanel->selected = $extension['location'];
		$this->myPanel->popUp('location', array('local','remote'));
	}	

	$this->myPanel->aLabelFor('callerid');
	echo '<input type="text" name="callerid" id="callerid" size="10"  value="' . $extension['callerid'] . '"  />' . PHP_EOL;				
	if ($extension['technology'] == 'SIP' ) {
		$this->myPanel->aLabelFor('extalert');
		echo '<input type="text" name="extalert" id="extalert" size="40"  value="' . $extension['extalert'] . '"  />' . PHP_EOL;
	}	
	if ($extension['technology'] == 'Custom' ) {
		$this->myPanel->aLabelFor('cdialstring');
		echo '<input type="text" name="cdialstring" id="cdialstring" size="10"  value="' . $extension['cdialstring'] . '"  />' . PHP_EOL;
	}
	$this->myPanel->aLabelFor('ringdelay');
    echo '<input type="text" name="ringdelay" id="ringdelay" size="2"  value="' . $ringdelay . '"  />' . PHP_EOL;
    			
	echo '</div>' . PHP_EOL;
	
/*
 * 	TAB BLF/DSS Keys
 */  
	if (isset($extension['macaddr'])) {
		if (preg_match(' /\.[FL]key/m ', $extension['provision'])) {
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
		$sql = "select * from Ipphone_FKEY where pkey='" . $pkey . "'";
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
	}	
	
/*
 * 	TAB COS
 */ 
	if (! empty ($classOfService)) {
		echo '<div id="cos"  >' . PHP_EOL;	
		echo '<h2>Day-time Class of Service</h2>' . PHP_EOL;	
    
		foreach ($classOfService as $cos) {
			$sql = "SELECT IPphone_pkey FROM IPphoneCOSopen where IPphone_pkey = '" . $extension['pkey'] . "' and COS_pkey = '" . $cos['pkey'] . "'";
			$cosrec = $this->dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
			if (is_array($cosrec) && array_key_exists('IPphone_pkey',$cosrec)) {
				echo '<input type="checkbox" checked="yes" name="opencos[]" value="' . $cos['pkey'] . '" />' . $cos['pkey'] . '<br/>' . PHP_EOL;	 
			}
			else {
				echo '<input type="checkbox" name="opencos[]" value="' . $cos['pkey'] . '" />' . $cos['pkey'] . '<br/>' . PHP_EOL;			
			}
		}
		echo '<h2>Night-time Class of Service</h2>' . PHP_EOL;	
		foreach ($classOfService as $cos) {
			$sql = "SELECT IPphone_pkey FROM IPphoneCOSclosed where IPphone_pkey = '" . $extension['pkey'] . "' and COS_pkey = '" . $cos['pkey'] . "'";
			$cosrec = $this->dbh->query($sql)->fetch(PDO::FETCH_ASSOC);
			if (is_array($cosrec) && array_key_exists('IPphone_pkey',$cosrec)) {
				echo '<input type="checkbox" checked="yes" name="closedcos[]" value="' . $cos['pkey'] . '" />' . $cos['pkey'] . '<br/>' . PHP_EOL;	 
			}
			else {
				echo '<input type="checkbox" name="closedcos[]" value="' . $cos['pkey'] . '" />' . $cos['pkey'] . '<br/>' . PHP_EOL;			
			}
		}
		echo '</div>' . PHP_EOL;
	}

	
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
 * 	TAB Vmail
 */ 
	echo '<div id="vmail"  >' . PHP_EOL;
	

	$this->myPanel->aLabelFor('vmailfwd');
	echo '<input type="text" name="vmailfwd" id="vmailfwd" size="30"  value="' . $extension['vmailfwd'] . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('dvrvmail');	
	$this->myPanel->selected = $extension['dvrvmail'];
	$this->myPanel->popUp('dvrvmail', $extlist);	    
	$this->myPanel->aLabelFor('vdelete');
    echo '<input type="checkbox"   name="vdelete" value="vdelete" />'; 
	$this->myPanel->aLabelFor('vreset');
    echo '<input type="checkbox"   name="vreset" value="vreset" />';
	
	echo '</div>' . PHP_EOL;
	
/*
 * 	TAB XREF 
 */ 
	echo '<div id="xref"  >' . PHP_EOL;
	echo '<h2>Cross References to this extension</h2>' . PHP_EOL;
    echo '<p>' . $xref . '</p>' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	
	echo '</div>';
	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $pkey . '"  />' . PHP_EOL;
	if ($latency != 'N/A') {
		echo '<input type="hidden" name="latency" id="latency" size="20"  value="' . $latency . '"  />' . PHP_EOL;
	} 	
	echo '</div>';			
}

private function saveEdit() {
// save the data away

	$tuple = array();
		
	$this->validator = new FormValidator();
	
	$this->validator->addValidation("newkey","num","Invalid extension number");
	$this->validator->addValidation("newkey","req","You must specify an extension number");
	$this->validator->addValidation("newkey","minlen=3","Invalid minimum extension number");
	$this->validator->addValidation("newkey","maxlen=4","Invalid maximum extension number");
    $this->validator->addValidation("vmailfwd","email","Invalid email address format");
    $this->validator->addValidation("callgroup","alnum","Call Group name must be alphanumeric(no spaces)");  
    $this->validator->addValidation("macaddr","regexp=/^[0-9a-fA-F]{12}|\s*$/","Mac address is invalid"); 
    $this->validator->addValidation("desc","regexp=/^[0-9a-zA-Z_-]+$/","Caller name is invalid - must be [0-9a-zA-Z_-]"); 
    $this->validator->addValidation("passwd","regexp=/^[0-9a-zA-Z]+$/","Password is invalid - must be [0-9a-zA-Z]"); 
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
		$newkey =  trim(strip_tags($_POST['newkey']));

/*
 * local/remote processing
 */ 
		if (preg_match( " /nat=yes/ ", $tuple['sipiaxfriend']) ) {
			if ($tuple['location'] == 'local') {
				$tuple['sipiaxfriend'] = trim(str_replace("nat=yes", "", $tuple['sipiaxfriend']));
			}
		}
		else {
			if ($tuple['location'] == 'remote') {
				$tuple['sipiaxfriend'] .= "\nnat=yes";
			}
		}


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
						$ami->PutDB('cfim', $newkey, $cfim);
					}
					else {
						$ami->DelDB('cfim', $newkey);
					}
				}
				if (isset($_POST['cfbs'])) {
					$cfbs			= strip_tags($_POST['cfbs']);
					if ($cfbs) {
						$ami->PutDB('cfbs', $newkey, $cfbs);
					}
					else {
						$ami->DelDB('cfbs', $newkey);
					}					
				}
				if (isset($_POST['ringdelay'])) {
					$ringdelay		= strip_tags($_POST['ringdelay']);	
					$ami->PutDB('ringdelay', $newkey, $ringdelay);				
				}					
				$ami->logout();
			}
		}
/*
 * reset/empty voicemail if requested
 */

	if (isset($_POST['vdelete'])) { 
		$rc = $this->helper->request_syscmd ("/bin/rm -rf /var/spool/asterisk/voicemail/default/" . $_POST['pkey']."/*");	
		$this->message = "Voicemail for Ext " . $_POST['pkey'] . " deleted";
	}	
	
	if (isset($_POST['vreset'])) { 
		$skey = $_POST['pkey'];
		$rc = $this->helper->request_syscmd ("/bin/sed -i 's/^$skey => [0-9]*\(.*\)/$skey => $skey\\1/' /etc/asterisk/voicemail.conf");	
		$this->message = "Voicemail password for Ext " . $_POST['pkey'] . " reset";	
	}
		
/*
 * update the SQL database
 */
 
// remove any escaped quotes 			
		$tuple['provision'] = preg_replace ( "/\\\/", '', $tuple['provision']);
		$tuple['sipiaxfriend'] = preg_replace ( "/\\\/", '', $tuple['sipiaxfriend']);
		$this->doCos();
		

/*
 * check for keychange
 */
		if ($newkey != $tuple['pkey']) {
			$res = $this->dbh->query("SELECT pkey FROM ipphone WHERE pkey = '" . $newkey . "'")->fetch(PDO::FETCH_ASSOC);
			if ( isset($res['pkey']) ) { 
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['extensave'] = " Attempt to change extension key but " . $newkey . " already exists!";	
			}
			else {
				// set the mailbox to the new extension
				$tuple['dvrvmail'] = $newkey;
				$this->chkMailbox($tuple['dvrvmail'],$tuple['sipiaxfriend']);
				$ret = $this->helper->setTuple("ipphone",$tuple,$newkey);
				if ($ret == 'OK') {
					$this->message = "Updated extension ";
					// move any mail
					$maildir = '/var/spool/asterisk/voicemail/default/'.$tuple['pkey'];
					if (is_dir($maildir)) {
						$oldmail = '/var/spool/asterisk/voicemail/default/'.$tuple['pkey'];
						$newmail = '/var/spool/asterisk/voicemail/default/'.$newkey;
						$this->helper->request_syscmd ("/bin/mv $oldmail $newmail");
					}
				}
				else {
					$this->invalidForm = True;
					$this->message = "<B>  --  Validation Errors!</B>";	
					$this->error_hash['extensave'] = $ret;	
				}
			}
		}
		else {
			$this->chkMailbox($tuple['dvrvmail'],$tuple['sipiaxfriend']);		
			$ret = $this->helper->setTuple("ipphone",$tuple,$newkey);
			if ($ret == 'OK') {
				$this->message = "Updated extension ";
			}
			else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['extensave'] = $ret;	
			}
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function doCOS() {
# Do the Booleans
	$tuple = array();

/*
 * delete the existing rows (if any)
 */ 
	 $this->helper->predDelTuple("IPphoneCOSopen","IPphone_pkey",$_POST['pkey']);
	 $this->helper->predDelTuple("IPphoneCOSclosed","IPphone_pkey",$_POST['pkey']);
/*
 * add these rows
 */ 	
	if ( isset ($_POST['opencos']) && is_array($_POST['opencos'])) {
		foreach ( $_POST['opencos'] as $ocos ) {
			$tuple['IPphone_pkey'] = $_POST['pkey'];
			$tuple['COS_pkey'] = $ocos;
			$ret = $this->helper->createTuple("IPphoneCOSopen",$tuple,false);
			if ($ret != 'OK') {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['opencos'] = $ret;	
			}
		}
	}
	if ( isset($_POST['closedcos']) && is_array($_POST['closedcos'])) {
		foreach ( $_POST['closedcos'] as $ccos ) {
			$tuple['IPphone_pkey'] = $_POST['pkey'];
			$tuple['COS_pkey'] = $ccos;
			$ret = $this->helper->createTuple("IPphoneCOSclosed",$tuple,false);
			if ($ret != 'OK') {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['opencos'] = $ret;	
			}
		}
	}		
}

private function xRef($pkey) {
/*
 * Build Xrefs
 */
	$xref = '';
	$tref = '';
	$sql = "SELECT * FROM IPphone WHERE dvrvmail LIKE '" . $pkey . "' AND pkey !=  '" . $pkey . "' ORDER BY pkey";
	foreach ($this->dbh->query($sql) as $row) {
//		if (!$row == $pkey) {
//			if (!$row['dvrvmail'] == $pkey) {
				$tref .= "This extension provides a voicemailbox for $pkey<br>" . PHP_EOL;
//			}
//		}
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No other extensions reference this extension's voicemail box<br/>" . PHP_EOL;
    }
    
	$sql = "SELECT * FROM lineio WHERE openroute LIKE '" . $pkey . "' OR closeroute LIKE '" . $pkey . "' ORDER BY pkey";
	foreach ($this->dbh->query($sql) as $row) {
		if ( $row['openroute'] == $pkey || $row['closeroute'] == $pkey ) {
                $tref .= "Trunk " . $row['pkey'] . " references this extension <br>" . PHP_EOL;
        }
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No Trunks reference this extension<br/>" . PHP_EOL;
    }  
    
 	$sql = "SELECT * FROM speed WHERE outcome LIKE '" . $pkey . "' OR out LIKE '" . $pkey . "' ORDER BY pkey";
 	foreach ($this->dbh->query($sql) as $row) {
		if ($row['pkey'] != 'RINGALL') {
			$tref .= "callgroup " . $row['pkey'] . " references this extension <br>" . PHP_EOL;
		}
	}
	
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No callgroups reference this extension<br/>" . PHP_EOL;
    }       

	$sql = "SELECT * FROM appl WHERE extcode LIKE '" . $pkey . "' ORDER BY pkey";
	foreach ($this->dbh->query($sql) as $row) {
/*
		$extcode = $row['extcode'];
		if (preg_match( "/$extcode/",$pkey)) {
*/
			$tref .= "App " . $row['pkey'] . " references this extension <br>" . PHP_EOL;
//        }
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No Apps reference this extension<br/>" . PHP_EOL;
    }  

	$sql = "SELECT * FROM ivrmenu ORDER BY pkey";
	foreach ($this->dbh->query($sql) as $row) {
		if ($row['timeout'] == $pkey) {
			$tref .= "IVR Timeout " . $row['pkey'] . " references this extension <br>" . PHP_EOL;
		}
		else {
			for ($i = 1; $i <= 11; $i++) {
				if ($row["option" . $i] == $pkey) {
					$tref .=  "IVR " . $row['pkey'] . " references this extension <br>" . PHP_EOL;
					break 1;
				}
			}
		}
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No IVRs reference this extension<br/>" . PHP_EOL;
    } 
    return $xref;  		   				
}

private function sipNotify () {
/*
 * send a notify to a phone to reboot it
 */ 
 
	// pkey arrives sometimes GET and sometimes POST
		if (isset($_GET['pkey'])) {
			$pkey = $_GET['pkey'];
		}
		else {
			$pkey = $_POST['pkey'];
		}

		$res = $this->dbh->query("SELECT technology,device  FROM IPphone WHERE pkey ='" . $pkey . "'" )->fetch(PDO::FETCH_ASSOC);
		
		if ($res['technology'] != 'SIP') {
			$this->message = "Ext " . $pkey . " is not a SIP UA!!.";
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
		if (preg_match ( " /Panasonic/ ", $res['device'])) {	
			$chk = 'panasonic-check-cfg';
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
			$this->message = "No notify data available for ext $pkey ";
			return;
		}
		$this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'sip notify $chk $pkey' ");
    	$this->message = "Issued SIP Notify to Ext " . $pkey . "(" . $res['device'] . ")" ;
}

private function sipNotifyPush () {
/*
 * send a notify to a phone to reboot it
 */ 
		if (isset($_GET['pkey'])) {
			$pkey = $_GET['pkey'];
		}
		else {
			$pkey = $_POST['pkey'];
		}

		$res = $this->dbh->query("SELECT technology,device  FROM IPphone WHERE pkey ='" . $pkey . "'" )->fetch(PDO::FETCH_ASSOC);
		
		if ($res['technology'] != 'SIP') {
			$this->message = "Ext " . $pkey . " is not a SIP UA!!.";
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
			$this->message = "No notify data available for ext $pkey ";
			return;
		}
		else {
			$this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'sip notify $chk $pkey' ");
    	}
    	$this->message = "Issued SIP Notify to Ext " . $pkey . "(" . $res['device'] . ")" ;
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
private function chkMailbox(&$mailbox,&$friend)
{
	/*
 * check mailbox setting
 */
		$astmailbox = 'mailbox=';
		if ($mailbox != "None") {			
			$astmailbox .= $mailbox;	
		}
		if ( preg_match(' /mailbox=\$ext/ ',$friend))	{
			$friend = preg_replace ( '/mailbox=\$ext/', $astmailbox, $friend);
		}
		else if ( preg_match(' /mailbox=\d{3,4}/ ',$friend))	{	
			$friend = preg_replace ( '/mailbox=\d{3,4}/', $astmailbox, $friend);
		}
		else if ( preg_match(' /mailbox=/ ',$friend))	{	
			$friend = preg_replace ( '/mailbox=/', $astmailbox, $friend);
		}
}


}
