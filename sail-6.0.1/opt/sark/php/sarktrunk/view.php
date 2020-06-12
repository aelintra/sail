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


Class sarktrunk {
	
	protected $message; 
	protected $head = "Trunks";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $astrunning=false;	
	protected $span = 1;
	protected $smartlink;
	protected $myBooleans = array(
		'active',
		'callprogress',
		'moh',		
		'swoclip',
		'regthistrunk',
		'routeable',
		'privileged'		
	);
	
public function showForm() {
	$params = array('server' => '127.0.0.1', 'port' => '5038');
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}
	
//	$this->myPanel->pagename = 'Trunks';
		
	if (isset($_POST['new']) || isset($_GET['new'])) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_POST['delete'])) { 
		$this->deleteRow();
		return;  		
	}
		
	if (isset($_GET['edit'])) { 
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
		$this->message = " - Committed!";	
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
 * sign on to the AMI and build a peer array - the IAX stuff is Ast >= 1.8 only - 
 * Digium changed the format after 1.4 to be (almost) the same as the SIP output 
 * so we'll only work with 1.8 or higher
 */

	$sip_peers = array(); 
	$iax_peers = array(); 
	$iax_lines = array();	
	
	if ( $this->astrunning ) {
		$amiHelper = new amiHelper();
		$sip_peers = $amiHelper->get_peer_array();	
		$iax_peers = $amiHelper->get_peer_array(true);			
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}

/* 
 * start page output
 */

/*
	if ( $_SESSION['user']['pkey'] == 'admin' ) {
		echo '<a  href="/php/downloadpdf.php?pdf=trunks"><img id="pdfprint" src="/sark-common/buttons/print.png" alt = "Click to Download PDF" title = "Click to Download PDF" ></a>' . PHP_EOL;									
	}
*/
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarktrunkForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$bigTable=true;
	$this->myPanel->responsiveSetup($bigTable);
//	$this->myPanel->subjectBar("Trunks");

	echo '<form id="sarktrunkForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('trunktable',' w3-tiny');
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	
	$this->myPanel->aHeaderFor('trunkname'); 	
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('description',false,'w3-hide-small w3-hide-medium');
//	$this->myPanel->aHeaderFor('carriertype');
	$this->myPanel->aHeaderFor('ipaddr',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('Active?',false,'w3-hide-small  w3-hide-medium');		
	$this->myPanel->aHeaderFor('tstate');	
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');	

	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$sql = "select li.pkey,cluster,description,trunkname,peername,routeclassopen,routeclassclosed,active,ca.technology,ca.carriertype " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey";
	$rows = $this->helper->getTable("lineio", $sql,true,false,'li.pkey');
	foreach ($rows as $row ) {
		if ($row['carriertype'] == 'DiD' || $row['carriertype'] == 'CLID' || $row['carriertype'] == 'Class' ) {
			continue;
		}
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		
		
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small  w3-hide-medium">' . $row['cluster'] . '</td>' . PHP_EOL;
		if (isset ($row['description'])) {
			echo '<td class="w3-hide-small w3-hide-medium">' . $row['description'] . '</td>' . PHP_EOL;
		}
		else {
			echo '<td class="w3-hide-small w3-hide-medium">' . $row['trunkname'] . '</td>' . PHP_EOL;
		}
//		echo '<td class="icons">' . $row['carriertype'] . '</td>' . PHP_EOL;
		
		$latency = 'N/A';
		$hostip = 'N/A';
		$status = 'N/A';
		
		
		
		$searchkey = $row['peername'];
		if ($row['active'] == 'YES' && $this->astrunning) {
			if ($row['technology'] == 'SIP' ) {
				if (preg_match(' /\((\d+)\sms/ ',$sip_peers [$searchkey]['Status'],$matches)) {
					$latency = 	$matches[1] . 'ms';
				}
				$hostip = $sip_peers [$searchkey]['IPaddress'];
				$status = $sip_peers [$searchkey]['Status'];
			}		
	
			else if ($row['technology'] == 'IAX2') {
				if (preg_match(' /\((\d+)\sms/ ',$iax_peers [$searchkey]['Status'],$matches)) {
					$latency = 	$matches[1] . 'ms';
				}
				$hostip = $iax_peers[$searchkey]['IPaddress'];
				$status = $iax_peers[$searchkey]['Status'];
			}
			else {
				$status = 'OK';
			}
		}
		 		
		echo '<td class="w3-hide-small">' . $hostip . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['active'] . '</td>' . PHP_EOL;
		if ($row['technology'] == 'IAX2' || $row['technology'] == 'SIP') {
			echo '<td  title = "Endpoint status" >' . $status . '</td>' . PHP_EOL;
		}
		else {		
			echo '<td>Unknown</td>' . PHP_EOL;
		}		

		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();
}

private function showNew() {
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarktrunkForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Trunk");

	echo '<form id="sarktrunkForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

/*
 * trunk control dropdown
 */ 
	$this->myPanel->displayPopupFor('chooser','Choose a trunk type',Array('Choose a trunk type','GeneralSIP','GeneralIAX2','Custom','InterSARK')); 
/*
 * Trunk variables - they will be hidden/revealed according to the chooser dropdown
 */	
	echo '<div id="divtrunkname">' . PHP_EOL;
	$this->myPanel->displayInputFor('trunkname','text');
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divpeername">' . PHP_EOL;	// Sibling only
	$this->myPanel->displayInputFor('peername','text');
	echo '</div>' . PHP_EOL;	
	
	echo '<div id="divhost">' . PHP_EOL;
	$this->myPanel->displayInputFor('host','text');	
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divusername">' . PHP_EOL;
	$this->myPanel->displayInputFor('username','text');
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divpassword">' . PHP_EOL;	
	$this->myPanel->displayInputFor('password','text');
	echo '</div>' . PHP_EOL;

	echo '<div id="divregister">' . PHP_EOL;	
	$this->myPanel->displayBooleanFor('regthistrunk','YES');
	echo '</div>' . PHP_EOL;

/*
 *  Sibling only
 */	

	echo '<div id="divprivileged">' . PHP_EOL;						
	$this->myPanel->displayBooleanFor('privileged','YES');
	echo '</div>' . PHP_EOL;	
						
/*
 * Custom Trunk only
 */ 
	echo '<div id="divpredial">' . PHP_EOL;	
	$this->myPanel->displayInputFor('predial','text');
	echo '</div>' . PHP_EOL;

	echo '<div id="divpostdial">' . PHP_EOL;	
	$this->myPanel->displayInputFor('postdial','text');
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divrouteable">' . PHP_EOL;			
	$this->myPanel->displayBooleanFor('routeable','YES');
	echo '</div>' . PHP_EOL;
	
	echo '<input type="hidden" id="carrier" name="carrier" value="" />' . PHP_EOL; 

	echo '</div>';
	$this->myPanel->responsiveClose();

	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	

}

private function saveNew() {
// save the data away
	$this->myPanel->xlateBooleans($this->myBooleans);
	$tuple = array();
/*
 * call the correct routine to prepare the record array
 */ 	
	switch ($_POST['carrier']) {
		case "GeneralSIP":
		case "GeneralIAX2":
			$this->saveSIPIAX($tuple);
			break;		
		case "InterSARK":
			$this->saveSibling($tuple);
			break;				
		case "Custom":
			$this->saveCustom($tuple);
			break;
		default: 
			return;						
	}
/*
 * call the creator routine and process any returned error
 */ 
 	if ($this->invalidForm != True) {		
		$ret = $this->helper->createTuple("lineio",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = " Saved!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!(3)</B>";	
			$this->error_hash[trunk] = $ret;	
		}
	}	
}

private function saveSIPIAX(&$tuple) {

	$this->validator = new FormValidator();
	$this->validator->addValidation("host","req","No host address");
	$this->validator->addValidation("trunkname","req","No trunk name");

	
	$tuple['pkey'] = strip_tags($_POST['trunkname']);
	
	if ($this->validator->ValidateForm()) {
		$tuple['trunkname'] 	= strip_tags($_POST['trunkname']);	
		$tuple['host'] 			= strip_tags($_POST['host']);		
		$tuple['username']		= strip_tags($_POST['username']);
		$tuple['password']		= strip_tags($_POST['password']);			
		$tuple['carrier']		= $_POST['carrier'];
		if ($tuple['carrier'] == 'GeneralSIP') {
			$tuple['technology']	= 'SIP';
		}
		else {
			$tuple['technology']	= 'IAX2';
		}
		if (!empty($_POST['username'])) {		
			$tuple['peername'] = strip_tags($_POST['username']);
		}
		else {
			$tuple['peername'] = "peer" . mt_rand(10000, 99999);
		}
				
		if ($tuple['technology'] == 'IAX2') {
			$tuple['desc'] = $tuple['peername'];
		}
					
		if ( $_POST['regthistrunk'] == "YES" ) {
			$tuple['register'] = $tuple['username'].':'.$tuple['password'].'@'.$tuple['host'].'/'.$tuple['username'];
		}
									
		$template = $this->copyTemplates ($tuple);
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);    
}

private function saveSibling(&$tuple) {

	$this->validator = new FormValidator();
	$this->validator->addValidation("trunkname","req","No hostname");
	$this->validator->addValidation("host","req","No host address");
	$tuple['pkey']  	= strip_tags($_POST['trunkname']);
		
	if ($this->validator->ValidateForm()) { 		
		$tuple['trunkname'] 	= strip_tags($_POST['trunkname']);	
		$tuple['host'] 			= strip_tags($_POST['host']);		
		$tuple['password']		= strip_tags($_POST['password']);			
		$tuple['carrier']		= $_POST['carrier'];
		$tuple['privileged']	= $_POST['privileged'];
		$tuple['technology']	= 'IAX2';
		
		if ($tuple['privileged'] == "YES") {
			
			$tuple['username'] 	= php_uname("n") . strip_tags($_POST['peername']);
			$tuple['peername']  = strip_tags($_POST['peername']) . php_uname("n");
			$tuple['desc']  	= $tuple['peername'] ;				
		}
		else {
	
			$tuple['username'] 	= php_uname("n") . "~" . strip_tags($_POST['peername']);
			$tuple['peername']  = strip_tags($_POST['peername']) . "~" . php_uname("n");;
			$tuple['desc']  	= $tuple['peername'] ;					
		}	 										
		$template = $this->copyTemplates ($tuple);	
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);   
}

private function saveCustom(&$tuple) {

	$this->validator = new FormValidator();
	$this->validator->addValidation("trunkname","req","No trunk name ");
	
	if ($this->validator->ValidateForm()) {
		$tuple['pkey'] 			= strip_tags($_POST['trunkname']);
		$tuple['trunkname'] 	= strip_tags($_POST['trunkname']);	
		$tuple['host'] 			= strip_tags($_POST['host']);		
		$tuple['predial']		= strip_tags($_POST['predial']);
		$tuple['postdial']		= strip_tags($_POST['postdial']);			
		$tuple['carrier']		= $_POST['carrier'];
		$tuple['technology']	= $_POST['carrier'];
		$tuple['routeable']		= $_POST['routeable'];			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);    
}


private function showEdit() {
/*
 * General update page.  Jquery controls tabs
 */

	if (isset($_POST['pkey'])) {
		$pkey = $_POST['pkey'];
	}
	else if (isset($_GET['pkey'])) {
		$pkey = $_GET['pkey'];
	} 
	else if (isset($_POST['trunkname'])) {
		$pkey = $_POST['trunkname'];
	} 
			
	$tuple = $this->dbh->query("select li.*,ca.carriertype from lineio li inner join Carrier ca on li.carrier = ca.pkey where li.pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
//	$printline = "Trunk " . $tuple['technology'] . "/" . $tuple['pkey'];
	
	if ( $this->astrunning ) {			
		$amiHelper = new amiHelper();
		$sip_peers = $amiHelper->get_peer_array();	
		$iax_peers = $amiHelper->get_peer_array(true);
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarktrunkForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar($tuple['technology'] . "/" . $tuple['pkey']);

	echo '<form id="sarktrunkForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

/*	
	echo '<br/>' . PHP_EOL;
	echo '<div class="titlebar">' . PHP_EOL; 			
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$ret = $this->helper->getLc();	
	if (!$ret) {	
		$this->myPanel->Button("new");
	}	
	$this->myPanel->override="update";
	$this->myPanel->Button("save");
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

	echo '<div class="globalwrapper">' . PHP_EOL;

	
	if ($tuple['technology'] == 'IAX2') {
		$this->printEditNotes($tuple['peername'],$tuple,$iax_peers);
	}
	else {
		$this->printEditNotes($tuple['peername'],$tuple,$sip_peers);
	}
	echo '<div class="exttabedit">';
		
	echo '<div id="pagetabs">' . PHP_EOL;
	echo '<ul>' . PHP_EOL;
	echo '<li><a href="#general">General</a></li>' . PHP_EOL;
	echo '<li><a href="#line" >Line</a></li>' . PHP_EOL;
	if ($tuple['technology'] == 'SIP' ||  $tuple['technology'] == 'IAX2' ) {
		echo  '<li><a href="#peer">Peer</a></li>' . PHP_EOL;
	}
	if ($tuple['technology'] == 'IAX2' ) {
		echo  '<li><a href="#user">User</a></li>' . PHP_EOL;
	}
    echo '</ul>' . PHP_EOL;	
*/

    $this->myPanel->displayBooleanFor('active',$tuple['active']);

    if ($tuple['technology'] == 'SIP' ||  $tuple['technology'] == 'IAX2') {
    	echo '<div id="peer">';
    	$this->myPanel->displayInputFor('peername','text',$tuple['peername']);
		$this->myPanel->aLabelFor('sipiaxpeer');
		$this->myPanel->displayFile($tuple['sipiaxpeer'],"sipiaxpeer");
		echo '</div>' . PHP_EOL;
	}
	
/*
 *   TAB User
 */
	if ($tuple['technology'] == 'IAX2') {
    	echo '<div id="user" >';
    	$this->myPanel->displayInputFor('desc','text',$tuple['desc']);
		$this->myPanel->displayFile($tuple['sipiaxuser'],"sipiaxuser");
		echo '</div>' . PHP_EOL;
	}

	if ( $tuple['technology'] == 'SIP' || $tuple['technology'] == 'IAX2' )  {
		$this->myPanel->displayInputFor('register','text',$tuple['register']);
	}

	if (isset($tuple['description'])) {
		$this->myPanel->displayInputFor('description','text',$tuple['description']); 
	}
	else {
		$this->myPanel->displayInputFor('description','text',$tuple['trunkname']); 
	}

// removed Jan 2019 - trunks are shared common    
/*
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';
*/
	$this->myPanel->subjectBar('Line Settings');
	
	if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' )  {
		$this->myPanel->displayInputFor('match','text',$tuple['match']); 
	}
 			
    if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' && $tuple['pkey'] != 'Analog-In' )  {
		$this->myPanel->displayInputFor('transform','text',$tuple['transform']); 
	}
	
	if ( $tuple['routeable'] == 'YES' )  {	
		echo '<div class="w3-margin-bottom">';
		$this->myPanel->aLabelFor('openroute');
		echo '</div>'; 	
		$this->myPanel->selected = $tuple['openroute'];
		$this->myPanel->sysSelect('openroute',false,false,true) . PHP_EOL;
		$this->myPanel->aHelpBoxFor('openroute');

		echo '<div class="w3-margin-bottom">';
		$this->myPanel->aLabelFor('closeroute');
		echo '</div>';
		$this->myPanel->selected = $tuple['closeroute'];
		$this->myPanel->sysSelect('closeroute',false,false,true) . PHP_EOL;
		$this->myPanel->aHelpBoxFor('closeroute');	
	}    

	
    
	$this->myPanel->displayInputFor('tag','text',$tuple['tag']); 
	$this->myPanel->displayInputFor('inprefix','text',$tuple['inprefix']);   
	$this->myPanel->displayBooleanFor('moh',$tuple['moh']);	
	$this->myPanel->displayInputFor('disapass','text',$tuple['disapass']);	 
	
	if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' )  {
		$this->myPanel->displayInputFor('callerid','text',$tuple['callerid']);
	}		
	
	if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'CLID' && $tuple['technology'] != 'Class' )  {	
		$this->myPanel->displayBooleanFor('callprogress',$tuple['callprogress']);
	}

	if ($tuple['carrier'] == "InterSARK"  || $tuple['carrier'] == "SailToSail") {
		$this->myPanel->displayBooleanFor('privileged',$tuple['privileged']);
	}

	if ( $tuple['carriertype'] == 'group' )  {
		$this->myPanel->displayInputFor('predial','text',$tuple['predial']);	
		$this->myPanel->displayInputFor('postdial','text',$tuple['postdial']); 
	}					  	 	
	
	echo '<input type="hidden" name="pkey" id="pkey" value="' . $tuple['pkey'] . '"  />' . PHP_EOL; 

	echo '</div>';
	$this->myPanel->responsiveClose();
	
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	

//	$this->myPanel->navRowDisplay("lineio", $pkey, "TRUNK");
}

