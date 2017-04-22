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

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkAmiHelperClass";
//require_once $_SERVER["DOCUMENT_ROOT"] . "../php/AsteriskManager.php";

require_once 'Net/IPv4.php';


Class sarkextension {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $astrunning=false;
	protected $keychange=NULL;

public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}

	echo '<div class="guest"><img src="/sark-common/buttons/cancel.png" id="closebutton" />'. PHP_EOL;
	echo '<div id="iframecontent"></div></div>' . PHP_EOL;
	echo '<form id="sarkextensionForm"  action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	$this->myPanel->pagename = 'Extensions';
	
	if (isset($_POST['new_x']) || isset($_GET['new'])) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_POST['delete_x'])) { 
		$this->deleteRow();
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
		$this->message = " - Reboot request sent";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;			
//		$this->showEdit();
//		return;			
	}
	
	if (isset($_POST['notify_x'])) { 
		$this->sipNotify();
		$this->message = " - Reboot request sent";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;			
		$this->showEdit();
		return;			
	}	
	
	if (isset($_POST['reboot_x'])) { 
		$this->sipNotify();
		$this->message = " - Reboot request sent";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
		$this->showEdit();
		return;		
	}	
			
	if (isset($_POST['upload_x'])) { 
		$this->sipNotifyPush();
		$this->message = " - Config request pushed";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
		$this->showEdit();
		return;			
	}
	
	if (isset($_POST['save_x'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
		}
		else {
			$this->showEdit();
		}
		return;					
	}
	
	if (isset($_POST['update_x'])) { 
		$this->saveEdit();
		$this->showEdit();
		return;				
	}

	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
		$this->helper->sysCommit();
		$this->message = "Updates Committed";	
	}
	
	
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}	

private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	if ( $this->astrunning ) {	
		$amiHelper = new amiHelper();
		$sip_peers = $amiHelper->get_peer_array();
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}	

/* 
 * start page output
 */
	
	$res = $this->dbh->query("SELECT PROXY,CLUSTER FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$proxy = $res['PROXY'];
	$cluster = $res['CLUSTER'];
	
	echo '<div class="titlebar">' . PHP_EOL; 
	echo '<div class="buttons">'. PHP_EOL;

	$ret = $this->helper->getLc(); 
	if (!$ret) {	
		$this->myPanel->Button("new");
	}
	$this->myPanel->commitButton();
	if ( $_SESSION['user']['pkey'] == 'admin' ) {	
		echo '<a  href="/php/downloadpdf.php?pdf=ipphone"><img id="pdfprint" src="/sark-common/buttons/print.png" border=0 title = "Click to Download PDF" ></a>' . PHP_EOL;									
	}
	echo '</div>';
	if (!empty($this->error_hash)) {
		$this->myPanel->msg = reset($this->error_hash);	
	}	
	$this->myPanel->Heading();
	echo '</div>' . PHP_EOL;
	
	if ($cluster == 'OFF') {
		echo '<div class="datadivwide">';
	}
	else {
		echo '<div class="datadivmax">';
	}
	
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
	$this->myPanel->aHeaderFor('sndcreds');
	$this->myPanel->aHeaderFor('bt');
	$this->myPanel->aHeaderFor('trns');
	$this->myPanel->aHeaderFor('tstate');
	$this->myPanel->aHeaderFor('active');	
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');	
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("ipphone","select ip.*, de.noproxy from ipphone ip inner join device de on ip.device=de.pkey");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="icons">' . $row['pkey'] . '</td>' . PHP_EOL;		
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
			if (strlen($matches[1]) > 12) {
				$display = substr($row['device'] , 0, 11);
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
		echo '<td class="icons">' . $display_macaddr . '</td>' . PHP_EOL;
		
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
		
		echo '<td class="icons">' . $display_ipaddr . '</td>' . PHP_EOL;
		echo '<td class="icons">' . $row['location'] . '</td>' . PHP_EOL;
		
		echo '<td >' . $row['sndcreds'] . '</td>' . PHP_EOL;
		
		$latency = 'N/A';
		if (isset($sip_peers [$row['pkey']]['Status'])) {
			$latency = $sip_peers [$row['pkey']]['Status'];			
			if ($row['technology'] == 'SIP' && preg_match(' /^OK/ ', $latency)) {		
				$get = '?notify=yes&amp;pkey=';
				$get .= $row['pkey'];	
				$this->myPanel->notifyClick($_SERVER['PHP_SELF'],$get);	
			}
			else {
				echo '<td class="icons" >N/A</td>' . PHP_EOL;
			}			
		}
		else {
			echo '<td class="icons" >N/A</td>' . PHP_EOL;
		}
		
		if ($row['tls'] == 'on') {
			echo '<td class="icons">TLS</td>' . PHP_EOL;
		}
		else {
			echo '<td class="icons">UDP</td>' . PHP_EOL;
		}
		
		echo '<td class="icons" border=0 title = "Device State">' . $latency . '</td>' . PHP_EOL;
		echo '<td class="icons" >' . $row['active'] . '</td>' . PHP_EOL;				

		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];
		$get .= '&amp;latency=';
		$get .= $latency;	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];
