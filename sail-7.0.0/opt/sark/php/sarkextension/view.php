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
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkNetHelperClass";


Class sarkextension {
	
	protected $message; 
	protected $head = "Extensions";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $netHelper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $astrunning=false;
	protected $keychange=NULL;
	protected $cosresult;
	protected $passwordLength=12;
	protected $myBooleans = array(
		'active',
		'celltwin',
//		'closedcos',
//		'location',
//		'opencos',
//		'provisionwith',
		'vdelete',
		'vreset'	
	);

	protected $createOptions = array(
		'Choose extension type',
		'Provisioned',
		'Unprovisioned',
	); 

	protected $adoptOptions = array(
		'Choose extension type',
		'Provisioned',
		'Unprovisioned'
	); 

public function showForm() {

//	print_r($_REQUEST);

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->netHelper = new nethelper;

	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}
/*
	echo '<div class="guest"><img src="/sark-common/buttons/cancel.png" alt="closebutton" id="closebutton" />'. PHP_EOL;
	echo '<div id="iframecontent"></div></div>' . PHP_EOL;
*/	
	$this->myPanel->pagename = 'Extensions';
	
	if (isset($_POST['new']) || isset($_GET['new'])) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_REQUEST['delete'])) { 
		$this->deleteRow(); 		
	}		
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}
	
	if (isset($_POST['newblf'])) { 
		$this->saveNewBlf();
		$this->showEdit();
		return;
	}	

	if (isset($_POST['delblf'])) { 
		$this->deleteLastBlf();
		$this->showEdit();
		return;
	}		

	if (isset($_GET['notify'])) { 
		$this->sipNotify();
		$this->message = "Reboot request sent";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;			
//		$this->showEdit();
//		return;			
	}
	
	if (isset($_POST['notify'])) { 
		$this->sipNotify();
		$this->message = "Reboot request sent";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;			
		$this->showEdit();
		return;			
	}	
	
	if (isset($_POST['reboot'])) { 
		$this->sipNotify();
		$this->message = "Reboot request sent";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
		$this->showEdit();
		return;		
	}	
			
	if (isset($_POST['sync'])) { 
		$this->sipNotifyPush();
		$this->message = "Config request pushed";
//		echo '<input type="hidden" id="tabselect" name="tabselect" value="1" />' . PHP_EOL;	
		$this->showEdit();
		return;			
	}
	
	if (isset($_POST['save']) || isset($_POST['endsave'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
		}
		else {
			$this->showEdit();
		}
		return;					
	}
	
	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		$this->showEdit();
		return;				
	}

	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
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
	$buttonArray=array();
	$ret = $this->helper->getLc(); 
	if (!$ret) {	
		$buttonArray['new'] = true;
	}
	$this->myPanel->actionBar($buttonArray,"sarkextensionForm",false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup();
//	$this->myPanel->subjectBar("Extensions");

	echo '<form id="sarkextensionForm"  action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	
/*
	if ( $_SESSION['user']['pkey'] == 'admin' ) {	
		echo '<a  href="/php/downloadpdf.php?pdf=ipphone"><img id="pdfprint" src="/sark-common/buttons/print.png" alt="Click to Download PDF" title = "Click to Download PDF" ></a>' . PHP_EOL;									
	}
*/	
	

	$this->myPanel->beginResponsiveTable('extensionstable',' w3-tiny');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
			
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('ext'); 	
	$this->myPanel->aHeaderFor('uname',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('device',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('macaddr',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('ipaddr',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('headlocation',false,'w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('sndcreds',false,'w3-hide-small w3-hide-medium');
//	$this->myPanel->aHeaderFor('bt',false,'w3-hide-small w3-hide-medium');
//	$this->myPanel->aHeaderFor('trns',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('tstate');
	$this->myPanel->aHeaderFor('active',false,'w3-hide-small');	
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
		
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("ipphone","select ip.*, de.noproxy from ipphone ip inner join device de on ip.device=de.pkey",true,false,'ip.pkey');
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 

		echo '<td class="w3-hide-small  w3-hide-medium">' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<input type="hidden" name="pkey" id="pkey" value="' . $row['pkey'] . '"  />' . PHP_EOL;
//		if ($row['cluster'] != 'default') {
			$shortkey = substr($row['pkey'],2);
/*		}
		else {
			$shortkey = $row['pkey'];
		}	
*/	
		echo '<td class="read_only">' . $shortkey . '</td>' . PHP_EOL;
		
		$display = $row['desc'];
		if ( strlen($row['desc']) > 7 ) {
			$display = substr($row['desc'] , 0, 5);
			$display .= '.';
		}			 
		echo '<td class="w3-hide-small  w3-hide-medium" title = "' . $row['desc'] . '" >' . $display  . '</td>' . PHP_EOL;
		
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
		echo '<td class="w3-hide-small  w3-hide-medium" title = "' . $row['device'] . '" >' . $display  . '</td>' . PHP_EOL;	
		
		$display_macaddr = 'N/A';
		if (!empty ($row['macaddr'])) {
			$display_macaddr = $row['macaddr'];
		}		
		echo '<td class="w3-hide-small">' . $display_macaddr . '</td>' . PHP_EOL;
		
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
		
		$display = $display_ipaddr;
        if ( strlen($display_ipaddr) > 15 ) {
			$display = substr($display_ipaddr , 0, 13);
			$display .= '..';
		}
    	echo '<td  class="w3-hide-small" title = "' . $display_ipaddr . '" >' . $display  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small  w3-hide-medium">' . $row['location'] . '</td>' . PHP_EOL;
		
		echo '<td class="w3-hide-small  w3-hide-medium">' . $row['sndcreds'] . '</td>' . PHP_EOL;
		
		$latency = 'N/A';
		if (isset($sip_peers [$row['pkey']]['Status'])) {
			$latency = $sip_peers [$row['pkey']]['Status'];	
		}
		
		echo '<td class="icons" title = "Device State">' . $latency . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small" >' . $row['active'] . '</td>' . PHP_EOL;				

		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$row['pkey']);

	}
	

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();
}

private function showNew() {

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkextensionForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
	$this->myPanel->subjectBar("New Extension");

	echo '<form id="sarkextensionForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		
// find the next available ext# - can't do this until we know the cluster
//	$pkey = $this->helper->getNextFreeExt();

	$provisionwith = array('IPV4');
	
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
		array_push($devices,'General SIP');
	}

	$value = null;
	if (isset($_GET['mac'])) {
		$value=$_GET['mac'];
	}

	$this->myPanel->internalEditBoxStart();

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	echo '<div id="divchooser">' . PHP_EOL;
	if (isset($_GET['new'])) {
		$this->myPanel->displayPopupFor('extchooser','Choose extension type',$this->adoptOptions); 
	}
	else {
		$this->myPanel->displayPopupFor('extchooser','Choose extension type',$this->createOptions); 
	}
	echo '</div>' . PHP_EOL;
/*
	echo '<div id="divmacaddr">' . PHP_EOL;
	$this->myPanel->displayInputFor('macaddr','text',$value);
	echo '</div>' . PHP_EOL;
*/

/*
	echo '<div id="divrule">' . PHP_EOL;
	$this->myPanel->displayInputFor('blkstart','number',$pkey,'pkey');
	echo '</div>' . PHP_EOL;
*/

	echo '<div id="divblksize">' . PHP_EOL;
	$this->myPanel->displayInputFor('blksize','number',1,'blksize');
	echo '</div>' . PHP_EOL;

	$getmac=NULL;
	if (!empty($_GET['mac'])) {
		$getmac = $_GET['mac'];
	}
	echo '<div id="divmacblock">' . PHP_EOL;
	$this->myPanel->aLabelFor('macblock');
	echo '<p><textarea name="txtmacblock" class="w3-padding w3-margin-bottom w3-small w3-card-4 longdatabox">' . $getmac . '</textarea>';
 	$this->myPanel->aHelpBoxFor('macblock');
 	echo '</div>' . PHP_EOL;
/*
 	$extname = 'Ext' . $pkey;
	echo '<div id="divcalleridname">' . PHP_EOL;	
	$this->myPanel->displayInputFor('calleridname','text',$extname,'desc');
	echo '</div>' . PHP_EOL;
*/
/*	
	echo '<div id="divdevice">' . PHP_EOL;
	$this->myPanel->displayPopupFor('device','General SIP',$devices);
	echo '</div>' . PHP_EOL;
*/


	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL;
	echo '</form>' . PHP_EOL; // close the form
	echo '</div>';  
    $this->myPanel->responsiveClose();
}

private function saveNew() {
// save the data away

	$this->myPanel->xlateBooleans($this->myBooleans);

	$tuple = array();

	$res = $this->dbh->query("SELECT ACL,NATDEFAULT,PWDLEN,USERCREATE,FQDNPROV,VCL FROM globals WHERE pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$acl = $res['ACL'];

	if (isset ($res['PWDLEN'])) {
		$this->paswordLength = $res['PWDLEN'];
	}
	$vcl = $res['VCL'];
	$natdefault = $res['NATDEFAULT'];
	$usercreate = $res['USERCREATE'];
	$fqdnprov = $res['FQDNPROV'];

	$_POST['pkey'] = $this->helper->getNextFreeKey('ipphone',$_POST['cluster'],'startextension');
	$tuple['pkey'] = $_POST['pkey'];
//remove first two digits - or return an array from the sub.
	$tuple['desc'] = substr($tuple['pkey'],2);

	if ($vcl) {
		$tuple['location'] = 'remote';
	}
	else {
		$tuple['location'] = $natdefault;
	}
	
	if ($fqdnprov == 'YES') {
		$tuple['provisionwith'] = 'FQDN';
	}					

	if (isset ($_POST['cluster'])) {
		$tuple['cluster'] = strip_tags($_POST['cluster']);
	}
	else {
		$tuple['cluster'] = 'default';
	}

	$sql = "SELECT * FROM COS ORDER BY pkey";
	$this->cosresult = $this->dbh->query($sql);		

	$myChooser = strip_tags($_POST['extchooser']);
    switch ($myChooser) {
		case 'Provisioned':
			$macArray=array();
			$macArray = preg_split('/[\s]+/', $_POST['txtmacblock'] );
			if ( empty ($macArray) ) {
				$this->error_hash['MAC'] = "No MAC address list entered";
				$this->invalidForm = True;
				return -1;
			}
			foreach ($macArray as $mac) {
				$mac =  trim($mac);
				if ($this->checkThisMacForDups($tuple['macaddr'])) {
					$this->error_hash[$mac] = $mac . "MAC address already exists";
					$this->invalidForm = True;
				}
			}
			if ($this->checkHeadRoom(count($macArray),$pkey)) {
				$this->error_hash['macArray'] = "Insufficient extension slots for block operation";
				$this->invalidForm = True;				
			}
			if ($this->invalidForm) {
				return -1;
			}
			foreach ($macArray as $mac) {
				$res = $this->getVendorFromMac($mac);
				if ( ! $res) {
					$this->helper->logit("mac is $mac res is $res ",1 );
					$this->error_hash['macArray'] = "Vendor lookup failed for MAC $mac";
					$this->invalidForm = True;
					return -1;
				}
				$this->helper->logit("mac is $mac res is $res ",5 );
				$tuple['device'] = $res;
				$tuple['macaddr'] = $mac;
/*
				if ($tuple['cluster'] == 'default') {					
					$tuple['desc'] = $tuple['pkey'];
				}
				else {
*/
					$tuple['desc'] = substr($tuple['pkey'],2);
//				}
				$this->addNewExtension($tuple);
				$tuple['pkey'] ++;
			}
			return;
			break;

		case 'Unprovisioned':
			if ($this->checkHeadRoom($_POST['blksize'],$pkey)) {
				$this->error_hash['blksize'] = "Insufficient extension slots for block operation";
				$this->invalidForm = True;
				return;					
			}
			$tuple['device'] = 'General SIP';
			$blksize = strip_tags($_POST['blksize']);
			while ($blksize) {
/*
				if ($tuple['cluster'] == 'default') {					
					$tuple['desc'] = $tuple['pkey'];
				}
				else {
*/
					$tuple['desc'] = substr($tuple['pkey'],2);
//				}
				$this->addNewExtension($tuple);
				$tuple['pkey'] ++;
				$blksize--;
			}
			return;
			break;
			
		default:
			break;
	}
	if ($usercreate == 'YES') {
		$usertuple = array();
		$usertuple['pkey'] = $tuple['pkey'];
		$usertuple['extension'] = $tuple['pkey'];
		$usertuple['realname'] = $tuple['desc'];
		$ret = $this->helper->createTuple("user",$usertuple);		
		if ($ret == 'OK') {
			$this->helper->resetPassword($tuple['pkey']);
		}
		else {
			$this->invalidForm = True;
			$this->message = "User Create Error!";	
			$this->error_hash['userinsert'] = $ret;	
		}
	}

			
    unset ($this->validator);
}

private function addNewExtension ($tuple) {
// move this to a function



	$sql = $this->dbh->prepare("SELECT sipiaxfriend,technology,blfkeyname FROM device WHERE pkey = ?");
	$sql->execute(array($tuple['device']));
	$resdevice = $sql->fetch();

//	$tuple['sipiaxfriend'] 	= $resdevice['sipiaxfriend'];

	$tuple['sipiaxfriend'] 	= 
	"type=peer
defaultuser=\$desc
secret=\$password
mailbox=\$ext
host=dynamic
qualify=yes
context=internal
call-limit=3
callerid=\"\$desc\" <\$ext>
canreinvite=no
subscribecontext=\$subtxt
namedcallgroup=\$clst
namedpickupgroup=\$clst
disallow=all 
allow=alaw
allow=ulaw
nat=\$nat
transport=\$transport
encryption=\$encryption";

	if ($resdevice['technology'] == 'SIP') {
		if ($tuple['device'] != 'General SIP' && $tuple['device'] != 'MAILBOX') {
			$tuple['provision']	.= "#INCLUDE " . $tuple['device'];
		}
			
		if (!preg_match('/^[Pp]olycom/', $tuple['device']) ) {
			if ( $resdevice['blfkeyname'] && $resdevice['blfkeyname'] != 'None' ) {
				$tuple['provision']	.= "\n#INCLUDE " . $resdevice['blfkeyname'];
			}
		}
		// special code to encapsulate cisco XML - not nice - should be data driven

		if (preg_match( '/^[Cc]isco/',$tuple['device'])) {	
			$tuple['provision']	.= "\n</flat-profile>";
			$tuple['provision']	.= "\n</device>";
		}	
		
	}
	$tuple['technology'] = $resdevice['technology'];			
	$tuple['passwd'] = $this->helper->ret_password ($this->passwordLength);
	$tuple['dvrvmail'] = substr($tuple['pkey'],2);
			
// ToDo permit ipv6 acl

	if ($tuple['acl'] == 'YES' && $tuple['location'] == 'local') {
		if ( !preg_match(' /deny=/ ',$tuple['sipiaxfriend'])) {
			$tuple['sipiaxfriend'] .= "\ndeny=0.0.0.0/0.0.0.0";
		}
		if ( !preg_match(' /permit=/ ',$tuple['sipiaxfriend'])) {
			$tuple['sipiaxfriend'] .= "\npermit=" . $this->netHelper->get_networkIPV4() . '/' . $this->netHelper->get_networkCIDR();
		}			
	}

	$tuple['sipiaxfriend'] = trim($tuple['sipiaxfriend']);

/*
 * 	Adjust the Asterisk and provisioning boxes
 */
	$this->adjustAstProvSettings($tuple);

/*
 *	Add the row
 */
	$ret = $this->helper->createTuple("ipphone",$tuple);
	if ($ret == 'OK') {
		$this->createCos(); 
		$this->message = "Saved new extension(s) ";
	}
	else {
		$this->invalidForm = True;
		$this->message = "DB Errors!";	
		$this->error_hash['DB'] = $ret;	
		return -1;
	}	
	return 'OK';	
}

private function createCos() {

		foreach ($this->cosresult as $cos) {
			if ($cos['defaultopen'] == 'YES') {
				$res=$this->dbh->prepare('INSERT INTO IPphoneCOSopen(IPphone_pkey,COS_pkey) VALUES(?,?)');
				$res->execute(array( $tuple['pkey'],$cos['pkey'] ));
			}
			if ($cos['defaultclosed'] == 'YES') {
				$res=$this->dbh->prepare('INSERT INTO IPphoneCOSclosed(IPphone_pkey,COS_pkey) VALUES(?,?)');
				$res->execute(array( $tuple['pkey'],$cos['pkey'] ));				
			}			
		}
}


private function blfKeys($extension) {
/*
 * 	TAB BLF/DSS Keys
 */  

	if (isset($extension['macaddr'])) {
		if (preg_match(' /\.[FLP]key/m ', $extension['provision'])) {
			$sql = $this->dbh->prepare("select count(*) from ipphone_fkey where pkey=?");
			$sql->execute(array($extension['pkey']));
			$numblfs = $sql->fetchColumn();	
			if ($numblfs < 6) {
				while ($numblfs < 6) {
					$this->saveNewBlf($extension['pkey']);
					$numblfs++;
				}
			}
			echo '<div class="w3-margin-top w3-margin-bottom">';
			$this->myPanel->aLabelFor("blfhead");
			$this->myPanel->aHelpBoxFor("blfhead");
			echo '</div>';


			echo '<table class="' . $this->myPanel->tableClass . ' w3-small" id="blftable">';
			echo '<thead>' . PHP_EOL;	
			echo '<tr>' . PHP_EOL;
	
			$this->myPanel->aHeaderFor('blfkey',false); 	
			$this->myPanel->aHeaderFor('blftype',false);
			$this->myPanel->aHeaderFor('blflabel',false,'w3-hide-small w3-hide-medium');
			$this->myPanel->aHeaderFor('blfvalue',false);
		
			$sql = "select * from Ipphone_FKEY where pkey='" . $extension['pkey'] . "'";
			$rows = $this->helper->getTable("ipphone_fkey",$sql,false);

			echo '</tr>' . PHP_EOL;
			echo '</thead>' . PHP_EOL;
			echo '<tbody>' . PHP_EOL;			

			foreach ($rows as $row ) {
				echo '<tr id="' . $row['seq'] . '~' . $row['pkey'] . '">'. PHP_EOL; 
				echo '<td>' . $row['seq'] . '</td>' . PHP_EOL;
				echo '<td >' . $row['type'] . '</td>' . PHP_EOL;
				echo '<td class="w3-hide-small w3-hide-medium">' . $row['label'] . '</td>' . PHP_EOL;
				echo '<td >' . $row['value'] . '</td>' . PHP_EOL;	
				echo '</tr>'. PHP_EOL;
			}

			echo '</tbody>' . PHP_EOL;
			echo '</table>' . PHP_EOL;
//			echo '</div>' . PHP_EOL;
			echo '<div class="w3-container w3-padding w3-margin-top">' . PHP_EOL;
			if ($numblfs > 6) {
				echo '<button class="w3-button w3-blue w3-small w3-round-xxlarge w3-padding w3-left" type="submit" name="delblf">Delete</button>';
			}
			echo '<button class="w3-button w3-blue w3-small w3-round-xxlarge w3-padding w3-right" type="submit" name="newblf">+Add</button>';
			echo '</div>' . PHP_EOL;
			
			
		}
	}	
}

private function saveNewBlf($pkey=null) {
// save the data away

	if (!$pkey) {
		$pkey = $_POST['pkey'];
	}
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $pkey . '" />' . PHP_EOL;
	
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
	$pkey = $_REQUEST['pkey'];
	$this->helper->delTuple("ipphone",$pkey); 
/* delete COS information */
	$this->helper->predDelTuple("IPphoneCOSopen","IPphone_pkey",$pkey);
	$this->helper->predDelTuple("IPphoneCOSclosed","IPphone_pkey",$pkey);
	$this->helper->predDelTuple("IPphone_Fkey","pkey",$pkey);
	$this->message = "Deleted extension " . $pkey;
//	$this->myPanel->msgDisplay('Deleted extension ' . $pkey);
//	$this->myPanel->navRowDisplay("ipphone", $pkey);
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

	$res = $this->dbh->query("SELECT FQDN,PROXY FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$proxy = $res['PROXY'];
	$fqdn = $res['FQDN'];

	$sql = $this->dbh->prepare("SELECT ip.*, de.noproxy FROM ipphone ip INNER JOIN device de on ip.device=de.pkey WHERE ip.pkey=?");
	$sql->execute(array($pkey));
	$extension = $sql->fetch();
	
	$extlist=array();
	array_push($extlist,"None");	
	$res = $this->helper->getTable("ipphone","select pkey from ipphone WHERE cluster='" . $extension['cluster'] . "'",false);
	foreach ($res as $row) {
		array_push($extlist,substr($row['pkey'],2));
	}

	$protocol = array('IPV4');
	
// see if we can do ipv6	
	$ipv6gua = $this->netHelper->get_IPV6GUA();
	if (!empty($ipv6gua)) {
// see ifphone can do IPV6
		$shortdevice = substr($extension['device'],0,4);
		if ($shortdevice == 'snom' || $shortdevice == 'Yeal' || $shortdevice == 'Pana'  || $shortdevice == 'Vtec') {
			array_push($protocol,'IPV6');
		}
	}	
	
	$classOfService = $this->helper->getTable("cos",$sql='',$filter=false);	
	
	$latency = 'N/A';
	if ($this->astrunning) {
		$amiHelper = new amiHelper();
		$sip_peers = $amiHelper->get_peer_array();
		$amiHelper->get_database($pkey,$cfim,$cfbs,$ringdelay,$celltwin);			
		$latency = $sip_peers [$pkey]['Status'];	
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}
	
	$xref = $this->xRef($pkey);
	$buttonArray['cancel'] = true;
	if (preg_match(' /^OK/ ', $latency) && $extension['device'] != 'General SIP') {
//		$buttonArray['redo'] = true;
		$buttonArray['notify'] = true;
		if (preg_match('/^[S|s]nom|Panasonic/',$extension['device'])) {
			$buttonArray['sync'] = true;
		}
	}
	$buttonArray['delete'] = true;

	$this->myPanel->actionBar($buttonArray,"sarkextensionForm",false,true,true,$pkey);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
/*	
	 if ($pkey) {
             $this->myPanel->navRowDisplay($pkey);
     }

	$this->myPanel->Heading($this->head . " " . $pkey,$this->message);
*/
	echo '<div class="w3-container w3-padding ' . $this->myPanel->bgColorClass . '">'; 
	$this->myPanel->navRowDisplay($pkey);
	echo '<span class="w3-text-blue-grey" style="margin:0;font-size:24px;">';
	echo $this->head;
    if (isset($this->message)) {
       $this->myPanel->showMsg($this->message);
    }
    echo '</span>';
    echo '</div>';

//    print_r($_REQUEST);

	$this->myPanel->responsiveTwoCol();

	$subject = $extension['technology'] . "/" . $extension['pkey'];
	if (isset($extension['macaddr'])) {
		$subject .= " (" . $extension['macaddr'] . ")";

	}
	else {
		$subject .= " General SIP";
	}

	echo '<form id="sarkextensionForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

/* ToDo - PROXY PANEL			
	if ($extension['location'] == "remote" || $proxy == "NO" || $extension['noproxy']  ) {
	}
	else {
		echo '<img src="/sark-common/buttons/connect.png" id="connect" alt="connect" title="Proxy to the phone" />'. PHP_EOL;
	}
*/

	echo '<div class="w3-padding w3-margin-bottom w3-card-4 w3-white w3-hide-large w3-hide-medium">';   
    $this->printEditNotes($pkey,$extension,$sip_peers);
    echo '</div>';

/*
 * 	TAB General
 */  
    $this->myPanel->internalEditBoxStart();
	echo '<div id="clustershow">';
	$this->myPanel->displayInputFor('cluster','text',$extension['cluster'],'cluster');
	echo '</div>';    
    $this->myPanel->displayBooleanFor('active',$extension['active']);
    echo '<input type="hidden" name="pkey" id="pkey" value="' . $extension['pkey'] . '"  />' . PHP_EOL;
//	if ($extension['cluster'] != 'default') {
		$shortkey = substr($extension['pkey'],2);
/*
	}
	else {
		$shortkey = $row['pkey'];
	}
*/
	$this->myPanel->displayInputFor('rule','text',$shortkey,'newkey');

/*	
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $extension['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';  
*/	  	
	$this->myPanel->displayInputFor('callerid','text',$extension['callerid']);	
	$this->myPanel->displayInputFor('calleridname','text',$extension['desc'],'desc');
	$this->myPanel->displayInputFor('password','text',$extension['passwd'],'passwd');	
    $this->myPanel->displayInputFor('cellphone','number',$extension['cellphone']);
    if ( $extension['cellphone'] ) {
		if ($celltwin) {
    		$this->myPanel->displayBooleanFor('celltwin','ON');
    	}
    	else {
    		$this->myPanel->displayBooleanFor('celltwin','OFF');
    	}
	}

	echo '</div>';
/*
 * 	TAB Vmail
 */ 
	$this->myPanel->internalEditBoxStart();
	$this->myPanel->displayInputFor('vmailfwd','text',$extension['vmailfwd']);
	$this->myPanel->displayPopupFor('dvrvmail',$extension['dvrvmail'],$extlist);
    $this->myPanel->displayBooleanFor('vdelete','NO');
    $this->myPanel->displayBooleanFor('vreset','NO');
	
	echo '</div>' . PHP_EOL;

	$this->myPanel->internalEditBoxStart();
//	$this->myPanel->displayBooleanFor('location',$extension['location']);
	$this->myPanel->radioSlide('location',$extension['location'],array('local','remote'));

	if (isset($extension['macaddr'])) {
		if (isset($fqdn)) {
			$this->myPanel->radioSlide('provisionwith',$extension['provisionwith'],array('IP','FQDN'));
		}
		$this->myPanel->radioSlide('sndcreds',$extension['sndcreds'],array('No','Once','Always'));	
	}	
	if (count($protocol) > 1)	{
		$this->myPanel->aLabelFor('protocol');
		$this->myPanel->selected = $extension['protocol'];
		$this->myPanel->popUp('protocol', $protocol);
		$this->myPanel->aHelpBoxFor('protocol');
	}
	$transportArray=array('udp','tcp','tls');
	if (preg_match(" /[Cc]isco/ ", $extension['device'])) {
		$transportArray=array('udp','tcp');
	}
	$this->myPanel->radioSlide('transport',$extension['transport'],$transportArray);

	$this->myPanel->displayPopupFor('devicerec',$extension['devicerec'],array('default','None','OTR','OTRR','Inbound','Outbound','Both'));	

	if ($extension['technology'] == 'SIP' ) {
		$this->myPanel->displayInputFor('extalert','text',$extension['extalert']);
	}	
	if ($extension['technology'] == 'Custom' ) {
		$this->myPanel->displayInputFor('cdialstring','text',$extension['cdialstring']);
	}

	echo '</div>';

/*
 *
 */	
		

	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $pkey . '"  />' . PHP_EOL;
	if (preg_match(' /^OK/ ', $latency)) {
		echo '<input type="hidden" name="latency" id="latency" size="20"  value="' . $latency . '"  />' . PHP_EOL;
	} 
	
	echo '<div class="w3-left w3-padding w3-container"></div>' . PHP_EOL;


	$this->myPanel->responsiveTwoColRight();

	echo '<div class="w3-padding w3-margin-bottom w3-card-4 w3-white w3-hide-small">';   
    $this->printEditNotes($pkey,$extension,$sip_peers);
    echo '</div>';

    $this->blfkeys($extension);


/*
 * 	TAB XREF 
 */ 
	$this->myPanel->internalEditBoxStart();
	echo '<div class="w3-margin-bottom">';	
	$this->myPanel->aLabelFor("xref");
	echo '</div>';	
//    $xref = $this->helper->xRef($pkey,"Extension");
    $xref = $this->xRef($pkey);


    $this->myPanel->displayXref($xref);


	echo '</div>' . PHP_EOL;    


	$this->myPanel->internalEditBoxStart();
    $this->myPanel->displayInputFor('ringdelay','number',$ringdelay);		

/*
 * 	Call Forwards
 */
	if ( $this->astrunning ) {
		$this->myPanel->displayInputFor('cfim','number',$cfim);
		$this->myPanel->displayInputFor('cfbs','number',$cfbs);
	}

	echo '</div>' . PHP_EOL;


/*
 * 	TAB COS
 */ 

	if (! empty ($classOfService)) {
		$this->myPanel->internalEditBoxStart();
		echo '<div class="w3-margin-bottom">';	
		$this->myPanel->aLabelFor("cosday");
		echo '</div>';	
    
		foreach ($classOfService as $cos) {
			$sql = $this->dbh->prepare("SELECT IPphone_pkey FROM IPphoneCOSopen where IPphone_pkey=? and COS_pkey=?");
			$sql->execute(array($extension['pkey'],$cos['pkey']));
			$cosrec = $sql->fetch();						
			echo '<div class="w3-margin-bottom">';
			$this->myPanel->aLabelFor($cos['pkey']);
			if (is_array($cosrec) && array_key_exists('IPphone_pkey',$cosrec)) {
//				echo '<input type="checkbox" checked="yes" name="opencos[]" value="' . $cos['pkey'] . '" />&nbsp' . $cos['pkey'] . '<br/>' . PHP_EOL;
				$this->myPanel->aBooleanFor('opencos','YES','','opencos' . $cos['pkey']);	 
			}
			else {
//				echo '<input type="checkbox" name="opencos[]" value="' . $cos['pkey'] . '" />&nbsp' . $cos['pkey'] . '<br/>' . PHP_EOL;	
				$this->myPanel->aBooleanFor('opencos','NO','','opencos' . $cos['pkey']);		
			}
			echo '</div>' . PHP_EOL;
		}
//		$this->myPanel->subjectBar("Night-time Class of Service");
		echo '<div class="w3-margin-bottom">';	
		$this->myPanel->aLabelFor("cosnight");
		echo '</div>';		
		foreach ($classOfService as $cos) {
			$sql = $this->dbh->prepare("SELECT IPphone_pkey FROM IPphoneCOSclosed where IPphone_pkey=? and COS_pkey=?");
			$sql->execute(array($extension['pkey'],$cos['pkey']));
			$cosrec = $sql->fetch();
			echo '<div class="w3-margin-bottom">';	
			$this->myPanel->aLabelFor($cos['pkey']);		
			if (is_array($cosrec) && array_key_exists('IPphone_pkey',$cosrec)) {
				$this->myPanel->aBooleanFor('closedcos','YES','','closedcos' . $cos['pkey']);	
			}
			else {
				$this->myPanel->aBooleanFor('closedcos','NO','','closedcos' . $cos['pkey']);			
			}
			echo '</div>' . PHP_EOL;
		}
		echo '</div>' . PHP_EOL;
	}	
	

	
/*
 *   TAB Asterisk
 */
	if ($extension['technology'] == 'SIP' ||  $extension['technology'] == 'IAX') {
		if ( $_SESSION['user']['pkey'] == 'admin' ) {
			echo '<div class="w3-margin-bottom">';	
			$this->myPanel->aLabelFor("sipiaxfriend");
			echo '</div>';	
			echo '<div id="asterisk" >';
			$this->myPanel->displayFile(htmlspecialchars($extension['sipiaxfriend']),"sipiaxfriend");
			echo '</div>' . PHP_EOL;
		}
	}	
    
    /*
 *   TAB Provisioning
 */
	
    if ($extension['technology'] == 'SIP') {
    	
		if ( $_SESSION['user']['pkey'] == 'admin' ) {
			if (isset($extension['macaddr'])) {
				echo '<div class="w3-margin-bottom">';	
				$this->myPanel->aLabelFor("Provisioning Rules");
				echo '</div>';	
				echo '<div id="provisioning" >';
				$this->myPanel->displayFile(htmlspecialchars($extension['provision']),"provision");
				if (!empty($extension['macaddr'])) {
					echo '<div class="w3-container w3-padding w3-margin-top">' . PHP_EOL;
					echo '<span onclick="document.getElementById(\'provExpand\').style.display=\'inherit\'" class="w3-blue w3-small w3-round-xxlarge w3-padding w3-right">Expand</span>';
					echo '</div>' . PHP_EOL;
					$cmd = 'php /opt/sark/provisioning/device.php '. $extension['macaddr'];
					$expand_prov = `$cmd`; 
				}		
				echo '</div>' . PHP_EOL;
			}
		}
	}

    echo '<div id="provExpand" style="display:none">';
    $this->myPanel->displayFile(htmlspecialchars($expand_prov),"provisioning",true);
    echo '<div class="w3-container w3-padding w3-margin-top">' . PHP_EOL;
	echo '<span onclick="document.getElementById(\'provExpand\').style.display=\'none\'" class="w3-blue w3-small w3-round-xxlarge w3-padding w3-right">Close</spann>';
	echo '</div>' . PHP_EOL;
  	echo '</div>';

  	if (isset($extension['macaddr'])) {
  		echo '<input type="hidden" id="macaddr" name="macaddr" value="' . $extension['macaddr'] . '" />' . PHP_EOL;	
	}	

	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);

 	echo '</form>' . PHP_EOL; // close the form 
    $this->myPanel->responsiveClose();
}

private function saveEdit() {
// save the data away
	$tuple = array();

	$this->myPanel->xlateBooleans($this->myBooleans);
		
	$this->validator = new FormValidator();
	
	$this->validator->addValidation("newkey","num","Invalid extension number");
	$this->validator->addValidation("newkey","req","You must specify an extension number");
	$this->validator->addValidation("newkey","minlen=3","Extension number must be 3 or 4 digits");
	$this->validator->addValidation("newkey","maxlen=4","Extension number must be 3 or 4 digits");
	$this->validator->addValidation("cellphone","num","cellphone number must be numeric");
    $this->validator->addValidation("vmailfwd","email","Invalid email address format");
//    $this->validator->addValidation("callgroup","alnum","Call Group name must be alphanumeric(no spaces)");  
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
						'ipaddress' => True,
						'provisioning' => True,
		);

		$cosSetArray = array();

// suppress update of concatenated COS names
		foreach ($_POST as $key => $val) {
			if (preg_match( '/^(opencos|closedcos)(.*)$/', $key, $matches)) {
				$custom[$key] = True;
				$cosSetArray[$key] = True; // scavenge for later
			}
		}
		
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
/*		
		if ( isset($_POST['twin']) && $_POST['twin'] == "" ) {
			$tuple['celltwin'] = True;
		}
		else {
			$tuple['celltwin'] = False;
		}
*/				
		$newkey =  trim(strip_tags($_POST['newkey']));

/*
 * 	Adjust the Asterisk and provisioning boxes
 */

	$this->adjustAstProvSettings($tuple);
			
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
		$shortkey = substr($skey,2);
		$rc = $this->helper->request_syscmd ("/bin/sed -i 's/^$skey => [0-9]*\(.*\)/$skey => $shortkey\\1/' /etc/asterisk/voicemail.conf");	
		$this->message = "Voicemail password reset";	
	}
		
/*
 * update the SQL database
 */
 
// remove any escaped quotes 
		$this->removeQuotes($tuple['provision']);
		$this->removeQuotes($tuple['sipiaxfriend']);
// do COS			
		$this->doCos($cosSetArray);
		
/*
 * check for keychange
 */
//		if ($tuple['cluster'] != 'default') {
			$sql = $this->dbh->prepare("SELECT id FROM cluster WHERE pkey=?");
			$sql->execute(array($tuple['cluster']));
			$cluster = $sql->fetch();	
			$sql = NULL;
			$newkey = $cluster['id'] . $newkey;
//		}

		if ($newkey != $tuple['pkey']) {

			$sql = $this->dbh->prepare("SELECT pkey FROM ipphone WHERE pkey=?");
			$sql->execute(array($newkey));
			$res = $sql->fetch();	
			$sql = NULL; 
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
					$this->message = "Updated extension " . $tuple['pkey'];
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
					$this->message = "Validation Errors!";	
					$this->error_hash['extensave'] = $ret;	
				}
			}
		}
		else {
			$this->chkMailbox($tuple['dvrvmail'],$tuple['sipiaxfriend']);		
			$ret = $this->helper->setTuple("ipphone",$tuple,$newkey);
			if ($ret == 'OK') {
				$this->message = "Updated extension " . $tuple['pkey'];
			}
			else {
				$this->invalidForm = True;
				$this->message = "Validation Errors!";	
				$this->error_hash['extensave'] = $ret;	
			}
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    unset ($this->validator);
}