private function saveEdit() {
/*
 * save data from an update(edit)
 */ 
 		$this->myPanel->xlateBooleans($this->myBooleans);
/*
 * 	call the tuple builder to create a table row array 
 */  	
		$this->helper->buildTupleArray($_POST,$tuple);

/*
 * update routeclass
 */ 
	if (array_key_exists('openroute',$tuple)) {
		 $tuple['routeclassopen'] = $this->helper->setRouteClass($tuple['openroute']);
	}
	if (array_key_exists('closeroute',$tuple)) {
		 $tuple['routeclassclosed'] = $this->helper->setRouteClass($tuple['closeroute']);
	}
/*
 * call the setter
 */ 
	$ret = $this->helper->setTuple("lineio",$tuple);
/*
 * flag errors
 */ 	
	if ($ret == 'OK') {
//		$this->helper->commitOn();	
		$this->message = " Updated Trunk " . $tuple['pkey'] . "!";
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash[trunk] = $ret;	
	}	
}

private function deleteRow() {
	$pkey = $_POST['pkey'];
	$this->helper->delTuple("lineio",$pkey); 
	$this->message = "Deleted extension " . $pkey;
	$this->myPanel->msgDisplay('Deleted trunk ' . $pkey);
	$this->myPanel->navRowDisplay("lineio", $pkey);
//	$this->helper->commitOn();	
}