/*
 * Checkbox multi-delete.  It's actually faster to use the ajax delete in most cases because the screen doesn't re-draw.
 *   
		echo '<td class="center"><input type="checkbox"  name="delrow[]" value="' . $row['pkey'] . '" /></td>';	
*/ 	
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
	 
	echo '<div class="titlebar">' . PHP_EOL;
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
	echo '</div>';	
		
// find the next available ext#
	$pkey = $this->helper->getNextFreeExt();
	
	if (isset($_GET['vendor'])) {
		$query = "SELECT pkey from device WHERE technology='SIP' AND legacy IS NULL AND pkey LIKE '%" .  SQLite3::escapeString ($_GET['vendor']) . "%' ORDER BY pkey";
	}
	else {
		$query = "SELECT pkey from device WHERE technology='SIP' AND legacy IS NULL ORDER BY pkey";
	}
	$res = $this->dbh->query($query);  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$devices = $res->fetchAll(); 
	if (isset($_GET['vendor'])) {
		array_push($devices,'GeneralSIP');
	}
	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('rule');
	echo '<input type="text" name="pkey" size="4" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('device');
	$this->myPanel->selected = 'General SIP';
	$this->myPanel->popUp('device', $devices);
	$this->myPanel->aLabelFor('devicerec');
	$this->myPanel->popUp('devicerec', array('default','None','OTR','OTRR','Inbound','Outbound','Both'));
	$this->myPanel->aLabelFor('location');
	$this->myPanel->popUp('location', array('local','remote'));	
	$this->myPanel->aLabelFor('vmailfwd');
	echo '<input type="text" name="vmailfwd" id="vmailfwd" size="30"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('macaddr');
	echo '<input type="text" name="macaddr" id="macaddr" size="30" ';	
	if (isset($_GET['mac'])) {
		echo 'value="' . $_GET['mac'] . '"';
	}
	echo ' />' . PHP_EOL;
	$this->myPanel->aLabelFor('calleridname');
	echo '<input type="text" name="desc" id="desc" size="30"  value="Ext' . $pkey . '"  />' . PHP_EOL;

	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->displayCluster();
	$this->myPanel->aLabelFor('extalert');
	echo '<input type="text" name="extalert" id="extalert" size="30"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('callerid');
	echo '<input type="text" name="callerid" id="callerid" size="30"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('cdialstring');
	echo '<input type="text" name="dialstring" id="dialstring" size="30"  />' . PHP_EOL;
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
		
//		$resdevice 		= $this->dbh->query("SELECT sipiaxfriend,technology,blfkeyname FROM device WHERE pkey = '" . $tuple['device'] . "'")->fetch(PDO::FETCH_ASSOC);
		$sql = $this->dbh->prepare("SELECT sipiaxfriend,technology,blfkeyname FROM device WHERE pkey = ?");
		$sql->execute(array($tuple['device']));
		$resdevice = $sql->fetch();

		$tuple['sipiaxfriend'] 	= $resdevice['sipiaxfriend'];
		if ($resdevice['technology'] == 'SIP') {
		// special code to encapsulate cisco XML - not nice - should be data driven
/*
			if (preg_match( '/^[Cc]isco/',$tuple['device'])) {
				$tuple['provision']	= "<?xml version="1.0" encoding="UTF-8"?>\n";
				$tuple['provision']	.= "\n<device xsi:type="axl:XIPPhone" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">\n";
				$tuple['provision']	.= "\n<flat-profile>\n";
			}
*/
			$tuple['provision']	.= "#INCLUDE " . $tuple['device'];
			
			if (!preg_match('/^[Pp]olycom/', $tuple['device']) ) {
				if ( $resdevice['blfkeyname'] ) {
					$tuple['provision']	.= "\n#INCLUDE " . $resdevice['blfkeyname'];
				}
			}
		// special code to encapsulate cisco XML - not nice - should be data driven

			if (preg_match( '/^[Cc]isco/',$tuple['device'])) {	
				$tuple['provision']	.= "\n</flat-profile>";
				$tuple['provision']	.= "\n</device>";
			}	
		
		}
		$tuple['technology']	= $resdevice['technology'];			
		$password 		= $this->helper->ret_password ($pwdlen);
		$tuple['passwd']	=  $password;
			

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
			
		$ret = $this->helper->createTuple("ipphone",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved new extension ";
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
	
//	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $pkey . "'")->fetchColumn();
	$sql = $this->dbh->prepare("select count(*) from ipphone_fkey where pkey=?");
	$sql->execute(array($pkey));
	$seq = $sql->fetchColumn();	
	$seq++;
	$res=$this->dbh->prepare('INSERT INTO ipphone_fkey(pkey,seq,type,label,value) VALUES(?,?,?,?,?)');
	$res->execute(array( $pkey,$seq,'Default','None','None'));
	
}