private function adjustAstProvSettings(&$tuple) {
/*
 * local/remote processing
 */ 
//		$tuple['sipiaxfriend'] = preg_replace( " /nat=yes/ ",'',$tuple['sipiaxfriend']);		
		$tuple['sipiaxfriend'] = preg_replace( " /^\#include\s*sark_sip_tls.conf.*$/m ",'',$tuple['sipiaxfriend']);	
		$tuple['sipiaxfriend'] = preg_replace( " /^\#include\s*sark_sip_tcp.conf.*$/m ",'',$tuple['sipiaxfriend']);	
		$tuple['sipiaxfriend'] = rtrim($tuple['sipiaxfriend']);	


		//tls - we provide provisioning support for snom,Yealink,Panasonic with TCP
		
		if (isset($tuple['provision'])) {
			$tuple['provision'] = preg_replace( " /^\#INCLUDE.*\.tcp.*$/m ",'',$tuple['provision']);		
		
		//tls - we provide provisioning support for snom,Yealink,Panasonic with TLS
		
			$tuple['provision'] = preg_replace( " /^\#INCLUDE.*\.tls.*$/m ",'',$tuple['provision']);
		
			$tuple['provision'] = preg_replace( " /^\#INCLUDE.*\.udp.*$/m ",'',$tuple['provision']);
		
		//ipv6 - we provide provisioning support for snom,Yealink,Panasonic with ipv6
		
			$tuple['provision'] = preg_replace( " /^\#INCLUDE.*\.ipv6.*$/m ",'',$tuple['provision']);
		
			$tuple['provision'] = preg_replace( " /^\#INCLUDE.*\.ipv4.*$/m ",'',$tuple['provision']);
		
			$tuple['provision'] = rtrim ($tuple['provision']);
		}
		
		
		
		if (isset($_POST['macaddr'])) {
			$res = $this->dbh->query("SELECT device FROM ipphone where pkey = '" . $tuple['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
			$device = $res['device'];
			$shortdevice = substr($device,0,4);

			switch ($shortdevice) {			
				case 'Snom':
				case 'snom':					
					switch ($tuple['transport']) {						
						case 'tcp':
							$tuple['provision'] .= "\n#INCLUDE snom.tcp";
							break;
						case 'tls':
							$tuple['provision'] .= "\n#INCLUDE snom.tls";						
							break;
						default: 
							$tuple['provision'] .= "\n#INCLUDE snom.udp";
					}
					switch ($tuple['protocol']) {
						case 'IPV6':
							$tuple['provision'] .= "\n#INCLUDE snom.ipv6";
							break;
						default:
							$tuple['provision'] .= "\n#INCLUDE snom.ipv4";
					}
					break;
					
				case 'Yeal':					
					switch ($tuple['transport']) {						
						case 'tcp':
							$tuple['provision'] .= "\n#INCLUDE yealink.tcp";
							break;
						case 'tls':
							$tuple['provision'] .= "\n#INCLUDE yealink.tls";
							break;
						default: 
							$tuple['provision'] .= "\n#INCLUDE yealink.udp";
					}
					switch ($tuple['protocol']) {
						case 'IPV6':
							$tuple['provision'] .= "\n#INCLUDE yealink.ipv6";
							break;
						default:
							$tuple['provision'] .= "\n#INCLUDE yealink.ipv4";
					}
					break;				
			
				case 'Pana':					
					switch ($tuple['transport']) {						
						case 'tcp':
							$tuple['provision'] .= "\n#INCLUDE panasonic.tcp";
							break;
						case 'tls':
							$tuple['provision'] .= "\n#INCLUDE panasonic.tls";
							break;	
						default: 
							$tuple['provision'] .= "\n#INCLUDE panasonic.udp";
					}
					switch ($tuple['protocol']) {
						case 'IPV6':
							$tuple['provision'] .= "\n#INCLUDE panasonic.ipv6";
							break;
						default:
							$tuple['provision'] .= "\n#INCLUDE panasonic.ipv4";
					}
					break;					
/*
	Cisco tcp not done yet
	Cisco tls needs cisco certs		
				case 'Cisc':					
					switch ($tuple['transport']) {						
						case 'tcp':
							$tuple['provision'] .= "\n#INCLUDE cisco.tcp";
							break;
						case 'tls':
							$tuple['provision'] .= "\n#INCLUDE cisco.tls";
							break;	
						default: 
							$tuple['provision'] .= "\n#INCLUDE cisco.udp";
					}
					break;		
*/
/*					
				case 'Vtec':					
					switch ($tuple['transport']) {						
						case 'tcp':
							$tuple['provision'] .= "\n#INCLUDE vtech.tcp";
							break;
						case 'tls':
							$tuple['provision'] .= "\n#INCLUDE vtech.tls";
							break;	
						default: 
							$tuple['provision'] .= "\n#INCLUDE vtech.udp";
					}
					switch ($tuple['protocol']) {
						case 'IPV6':
							$tuple['provision'] .= "\n#INCLUDE vtech.ipv6";
							break;
						default:
							$tuple['provision'] .= "\n#INCLUDE vtech.ipv4";
					}
*/			
			}

		}


}

private function doCOS($cosSetArray) {
# Do the Booleans
	$tuple = array();

/*
 * delete the existing rows (if any)
 */ 
	$this->helper->predDelTuple("IPphoneCOSopen","IPphone_pkey",$_POST['pkey']);
	$this->helper->predDelTuple("IPphoneCOSclosed","IPphone_pkey",$_POST['pkey']);
/*
 * add the new rows
 */ 
	if (!empty($cosSetArray)) {
		foreach ($cosSetArray as $key=>$val) {			
			preg_match( '/^(opencos|closedcos)(.*)$/', $key, $matches);
			$this->helper->logit("COS considering $key " . $_POST['pkey'] . " " . $matches[2],1 );
			$tuple['IPphone_pkey'] = $_POST['pkey'];
			$tuple['COS_pkey'] = $matches[2];
			$target = 'IPphoneCOSclosed';
			if ($matches[1] == 'opencos') {
				$target = 'IPphoneCOSopen';
			} 
			$ret = $this->helper->createTuple($target,$tuple,false);
			if ($ret != 'OK') {
				$this->invalidForm = True;
				$this->message = "Validation Errors!";	
				$this->error_hash[$matches[2]] = $ret;	
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
    	$xref .= "No DDI's reference this extension<br/>" . PHP_EOL;
    }  
    
 	$sql = $this->dbh->prepare("SELECT * FROM speed WHERE outcome LIKE ? OR out LIKE ? ORDER BY pkey");
	$sql->execute(array($pkey,'%' . $pkey . '%'));	
 	$result = $sql->fetchall();	
	foreach ($result as $row) {
		if ($row['pkey'] != 'RINGALL') {
			$tref .= "Ring Group <a href='javascript:window.top.location.href=" . '"/php/sarkcallgroup/main.php?edit=yes&pkey=' . $row['pkey'] . '"' . "' >" . $row['pkey'] . ' </a> references this extension <br>' . PHP_EOL;

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
		$shortdevice = substr($res['device'],0,4);
		switch ($shortdevice) {
			case 'Snom':
				$chk = 'snom-reboot';
				break;
			case 'Yeal':
				$chk = 'yealink-reboot';
				break;	
			case 'Link':
				$chk = 'sipura-check-cfg';
				break;												
			case 'Aast':		
			case 'Cisc':		
			case 'Pana':
			case 'Poly':
			case 'Vtec':
				$chk = 'general-check-cfg';
				break;	
			default:
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
		else if ( preg_match(' /mailbox=\d+/ ',$friend))	{	
			$friend = preg_replace ( '/mailbox=\d+/', $astmailbox, $friend);
		}
		else if ( preg_match(' /mailbox=/ ',$friend))	{	
			$friend = preg_replace ( '/mailbox=/', $astmailbox, $friend);
		}
}

private function printEditNotes ($pkey,$extension,$sip_peers) {
#
#   prints info Box
#

    echo '<span style="color: #696969;" >';
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

    echo 'Transport: <strong>' . $extension['transport'] . '</strong><br/>' . PHP_EOL;
   
    if (isset($extension['firstseen'])) {
    	$epoch = $extension['firstseen'];
    	$dt = new DateTime("@$epoch");
    	echo 'Firstprov: <strong>' . $dt->format('d-m-y H:i') . '</strong><br/>' . PHP_EOL;
    }

    if (isset($extension['lastseen'])) {
    	$epoch = $extension['lastseen'];
    	$dt = new DateTime("@$epoch");
    	echo 'Provisioned: <strong>' . $dt->format('d-m-y H:i') . '</strong><br/>' . PHP_EOL;
    } 
       
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
		
// for Yealinks, just use first three characters - they keep changing the last few
		
		if (preg_match ( " /Yealink/ ", $extension['device'])) {
			$images .= '/' . substr($extension['devicemodel'],0,3) . '.jpg';
		}
		else {
			$images .= '/' . $extension['devicemodel'] . '.jpg';
		}	
				    	
		if (file_exists("/opt/sark/www" . $images)) {	
			echo '<br/><br/><img src="' . $images . '" width="190px" />' . PHP_EOL;
		}
		else {
			$this->helper->logit("Phone Image not found $images ",1 );
		}
	}	
//    echo '</div>' . PHP_EOL;

}

private function checkHeadRoom($count,$pkey) {	
	
	return false;
}

private function checkThisMacForDups($mac) {	
	
	$sql = $this->dbh->prepare("select count(*) from ipphone where macaddr=?");
	$sql->execute(array($mac));
	$count = $sql->fetchColumn();	
	if ($count) {
		return true;
	}
	return false;
}

private function getVendorFromMac($mac) {
		$this->helper->logit("GETV mac is $mac  ",5 );
		$short_vendor = NULL;
		$shortmac = strtoupper(substr($mac,0,6));
		preg_match(" /^([0-9A-F][0-9A-F])([0-9A-F][0-9A-F])([0-9A-F][0-9A-F])$/ ", $shortmac,$matches);
		$findmac = $matches[1] . ':' . $matches[2] . ':' . $matches[3];
		$this->helper->logit("GETV findmac is $findmac  ",5 );
		$vendorline = `grep -i $findmac /opt/sark/www/sark-common/manuf.txt`;
		$delim="\t";
		$short_vendor_cols = explode($delim,$vendorline,3);
		if ( ! empty($short_vendor_cols[1]) ) {
			$short_vendor = $short_vendor_cols[1];
		}
		if (preg_match('/(Snom|Panasonic|Yealink|Polycom|Cisco|Gigaset|Aastra|Grandstream|Vtech)/i',$short_vendor_cols[2],$matches)) {
				$short_vendor = $matches[1];
		}
		else {
			if (preg_match('/(Snom|Panasonic|Yealink|Polycom|Cisco|Gigaset|Aastra|Grandstream|Vtech)/i',$short_vendor,$matches)) {
				$short_vendor = $matches[1];
			}
			else {
//				print_r($findmac);
				return 0;
			}
		}
// Not all Yealinks advertise themselvs as Yealink, someties it's YEALINK
		if (strcasecmp($short_vendor, 'yealink') == 0) {
			$short_vendor = "Yealink";
		}
		$this->helper->logit("GETV shortv is $short_vendor  ",5 );
		return $short_vendor;
}


private function removeQuotes(&$string) {
		$string = preg_replace ( "/\\\/", '', $string);

// $tuple['sipiaxfriend'] = preg_replace ( "/\\\/", '', $tuple['sipiaxfriend']);

}

}