private function copyTemplates (&$tuple) {
/*
 * get the carrier template from the database and
 * substitute into it the values from this create
 */ 
		
        $template = $this->dbh->query("SELECT sipiaxuser,sipiaxpeer FROM Carrier WHERE pkey = '" . $_POST['carrier'] . "'")->fetch(PDO::FETCH_ASSOC);
 
        if (isset( $template['sipiaxpeer'] )) {
      		$template['sipiaxpeer'] = preg_replace ('/username=/',"username=" . $tuple['username'], $template['sipiaxpeer']);
      		$template['sipiaxpeer'] = preg_replace ('/fromuser=/',"fromuser=" . $tuple['username'], $template['sipiaxpeer']);
      		$template['sipiaxpeer'] = preg_replace ('/secret=/',"secret=" . $tuple['password'], $template['sipiaxpeer']);
      		$template['sipiaxpeer'] = preg_replace ('/host=/',"host=" . $tuple['host'], $template['sipiaxpeer']);
      		$template['sipiaxpeer'] = preg_replace ('/^\s+/',"", $template['sipiaxpeer']);
      		$template['sipiaxpeer'] = preg_replace ('/\s+$/',"", $template['sipiaxpeer']);
            if ( $_POST['carrier'] == "InterSARK") {
				$template['sipiaxpeer'] = preg_replace ('/mainmenu/',"priv_sibling", $template['sipiaxpeer']);
				$template['sipiaxpeer'] = preg_replace ('/trunk=yes/',"trunk=no", $template['sipiaxpeer']);
            }  
            if ( !preg_match(' /allow=/ ',$template['sipiaxpeer'])) {				
        		$template['sipiaxpeer'] .= "\ndisallow=all\nallow=alaw\nallow=ulaw";
        	}       	
        }
        
        if (isset( $template['sipiaxuser'] )) {
      		$template['sipiaxuser'] = preg_replace ('/username=/',"username=" . $tuple['username'], $template['sipiaxuser']);
      		$template['sipiaxuser'] = preg_replace ('/fromuser=/',"fromuser=" . $tuple['username'], $template['sipiaxuser']);
      		$template['sipiaxuser'] = preg_replace ('/secret=/',"secret=" . $tuple['password'], $template['sipiaxuser']);
        	$template['sipiaxuser'] = preg_replace ('/^\s+/',"", $template['sipiaxuser']);
      		$template['sipiaxuser'] = preg_replace ('/\s+$/',"", $template['sipiaxuser']);
			
			if ( $_POST['carrier'] == "InterSARK" && $_POST['privileged'] == "NO") {		
				$template['sipiaxuser'] = preg_replace ('/context=internal/',"context=mainmenu", $template['sipiaxuser']);
            }           
        }
        $tuple['sipiaxpeer'] = $template['sipiaxpeer'];
		$tuple['sipiaxuser'] = $template['sipiaxuser'];

}