private function deleteLastBlf() {
// save the data away
	$pkey = $_POST['pkey'];
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $pkey . '" />' . PHP_EOL;
	echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
	
//	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $pkey . "'")->fetchColumn();
	$sql = $this->dbh->prepare("select count(*) from ipphone_fkey where pkey=?");
	$sql->execute(array($pkey));
	$seq = $sql->fetchColumn();	
	if ($seq) {
		$res=$this->dbh->prepare('DELETE FROM ipphone_fkey WHERE pkey=? AND seq=?');
		$res->execute(array($pkey,$seq));
	}
	
}

private function deleteRow() {
	$pkey = $_POST['pkey'];
	$this->helper->delTuple("ipphone",$pkey); 
/* delete COS information */
	$this->helper->predDelTuple("IPphoneCOSopen","IPphone_pkey",$pkey);
	$this->helper->predDelTuple("IPphoneCOSclosed","IPphone_pkey",$pkey);
	$this->helper->predDelTuple("IPphone_Fkey","pkey",$pkey);
	$this->message = "Deleted extension " . $pkey;
	$this->myPanel->msgDisplay('Deleted extension ' . $pkey);
	$this->myPanel->navRowDisplay("ipphone", $pkey);
}

private function showEdit() {

	$cfim = NULL;
	$cfbs = NULL;
	$ringdelay = 20;

	if (isset($this->keychange)) {
		$pkey = $this->keychange;		
	}
	else {
		if (isset($_POST['pkey'])) {
			$pkey = $_POST['pkey'];
		}
		else {
			$pkey = $_GET['pkey'];
		}
	}
	$tabselect = 0;
	if (isset($_POST['tabselect'])) {
		$tabselect = $_POST['tabselect'];
	}
	if (isset($_GET['tabselect'])) {
		$tabselect = $_GET['tabselect'];
	}		
	echo '<input type="hidden" id="tabselect" name="tabselect" value="'. $tabselect . '" />' . PHP_EOL;
	
	$res = $this->dbh->query("SELECT PROXY FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$proxy = $res['PROXY'];
	
//	$rows = $this->helper->getTable("ipphone","select ip.*, de.noproxy from ipphone ip inner join device de on ip.device=de.pkey");
	$sql = $this->dbh->prepare("SELECT ip.*, de.noproxy FROM ipphone ip INNER JOIN device de on ip.device=de.pkey WHERE ip.pkey=?");
	$sql->execute(array($pkey));
	$extension = $sql->fetch();
	
	$extlist=array();
	array_push($extlist,"None");	
	$res = $this->helper->getTable("ipphone","select pkey from ipphone ORDER BY pkey",false);
	foreach ($res as $row) {
		array_push($extlist,$row['pkey']);
	}
	
	$classOfService = $this->helper->getTable("cos",$sql='',$filter=false);	
	$printline = "Ext " . $extension['technology'] . "/" . $extension['pkey'] . " ";

	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 
	$latency = 'N/A';
	if ($this->astrunning) {
		$amiHelper = new amiHelper();
		$sip_peers = $amiHelper->get_peer_array();
		$amiHelper->get_database($pkey,$cfim,$cfbs,$ringdelay);			
		$latency = $sip_peers [$pkey]['Status'];	
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}
	
	$xref = $this->xRef($pkey);
	echo '<div class="titlebar">' . PHP_EOL;
	echo '<div class="buttons">' . PHP_EOL;
	$this->myPanel->Button("cancel");
	$ret = $this->helper->getLc();	
	if (!$ret) {	
		$this->myPanel->Button("new");
	}	
	$this->myPanel->override="update";
	$this->myPanel->Button("save");
			
	if ($extension['location'] == "remote" || $proxy == "NO" || $extension['noproxy']  ) {
	}
	else {
		echo '<img src="/sark-common/buttons/connect.png" id="connect" alt="connect" title="Proxy to the phone" />'. PHP_EOL;
	}
		
	if (preg_match(' /^OK/ ', $latency)) {
		
		$this->myPanel->buttonName["redo"]["title"] = "Send a reboot request to the endpoint";
		$this->myPanel->Button("notify");
		if (preg_match('/^[S|s]nom|Panasonic/',$extension['device'])) {
			$this->myPanel->buttonName["upload"]["title"] = "Send config without reboot";
			$this->myPanel->Button("upload");
		}
	} 
	$this->myPanel->commitButton(); 
	$this->myPanel->Button("delete");	
	echo '</div>';	
		
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
		
	echo '</div>';
	$this->printEditNotes($pkey,$extension,$sip_peers);
	echo '<div class="exttabedit">';
	echo '<div id="pagetabs" class="mytabs">' . PHP_EOL;
	echo '<ul>' . PHP_EOL;
	echo '<li><a href="#general">General</a></li>'. PHP_EOL;
	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		if (isset($extension['macaddr'])) {
			if (preg_match(' /\.[FLP]key/m ', $extension['provision'])) {
				echo  '<li><a href="#blf">BLF/DSS Keys</a></li>' . PHP_EOL;
			}
		}
		echo  '<li><a href="#vmail">Vmail</a></li>' . PHP_EOL;
	}
	if (! empty ($classOfService)) {
		echo '<li><a href="#cos" >CoS</a></li>' . PHP_EOL;
	}
	if ( $this->astrunning ) {
//		if ( $amiconrets ) {
			echo '<li><a href="#cfwd" >CFWD</a></li>' . PHP_EOL;
//		}
	}
    echo '<li><a href="#xref" >XREF</a></li>' . PHP_EOL;
	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {		
		if ( $_SESSION['user']['pkey'] == 'admin' ) {
			echo  '<li><a href="#asterisk">Asterisk</a></li>' . PHP_EOL;
//			if (isset($extension['macaddr'])) {
				echo  '<li><a href="#provisioning">Provisioning</a></li>' . PHP_EOL;
//			}						
		}
	}    
    echo '</ul>' . PHP_EOL;
    
/*
 *   TAB Provisioning
 */
    if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		if ( $_SESSION['user']['pkey'] == 'admin' ) {
//			if (isset($extension['macaddr'])) {
				echo '<div id="provisioning" >';
				echo '<textarea class="databox" name="provision" id="provision">' . htmlspecialchars($extension['provision']) . '</textarea>' . PHP_EOL;
				if (!empty($extension['macaddr'])) {
					echo '<br/><br/><a id="inline" href="#provgen">Expand</a>' . PHP_EOL;
					$url = "http://localhost/provisioning/" . $extension['macaddr'];
  					$ch = curl_init();
  					curl_setopt($ch, CURLOPT_URL, $url);
  					curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
  					$expand_prov = curl_exec($ch);
				}		
				echo '</div>' . PHP_EOL;
//			}
		}
	}

	echo '<div style="display:none"><div id="provgen"><div class="fancybox"><pre>' . htmlentities($expand_prov) . '</pre></div></div></div>'  . PHP_EOL;	
	
