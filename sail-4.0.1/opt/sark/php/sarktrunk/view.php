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


Class trunk {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $params = array('server' => '127.0.0.1', 'port' => '5038');
	protected $span = 1;
	protected $smartlink;
	
public function showForm() {
	$params = array('server' => '127.0.0.1', 'port' => '5038');
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	
	echo '<body>';
	echo '<form id="sarktrunkForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Trunks';
	
	if (isset($_POST['new_x'])) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_GET['edit'])) { 
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
			$this->showEdit();
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
 * sign on to the AMI and build a peer array - the IAX stuff is Ast 1.8 only - 
 * Digium changed the format after 1.4 to be (almost) the same as the SIP output 
 * so we'll only retrieve IAX stuff if we are 1.8
 */
	$ast18 = false;
	$astrunning = false;
	
	$release = $this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'core show version'");
	if (preg_match(" /^Asterisk 1.8/ ", $release)) {
		$ast18 = true;
	}
	if ( $this->helper->check_pid() ) {	
		$astrunning = true;
	}
	$sip_peers = array(); 
	$iax_peers = array(); 
	$iax_lines = array();	
	
	if ( $astrunning ) {			
		$ami = new ami($this->params);
		$amiconrets = $ami->connect();
		if ( !$amiconrets ) {
			$this->myPanel->msg .= "  (AMI Connect failed)";
		}
		else {
			$ami->login('sark','mysark');
			$amisiprets = $ami->getSipPeers();
			$sip_peers = $this->build_peer_array($amisiprets);
			$amiiaxrets = $ami->getIaxPeers();
			$iax_peers = $this->build_peer_array($amiiaxrets);
			$ami->logout();
		}
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}

/* 
 * start page output
 */
  
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	echo '<div class="datadivmax">';

	echo '<table class="display" id="trunktable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('line'); 	
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('trunkname');
	$this->myPanel->aHeaderFor('carriertype');
	$this->myPanel->aHeaderFor('ipaddr');
//	$this->myPanel->aHeaderFor('latency');
	$this->myPanel->aHeaderFor('openroute');
	$this->myPanel->aHeaderFor('closeroute');
	$this->myPanel->aHeaderFor('Act');		
	$this->myPanel->aHeaderFor('Ping');	
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');	

	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
/*	
	$sql = "select li.pkey,cluster,trunkname,openroute,closeroute,peername,routeclassopen,routeclassclosed,active,ca.technology,ca.carriertype " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey WHERE " . 
			"ca.carriertype != 'DiD' AND ca.carriertype != 'CLID' AND ca.carriertype != 'Class'";
*/
	$sql = "select li.pkey,cluster,trunkname,openroute,closeroute,peername,routeclassopen,routeclassclosed,active,ca.technology,ca.carriertype " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey";
	$rows = $this->helper->getTable("lineio", $sql);
	foreach ($rows as $row ) {
		if ($row['carriertype'] == 'DiD' || $row['carriertype'] == 'CLID' || $row['carriertype'] == 'Class' ) {
			continue;
		}
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;		
		echo '<td >' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['trunkname'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['carriertype'] . '</td>' . PHP_EOL;
		
		$latency = 'N/A';
		$hostip = 'N/A';
		$status = 'N/A';
		
		
		
		$searchkey = $row['peername'];
		if ($row['active'] == 'YES' && $astrunning) {
			if ($row['technology'] == 'SIP' ) {
				if (preg_match(' /\((\d+)\sms/ ',$sip_peers [$searchkey]['Status'],$matches)) {
					$latency = 	$matches[1] . 'ms';
				}
				$hostip = $sip_peers [$searchkey]['IPaddress'];
				$status = $sip_peers [$searchkey]['Status'];
			}		
	
			else if ($row['technology'] == 'IAX2' && $ast18) {
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
		 		
		echo '<td >' . $hostip . '</td>' . PHP_EOL;
//		echo '<td >' . $latency . '</td>' . PHP_EOL;
		
		$this->helper->pkey = $row['openroute'];
		echo '<td >' . $this->helper->displayRouteClass($row['routeclassopen']) . '</td>' . PHP_EOL;
		$this->helper->pkey = $row['closeroute'];
		echo '<td >' . $this->helper->displayRouteClass($row['routeclassclosed']) . '</td>' . PHP_EOL;		
		echo '<td >' . $row['active'] . '</td>' . PHP_EOL;
		if (substr($status, 0, 2) == 'OK') {
			if ($row['technology'] == 'IAX2' || $row['technology'] == 'SIP') {
				echo '<td title = "Endpoint is on-line" >' . $latency . '</td>' . PHP_EOL;
			}
			else {		
				echo '<td><img src="/sark-common/actions/apply.png" border=0 title = "Endpoint is on-line" ></td>' . PHP_EOL;
			}		
		}
		else if (substr($status, 0, 11) == 'Unmonitored') {
			echo '<td title = "Endpoint is Unmonitored" >U/M</td>' . PHP_EOL;
		}
		else {			
			echo '<td><img src="/sark-common/actions/no.png" border=0 title = "Endpoint is Offline; Status is ' . $status .'" ></td>' . PHP_EOL;
		}

		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;

	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
}

private function showNew() {
	
	$pika = false;
	
	$this->myPanel->msg .= "Add a Trunk "; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->Button("save");
	echo '</div>';			
	
	$this->myPanel->Heading();
//	if (isset($this->message)) {

	if (!empty($this->error_hash)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}

/*
 * trunk container
 */ 	
	echo '<div class="editinsert">';

/*
 * trunk control dropdown
 */ 
	$this->myPanel->aLabelFor('Trunk Type');
	$this->myPanel->popUp('chooser', array('Choose a trunk type','GeneralSIP','GeneralIAX2','Custom','InterSARK'));

/*
 * Trunk variables - they will be hidden/revealed according to the chooser dropdown
 */	
	echo '<div id="divtrunkname">' . PHP_EOL;
	$this->myPanel->aLabelFor('trunkname');
	echo '<input type="text" name="trunkname" id="trunkname" size="10"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divpeername">' . PHP_EOL;	// Sibling only
	$this->myPanel->aLabelFor('s-peername');
	echo '<input type="text" name="peername" id="peername" size="25"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;	
	
	echo '<div id="divhost">' . PHP_EOL;
	$this->myPanel->aLabelFor('host');
	echo '<input type="text" name="host" id="host" size="20"  />' . PHP_EOL;	
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divusername">' . PHP_EOL;
	$this->myPanel->aLabelFor('username');
	echo '<input type="text" name="username" id="username" size="15"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divpassword">' . PHP_EOL;	
	$this->myPanel->aLabelFor('password');
	echo '<input type="text" name="password" id="password" size="15"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;

	echo '<div id="divregister">' . PHP_EOL;	
	$this->myPanel->aLabelFor('register');
	$this->myPanel->popUp('regthistrunk', array('NO','YES'));
	echo '</div>' . PHP_EOL;

/*
 *  Sibling only
 */	

	echo '<div id="divprivileged">' . PHP_EOL;						
	$this->myPanel->aLabelFor('privileged');
	$this->myPanel->popUp('privileged', array('YES','NO'));
	echo '</div>' . PHP_EOL;	
/*
 * DiD only
 */ 		
	echo '<div id="divsmartlink">' . PHP_EOL;		
	$this->myPanel->aLabelFor('smartlink');
	$this->myPanel->popUp('smartlink', array('NO','YES'));
	echo '</div>' . PHP_EOL;	
						
/*
 * Custom Trunk only
 */ 
	echo '<div id="divpredial">' . PHP_EOL;	
	$this->myPanel->aLabelFor('predial');
	echo '<input type="text" name="predial" id="predial" size="15"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;

	echo '<div id="divpostdial">' . PHP_EOL;	
	$this->myPanel->aLabelFor('postdial');
	echo '<input type="text" name="postdial" id="postdial" size="15"  />' . PHP_EOL;
	echo '</div>' . PHP_EOL;
	
	echo '<div id="divrouteable">' . PHP_EOL;			
	$this->myPanel->aLabelFor('routeable');
	$this->myPanel->popUp('routeable', array('NO','YES'));
	echo '</div>' . PHP_EOL;
	
	echo '<input type="hidden" id="carrier" name="carrier" value="" />' . PHP_EOL; 

/*
 * trunk container end
 */ 
	echo '</div>' . PHP_EOL;
	

}

private function saveNew() {
// save the data away

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
/*			
		case "DiD":
			$this->saveDiD($tuple);
			break;	
		case "CLID":
			$this->saveCLI($tuple);
			break;	
*/
		default: 
			return;						
	}
/*
 * call the creator routine and process any returned error
 */ 
 	if ($this->invalidForm != True) {
		$startkey = $tuple['pkey'];
		for ($i = 0; $i < $this->span; $i++) {			
			$ret = $this->helper->createTuple("lineio",$tuple);
			if ($ret != 'OK') {
				break;
			}
/*
 * increment the DiD number without losing any leading zeros;
 * we split it and increment just the right hand side (3 digits)
 * overflow breaks the loop
 */
			$left = substr($tuple['pkey'],0,-3);
			$right = substr($tuple['pkey'],-3);
			$right++;
			if (strlen($right) > 3) {
				$ret = "Number overflow on DiD create"; 
				break;
			}
			$tuple['pkey'] = $left.$right;
		}	
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Saved new Trunk " . $startkey . "!";
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

//	$tuple['pkey'] = strip_tags($_POST['didnumber']);
	
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
		$tuple['peername'] = 'peer' . mt_rand(1000, 9999);
		if ($tuple['technology'] == 'IAX2') {
			$tuple['desc'] = $tuple['username'];
		}
		else {
			$tuple['desc'] = "user" . mt_rand(1000, 9999);
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
	$this->validator->addValidation("host","req","No host url");
		
	if ($this->validator->ValidateForm()) { 		
		$tuple['trunkname'] 	= strip_tags($_POST['trunkname']);	
		$tuple['host'] 			= strip_tags($_POST['host']);		
		$tuple['password']		= strip_tags($_POST['password']);			
		$tuple['carrier']		= $_POST['carrier'];
		$tuple['privileged']	= $_POST['privileged'];
		$tuple['technology']	= 'IAX2';
		
		if ($tuple['privileged'] == "YES") {
			$tuple['pkey']  	= strip_tags($_POST['peername']) . php_uname("n");
			$tuple['username'] 	= php_uname("n") . strip_tags($_POST['peername']);
			$tuple['peername']  = $tuple['pkey'];
			$tuple['desc']  	= $tuple['pkey'];				
		}
		else {
			$tuple['pkey']  	= strip_tags($_POST['peername']) . "~" . php_uname("n");
			$tuple['username'] 	= php_uname("n") . "~" . strip_tags($_POST['peername']);
			$tuple['peername']  = $tuple['pkey'];
			$tuple['desc']  	= $tuple['pkey'];					
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
  
	$pkey = $_GET['pkey']; 
	$tuple = $this->dbh->query("select li.*,ca.carriertype from lineio li inner join Carrier ca on li.carrier = ca.pkey where li.pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
	$printline = "Trunk " . $tuple['technology'] . "/" . $tuple['pkey'];
	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->override="update";
	$this->myPanel->Button("save");
	echo '</div>';	
	
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
		
	echo '<div class="datadivtabedit">';
		
	echo '<div id="pagetabs">' . PHP_EOL;
	echo '<ul>' . PHP_EOL;
	echo '<li><a href="#route">Routing</a></li>' . PHP_EOL;
	echo '<li><a href="#line" >Line</a></li>' . PHP_EOL;
	if ($tuple['technology'] == 'SIP' ||  $tuple['technology'] == 'IAX2' ) {
		echo  '<li><a href="#peer">Peer</a></li>' . PHP_EOL;
		echo  '<li><a href="#user">User</a></li>' . PHP_EOL;
	}
    echo '</ul>' . PHP_EOL;	

#
#   TAB Route
#
    echo '<div id="route" >';
    
 	$this->myPanel->aLabelFor('trunkname');
	echo '<input type="text" name="trunkname" id="trunkname" size="20"  value="' . $tuple['trunkname'] . '"  />' . PHP_EOL;   
    
    $this->myPanel->aLabelFor('active');
    $this->myPanel->selected = $tuple['active'];
	$this->myPanel->popUp('active', array('NO','YES'));
	
    $this->myPanel->aLabelFor('cluster');
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
	
	if ( $tuple['technology'] == 'Analogue' || $tuple['technology'] == 'DAHDI' ) { 
		$this->myPanel->aLabelFor('faxonoff');
		$this->myPanel->selected = $tuple['faxdetect'];
		$this->myPanel->popUp('faxdetect', array('NO','YES'));
	}	
	
	if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' )  {
		$this->myPanel->aLabelFor('match');
		echo '<input type="text" name="match" id="match" size="3"  value="' . $tuple['match'] . '"  />' . PHP_EOL;
	}
	
	$this->myPanel->aLabelFor('swoclip');
    $this->myPanel->selected = $tuple['swoclip'];
	$this->myPanel->popUp('swoclip', array('NO','YES'));	
		
    if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' && $tuple['pkey'] != 'Analog-In' )  {
		$this->myPanel->aLabelFor('transform');
		echo '<input type="text" name="transform" id="transform" size="10"  value="' . $tuple['transform'] . '"  />' . PHP_EOL;
	}
	
	if ( $tuple['routeable'] = 'YES' || $tuple['carriertype'] != 'group' )  {
		$this->myPanel->aLabelFor('openroute');
		$this->myPanel->selected = $tuple['openroute'];
		$this->myPanel->sysSelect('openroute',false,false,true) . PHP_EOL;
		
		$this->myPanel->aLabelFor('closeroute',false,false,true);
		$this->myPanel->selected = $tuple['closeroute'];
		$this->myPanel->sysSelect('closeroute') . PHP_EOL;	
	}    

	echo '</div>' . PHP_EOL;
	
#
#   TAB Line
#
    echo '<div id="line" >';
    
    $this->myPanel->aLabelFor('tag');
	echo '<input type="text" name="tag" id="tag" size="20"  value="' . $tuple['tag'] . '"  />' . PHP_EOL;
    
    $this->myPanel->aLabelFor('inprefix');
	echo '<input type="text" name="inprefix" id="tag" size="1"  value="' . $tuple['inprefix'] . '"  />' . PHP_EOL;   
	
	$this->myPanel->aLabelFor('moh');
    $this->myPanel->selected = $tuple['moh'];
	$this->myPanel->popUp('moh', array('NO','YES'));	
	
    $this->myPanel->aLabelFor('disapass');
	echo '<input type="text" name="disapass" id="tag" size="4"  value="' . $tuple['disapass'] . '"  />' . PHP_EOL; 
	
	if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'Class' )  {
		$this->myPanel->aLabelFor('callerid');
		echo '<input type="text" name="callerid" id="callerid" size="12"  value="' . $tuple['callerid'] . '"  />' . PHP_EOL;
	}	
	
	if ( $tuple['carriertype'] != 'group' )  {
		$this->myPanel->aLabelFor('alertinfo');
		echo '<input type="text" name="alertinfo" id="alertinfo" size="20"  value="' . $tuple['alertinfo'] . '"  />' . PHP_EOL;
	}	
	
	if ( $tuple['technology'] == 'SIP' || $tuple['technology'] == 'IAX2' )  {
		$this->myPanel->aLabelFor('register');
		echo '<input type="text" name="register" id="register" size="50"  value="' . $tuple['register'] . '"  />' . PHP_EOL;
	}
	if ( $tuple['technology'] != 'DiD' && $tuple['technology'] != 'CLID' && $tuple['technology'] != 'Class' )  {	
		$this->myPanel->aLabelFor('callprogress');
		$this->myPanel->selected = $tuple['callprogress'];
		$this->myPanel->popUp('callprogress', array('NO','YES'));
	}

	if ( $tuple['carriertype'] == 'group' )  {
		$this->myPanel->aLabelFor('predial');
		echo '<input type="text" name="predial" id="predial" size="20"  value="' . $tuple['predial'] . '"  />' . PHP_EOL; 	
		
		$this->myPanel->aLabelFor('postdial');
		echo '<input type="text" name="postdial" id="postdial" size="20"  value="' . $tuple['postdial'] . '"  />' . PHP_EOL; 
	}					  	 	
    echo '</div>' . PHP_EOL;	

/*
 *   TAB Peer
 */
    if ($tuple['technology'] == 'SIP' ||  $tuple['technology'] == 'IAX2') {
    	echo '<div id="peer">';
    	$this->myPanel->aLabelFor('peername');
    	echo '<input type="text" name="peername" id="peername" size="20"  value="' . $tuple['peername'] . '"  />' . PHP_EOL;
    	echo '<br/><br/>'. PHP_EOL;
		echo '<textarea class="databox" name="sipiaxpeer" id="sipiaxpeer">' . $tuple['sipiaxpeer'] . '</textarea>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	}
	
/*
 *   TAB User
 */
	if ($tuple['technology'] == 'SIP' ||  $tuple['technology'] == 'IAX2') {
    	echo '<div id="user" >';
    	$this->myPanel->aLabelFor('username');
    	echo '<input type="text" name="desc" id="desc" size="20"  value="' . $tuple['desc'] . '"  />' . PHP_EOL;
    	echo '<br/><br/>'. PHP_EOL;    	
		echo '<textarea class="databox" name="sipiaxuser" id="sipiaxuser">' . $tuple['sipiaxuser'] . '</textarea>' . PHP_EOL;
		echo '</div>' . PHP_EOL;
	}
	echo '</div>' . PHP_EOL;
	echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $tuple['pkey'] . '"  />' . PHP_EOL; 
	echo '</div>' . PHP_EOL;
}

private function saveEdit() {
/*
 * save data from an update(edit)
 */ 
 	
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
		$this->message = "Saved new Trunk " . $startkey . "!";
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash[trunk] = $ret;	
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

}