private function printEditNotes ($pkey,$endpoint,$si_peers) {
#
#   prints info Box
#

	echo '<div  class="extnotes">' . PHP_EOL;
    echo '<span style="font-weight:bold; color: #696969;"></span><br/><br/>';
    echo 'Peer: <strong>' . $pkey . '</strong><br/>' . PHP_EOL;
    echo 'Name: <strong>' . $endpoint['pkey'] . '</strong><br/>' . PHP_EOL;
    echo 'Type: <strong>' . $endpoint['technology'] . '</strong><br/>' . PHP_EOL;
    
    if (!empty ($si_peers [$pkey])) {
		echo 'State: <strong>' . $si_peers [$pkey]['Status'] . '</strong><br/>' . PHP_EOL; 	
		if (preg_match(' /^OK/ ', $si_peers [$pkey]['Status'])) {
			if (isset ($si_peers [$pkey]['IPport'])) {
				echo 'IPport: <strong>' . $si_peers [$pkey]['IPport'] . '</strong><br/>' . PHP_EOL;
			}		
			if (isset ($si_peers [$pkey]['IPaddress'])) {
				echo 'IP: <strong>' . $si_peers [$pkey]['IPaddress'] . '</strong><br/>' . PHP_EOL;
			}
		}		
	}
    echo '</div>' . PHP_EOL;

}

}