/*
 *   TAB Asterisk
 */
	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		if ( $_SESSION['user']['pkey'] == 'admin' ) {
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
	
	$this->myPanel->aLabelFor('sndcreds');
	$this->myPanel->selected = $extension['sndcreds'];
	$this->myPanel->popUp('sndcreds', array('No','Once','Always'));
	
	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->selected = $extension['cluster'];
	$this->myPanel->displayCluster();


 	$this->myPanel->aLabelFor('devicerec');
 	$this->myPanel->selected = $extension['devicerec'];
 	$this->myPanel->popUp('devicerec', array('default','None','OTR','OTRR','Inbound','Outbound','Both'));	
	if ($extension['technology'] == 'SIP' ) {
		$this->myPanel->aLabelFor('location');
		$this->myPanel->selected = $extension['location'];
		$this->myPanel->popUp('location', array('local','remote'));
		$this->myPanel->aLabelFor('tls');
		$this->myPanel->selected = $extension['tls'];
		$this->myPanel->popUp('tls', array('off','on'));		
	}
		

	$this->myPanel->aLabelFor('callerid');
	echo '<input type="text" name="callerid" id="callerid" size="10"  value="' . $extension['callerid'] . '"  />' . PHP_EOL;				
	if ($extension['technology'] == 'SIP' ) {
		$this->myPanel->aLabelFor('extalert');
		echo '<input type="text" name="extalert" id="extalert" size="30"  value="' . $extension['extalert'] . '"  />' . PHP_EOL;
	}	
	if ($extension['technology'] == 'Custom' ) {
		$this->myPanel->aLabelFor('cdialstring');
		echo '<input type="text" name="cdialstring" id="cdialstring" size="10"  value="' . $extension['cdialstring'] . '"  />' . PHP_EOL;
	}
	$this->myPanel->aLabelFor('ringdelay');
    echo '<input type="text" name="ringdelay" id="ringdelay" size="2"  value="' . $ringdelay . '"  />' . PHP_EOL;
    
    $this->myPanel->aLabelFor('cellphone');
    echo '<input type="text" name="cellphone" id="cellphone" size="14"  value="' . $extension['cellphone'] . '"  />' . PHP_EOL;
    if ( $extension['cellphone'] ) {
		$this->myPanel->aLabelFor('twin');
		if ( $extension['celltwin'] ) {
			echo '<input type="checkbox" checked="checked" name="celltwin" value="celltwin" />';
		}
		else {
			echo '<input type="checkbox" name="celltwin" value="celltwin" />';
		}
	}
    			
	echo '</div>' . PHP_EOL;
	
/*
 * 	TAB BLF/DSS Keys
 */  
	if (isset($extension['macaddr'])) {
		if (preg_match(' /\.[FLP]key/m ', $extension['provision'])) {
		echo '<div id="blf">' . PHP_EOL;

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
		echo '<div class="blfbuttons">' . PHP_EOL;
		$this->myPanel->override="newblf";
        $this->myPanel->buttonName["plus"]["title"] = "Add a new BLF key";
        $this->myPanel->Button("plus");
        $this->myPanel->override="delblf";
        $this->myPanel->buttonName["minus"]["title"] = "Delete last BLF key";
        $this->myPanel->Button("minus");
        echo '</div>' . PHP_EOL;			
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
			$sql = $this->dbh->prepare("SELECT IPphone_pkey FROM IPphoneCOSopen where IPphone_pkey=? and COS_pkey=?");
			$sql->execute(array($extension['pkey'],$cos['pkey']));
			$cosrec = $sql->fetch();						

			if (is_array($cosrec) && array_key_exists('IPphone_pkey',$cosrec)) {
				echo '<input type="checkbox" checked="yes" name="opencos[]" value="' . $cos['pkey'] . '" />&nbsp' . $cos['pkey'] . '<br/>' . PHP_EOL;	 
			}
			else {
				echo '<input type="checkbox" name="opencos[]" value="' . $cos['pkey'] . '" />&nbsp' . $cos['pkey'] . '<br/>' . PHP_EOL;			
			}
		}
		echo '<h2>Night-time Class of Service</h2>' . PHP_EOL;	
		foreach ($classOfService as $cos) {
			$sql = $this->dbh->prepare("SELECT IPphone_pkey FROM IPphoneCOSclosed where IPphone_pkey=? and COS_pkey=?");
			$sql->execute(array($extension['pkey'],$cos['pkey']));
			$cosrec = $sql->fetch();				
			if (is_array($cosrec) && array_key_exists('IPphone_pkey',$cosrec)) {
				echo '<input type="checkbox" checked="yes" name="closedcos[]" value="' . $cos['pkey'] . '" />&nbsp' . $cos['pkey'] . '<br/>' . PHP_EOL;	 
			}
			else {
				echo '<input type="checkbox" name="closedcos[]" value="' . $cos['pkey'] . '" />&nbsp' . $cos['pkey'] . '<br/>' . PHP_EOL;			
			}
		}
		echo '</div>' . PHP_EOL;
	}

	
/*
 * 	TAB Call Forwards
 */
	if ( $this->astrunning ) {
//		if ( $amiconrets ) {
			echo '<div id="cfwd"  >' . PHP_EOL;
			echo '<h2>Internal PBX Call Forwards</h2>' . PHP_EOL;
			$this->myPanel->aLabelFor('cfim');
			echo '<input type="text" name="cfim" id="cfim" value="' . $cfim . '"  />' . PHP_EOL;
			$this->myPanel->aLabelFor('cfbs');
			echo '<input type="text" name="cfbs" id="cfbs" value="' . $cfbs . '"  />' . PHP_EOL;
//		}
	}

	echo '</div>' . PHP_EOL;
	
/*
 * 	TAB Vmail
 */ 
	echo '<div id="vmail">' . PHP_EOL;
	
	$this->myPanel->aLabelFor('dvrvmail') . PHP_EOL;	
	$this->myPanel->selected = $extension['dvrvmail'];
	$this->myPanel->popUp('dvrvmail', $extlist) . PHP_EOL;	 
	$this->myPanel->aLabelFor('vmailfwd') . PHP_EOL;
	echo '<input type="text" name="vmailfwd" id="vmailfwd" size="30"  value="' . $extension['vmailfwd'] . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('vdelete') . PHP_EOL;
    echo '<input type="checkbox"   name="vdelete"  id="vdelete" value="vdelete" />' . PHP_EOL; 
	$this->myPanel->aLabelFor('vreset') . PHP_EOL;
    echo '<input type="checkbox"   name="vreset" id="vreset" value="vreset" />' . PHP_EOL;
	
	echo '</div>' . PHP_EOL;
	
/*
 * 	TAB XREF 
 */ 
	echo '<div id="xref">' . PHP_EOL;
	echo '<h2>Cross References to this extension</h2>' . PHP_EOL;
    echo '<p>' . $xref . '</p>' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	
	echo '</div>';
	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $pkey . '"  />' . PHP_EOL;
	if (preg_match(' /^OK/ ', $latency)) {
		echo '<input type="hidden" name="latency" id="latency" size="20"  value="' . $latency . '"  />' . PHP_EOL;
	} 
	echo '</div>';
	$this->myPanel->navRowDisplay("ipphone",$pkey);		
}

private function saveEdit() {
// save the data away

	$tuple = array();
		
	$this->validator = new FormValidator();
	
	$this->validator->addValidation("newkey","num","Invalid extension number");
	$this->validator->addValidation("newkey","req","You must specify an extension number");
	$this->validator->addValidation("newkey","minlen=3","Invalid minimum extension number");
	$this->validator->addValidation("newkey","maxlen=4","Invalid maximum extension number");
	$this->validator->addValidation("cellphone","num","cellphone number must be numeric");
    $this->validator->addValidation("vmailfwd","email","Invalid email address format");
    $this->validator->addValidation("callgroup","alnum","Call Group name must be alphanumeric(no spaces)");  
    $this->validator->addValidation("macaddr","regexp=/^[0-9a-fA-F]{12}|\s*$/","Mac address is invalid"); 
    $this->validator->addValidation("desc","regexp=/^[0-9a-zA-Z_\-\s()]+$/","Caller name is invalid - must be [0-9a-zA-Z_-() ]"); 
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
						'vreset' => True,
						'celltwin' => True,
						'ipaddress' => True
		);
		
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
		
		if ( isset($_POST['celltwin']) ) {
			$tuple['celltwin'] = True;
		}
		else {
			$tuple['celltwin'] = False;
		}
				
		$newkey =  trim(strip_tags($_POST['newkey']));

/*
 * local/remote processing
 */ 
		$tuple['sipiaxfriend'] = preg_replace( " /nat=yes/ ",'',$tuple['sipiaxfriend']);
		$tuple['sipiaxfriend'] = preg_replace( " /^\#include\s*sark_sip_tls.conf.*$/m ",'',$tuple['sipiaxfriend']);	
		$tuple['sipiaxfriend'] = rtrim($tuple['sipiaxfriend']);	
		
		if ($tuple['location'] == 'remote') {
			$tuple['sipiaxfriend'] .= "\nnat=yes";
		}
		
		//tls - we provide provisioning support for snom,Yealink,Panasonic with TLS
		
		$tuple['provision'] = preg_replace( " /^\#INCLUDE.*\.tls.*$/m ",'',$tuple['provision']);
		$tuple['provision'] = rtrim ($tuple['provision']);
		if ( $tuple['tls'] == 'on' ) { 
			$tuple['sipiaxfriend'] .= "\n#include sark_sip_tls.conf";
			if (isset($_POST['macaddr'])) {
				$res = $this->dbh->query("SELECT device FROM ipphone where pkey = '" . $tuple['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
				$device = $res['device'];
				if (preg_match(' /^s|Snom/ ',$device)) {
					$tuple['provision'] .= "\n#INCLUDE snom.tls";
				}
				if (preg_match(' /^y|Yealink/ ',$device)) {
					$tuple['provision'] .= "\n#INCLUDE yealink.tls";
				}
				if (preg_match(' /^Panasonic/ ',$device)) {
					$tuple['provision'] .= "\n#INCLUDE panasonic.tls";
				}
				if (preg_match(' /^Vtech/ ',$device)) {
					$tuple['provision'] .= "\n#INCLUDE vtech.tls";
				}								
			}
		}
		
/*	
 * update the asterisk internal database (callforwards and ringdelay)
 */  
 		if ($this->astrunning) {
			$amiHelper = new amiHelper();
			$amiHelper->put_database($newkey);			
		}
 		
/*
 * reset/empty voicemail if requested
 */

	if (isset($_POST['vdelete'])) { 
		$rc = $this->helper->request_syscmd ("/bin/rm -rf /var/spool/asterisk/voicemail/default/" . $_POST['pkey']."/*");	
		$this->message = "Voicemail deleted";
	}	
	
	if (isset($_POST['vreset'])) { 
		$skey = $_POST['pkey'];
		$rc = $this->helper->request_syscmd ("/bin/sed -i 's/^$skey => [0-9]*\(.*\)/$skey => $skey\\1/' /etc/asterisk/voicemail.conf");	
		$this->message = "Voicemail password reset";	
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
			$sql = $this->dbh->prepare("SELECT pkey FROM ipphone WHERE pkey=?");
			$sql->execute(array($newkey));
			$res = $sql->fetch();	
			if ( isset($res['pkey']) ) { 
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['extensave'] = " " . $newkey . " already exists!";	
			}
			else {
				// signal a key change to the editor
				$this->keychange = $newkey;
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
					// delete the old COS entries
					$this->helper->predDelTuple("IPphoneCOSopen","IPphone_pkey",$tuple['pkey']);
	 				$this->helper->predDelTuple("IPphoneCOSclosed","IPphone_pkey",$tuple['pkey']);
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
	$sql = $this->dbh->prepare("SELECT * FROM IPphone WHERE dvrvmail LIKE ? AND pkey != ? ORDER BY pkey");
	$sql->execute(array($pkey,$pkey));
	$result = $sql->fetchall();	
	foreach ($result as $row) {
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
    
	$sql = $this->dbh->prepare("SELECT * FROM lineio WHERE openroute LIKE ? OR closeroute LIKE ? ORDER BY pkey");
	$sql->execute(array($pkey,$pkey));		
	$result = $sql->fetchall();	
	foreach ($result as $row) {
		if ( $row['openroute'] == $pkey || $row['closeroute'] == $pkey ) {
                $tref .= "DDI/Class <a href='javascript:window.top.location.href=" . '"/php/sarkddi/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;
        }
	}
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No Trunks reference this extension<br/>" . PHP_EOL;
    }  
    
 	$sql = $this->dbh->prepare("SELECT * FROM speed WHERE outcome LIKE ? OR out LIKE ? ORDER BY pkey");
	$sql->execute(array($pkey,'%' . $pkey . '%'));	
 	$result = $sql->fetchall();	
	foreach ($result as $row) {
		if ($row['pkey'] != 'RINGALL') {
			$tref .= "Callgroup <a href='javascript:window.top.location.href=" . '"/php/sarkcallgroup/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;

		}
	}
	
	if ($tref != "") {
    	$xref .= $tref;
        $tref = "";
    }
    else {
    	$xref .= "No callgroups reference this extension<br/>" . PHP_EOL;
    }       

 	$sql = $this->dbh->prepare("SELECT * FROM appl WHERE extcode LIKE ? ORDER BY pkey");
	$sql->execute(array($pkey));		
	$result = $sql->fetchall();	
	foreach ($result as $row) {
/*
		$extcode = $row['extcode'];
		if (preg_match( "/$extcode/",$pkey)) {
*/
			$tref .= "Custom App <a href='javascript:window.top.location.href=" . '"/php/sarkappl/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;

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
			$tref .= "IVR <a href='javascript:window.top.location.href=" . '"/php/sarkivr/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;
		}
		else {
			for ($i = 1; $i <= 11; $i++) {
				if ($row["option" . $i] == $pkey) {
					$tref .= "IVR <a href='javascript:window.top.location.href=" . '"/php/sarkivr/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;
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

		$sql = $this->dbh->prepare("SELECT technology,device  FROM IPphone WHERE pkey = ?");
		$sql->execute(array($pkey));
		$res=$sql->fetch();		
		
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
		if (preg_match ( " /CiscoMP/ ", $res['device'])) {	
			$chk = 'ciscoMP-reboot';
		}
		else if (preg_match ( " /Cisco/ ", $res['device'])) {	
			$chk = 'cisco-check-cfg';
		}
		if (preg_match ( " /Panasonic/ ", $res['device'])) {	
			$chk = 'panasonic-check-cfg';
		}				
		if (preg_match ( " /Polycom/ ", $res['device'])) {	
			$chk = 'polycom-check-cfg';
		}	
		if (preg_match ( " /[S|s]nom/ ", $res['device'])) {
			$chk = 'snom-reboot';
		}
		if (preg_match ( " /Yealink/ ", $res['device'])) {
			$chk = 'yealink-reboot';
		}
		if (preg_match ( " /Grandstream/ ", $res['device'])) {
			$chk = 'grandstream-check-cfg';
		}
		if (preg_match ( " /Vtech/ ", $res['device'])) {
			$chk = 'vtech-check-cfg';
		}		
		if ( ! $chk ) {
			$this->message = "No notify data available for ext";
			return;
		}
		$this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'sip notify $chk $pkey' ");
    	$this->message = "Issued SIP Notify";
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

		$sql = $this->dbh->prepare("SELECT technology,device  FROM IPphone WHERE pkey = ?");
		$sql->execute(array($pkey));
		$res=$sql->fetch();		
		if ($res['technology'] != 'SIP') {
			$this->message = "Ext is not a SIP UA!!.";
			return;
		}
#
#	Only for Snoms....   and Panasonics 
#
		$chk = false;
	
		if (preg_match ( " /[S|s]nom/ ", $res['device'])) {
			$chk = 'snom-check-cfg';
		}
		if (preg_match ( " /Panasonic/ ", $res['device'])) {	
			$chk = 'panasonic-check-cfg';
		}			
		if (preg_match ( " /CiscoMP/ ", $res['device'])) {	
			$chk = 'ciscoMP-check-cfg';
		}
		if ( ! $chk ) {
			$this->message = "No notify data available";
			return;
		}
		else {
			$this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'sip notify $chk $pkey' ");
    	}
    	$this->message = "Issued SIP Notify" ;
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

private function printEditNotes ($pkey,$extension,$sip_peers) {
#
#   prints info Box
#
//	$helper = new helper;
//	$dbh = DB::getInstance();
//print_r($sip_peers);
	echo '<div  class="extnotes">' . PHP_EOL;
    echo '<span style="color: #696969;" >';
    echo '<span style="font-weight:bold; "></span><br/><br/>';
    echo 'Ext: <strong>' . $pkey . '</strong><br/>' . PHP_EOL;
    echo 'Name: <strong>' . $extension['desc'] . '</strong><br/>' . PHP_EOL;
    echo 'Vendor: <strong>' . $extension['device'] . '</strong><br/>' . PHP_EOL;
    if (!empty($extension['devicemodel'])) {
    	echo 'Model: <strong>' . $extension['devicemodel'] . '</strong><br/>' . PHP_EOL;
    }
    if (!empty ($extension['macaddr'])) {
			echo 'MAC: <strong>' . $extension['macaddr'] . '</strong><br/>' . PHP_EOL;
	}
	if (isset($sip_peers [$pkey]['Status'])) {
		echo 'State: <strong>' . $sip_peers [$pkey]['Status'] . '</strong><br/>' . PHP_EOL; 
	}
	else {
		echo 'State: <strong>UNKNOWN</strong><br/>' . PHP_EOL; 
	}
	
	
	if (preg_match(' /^OK/ ', $sip_peers [$pkey]['Status'])) {
		if (isset ($sip_peers [$pkey]['IPport'])) {
			echo 'IPport: <strong>' . $sip_peers [$pkey]['IPport'] . '</strong><br/>' . PHP_EOL;
		}		
		if (isset ($sip_peers [$pkey]['IPaddress'])) {
			echo 'IP: <strong>' . $sip_peers [$pkey]['IPaddress'] . '</strong><br/>' . PHP_EOL;
			echo '<input type="hidden" id="ipaddress" name="ipaddress" value="' . $sip_peers [$pkey]['IPaddress'] . '" />' . PHP_EOL;	 
		}		
	}
	if ($extension['tls'] == 'on') {
    	$transport = 'TLS';
    }
    else {
    	$transport = 'UDP';
    }
    echo 'Transport: <strong>' . $transport . '</strong><br/>' . PHP_EOL;
    
    $images='/sark-common/phoneimages/';
    if (isset($extension['devicemodel'])) {

		if (preg_match ( " /Aastra/ ", $extension['device'])) {
			$images .= 'aastra';
		}
		if (preg_match ( " /Cisco/ ", $extension['device'])) {	
			$images .= 'cisco';
		}
		if (preg_match ( " /Panasonic/ ", $extension['device'])) {	
			$images .= 'panasonic';
		}				
		if (preg_match ( " /Polycom/ ", $extension['device'])) {	
			$images .= 'polycom';
		}	
		if (preg_match ( " /[S|s]nom/ ", $extension['device'])) {
			$images .= 'snom';
		}
		if (preg_match ( " /Vtech/ ", $extension['device'])) {
			$images .= 'vtech';
		}
		if (preg_match ( " /Yealink/ ", $extension['device'])) {
			$images .= 'yealink';
		}
		
		$images .= '/' . $extension['devicemodel'] . '.jpg';		
		    	
		if (file_exists("/opt/sark/www" . $images)) {	
			echo '<br/><br/><img src="' . $images . '" width="190px" />' . PHP_EOL;
		}
		else {
			$this->helper->logit("Phone Image not found $images ",1 );
		}
	}	
    echo '</div>' . PHP_EOL;

}

}
