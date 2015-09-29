<?php
// sarkuser class
// Developed by CoCo
// Copyright (C) 2012 CoCoSoFt
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

Class user {
	
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
		
	echo '<body>';	
	echo '<form id="sarkuserForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Users';
	
	if (isset($_POST['new_x'])) { 
		$this->showNew();
		return;		
	}
	if (isset($_POST['save_x'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (isset($_POST['jsave_x'])) { 
		$this->setPerms();	
	}
	if (isset($_GET['permedit'])) { 
		$this->showPerms();	
		return;
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
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
//	$this->myPanel->commitButton();
	echo '</div>';
		
	$this->myPanel->Heading();

	echo '<div class="datadivnarrow">';

	echo '<table class="display" id="usertable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;

	
	$this->myPanel->aHeaderFor('user');
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('extension');		 
	$this->myPanel->aHeaderFor('userpass');	
	$this->myPanel->aHeaderFor('useremail');
	$this->myPanel->aHeaderFor('userspan');
	$this->myPanel->aHeaderFor('userscope');
	$this->myPanel->aHeaderFor('del');
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

 	$rows = $this->helper->getTable("user");
	foreach ($rows as $row ) {
		if ($row['pkey'] == 'admin' ) {
			continue;
		}
		else {
			echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
			echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['cluster'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['extension'] . '</td>' . PHP_EOL;				
			echo '<td >********</td>' . PHP_EOL;
			echo '<td >' . $row['email'] . '</td>' . PHP_EOL;			
			
			echo '<td >' . $row['selection'] . '</td>' . PHP_EOL;
//			echo '<td >' . $row['readonly'] . '</td>' . PHP_EOL;
			$get = '?permedit=yes&amp;pkey=';
			$get .= $row['pkey'];	
			$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		
			$get = '?id=' . $row['pkey'];		
			$this->myPanel->ajaxdeleteClick($get);			echo '</tr>'. PHP_EOL;
		}
	}
	echo '</tbody>' . PHP_EOL;
	echo '</table>';
	echo '</div>';	
}

private function showNew() {
	$this->myPanel->msg = "Add New User "; 
	
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

	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('user');
	echo '<input type="text" name="pkey" id="pkey"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('password');
	echo '<input type="password" name="password" id="password"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('email');
	echo '<input type="text" email" name="email" id="email"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('cluster');
	$this->myPanel->displayCluster();
	$this->myPanel->aLabelFor('scope');
	$this->myPanel->popUp('selection', array("enduser", "poweruser", "tenant","all"));	
	echo '</div>';	
}

private function saveNew() {
	$tuple = array();
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Username");
    $this->validator->addValidation("pkey","alpha","Username must be alpha(no spaces)");    
    $this->validator->addValidation("password","req","Please fill in a password");
    $this->validator->addValidation("password","alnum","Password must be alphanumeric (no spaces)");
    $this->validator->addValidation("password","minlen=6","Password must be minimum 6 chars");
//    $this->validator->addValidation("email","req","Please fill in Email");
    $this->validator->addValidation("email","email","The input for Email should be a valid email value");
    //Now, validate the form
    if ($this->validator->ValidateForm()) {
		
		
		$tuple['pkey']  		=  strip_tags($_POST['pkey']);
		$tuple['cluster'] 	=  strip_tags($_POST['cluster']);
		$tuple['email'] 		=  strip_tags($_POST['email']);
		$tuple['password']	=  strip_tags($_POST['password']);
		$tuple['selection'] 	=  strip_tags($_POST['selection']); 
		
		$ret = $this->helper->createTuple("user",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$data = '/usr/bin/htpasswd -b /opt/sark/passwd/htpasswd ' . $tuple['pkey'] . ' ' . $tuple['password'];
			$this->helper->request_syscmd ($data);
			$this->message = "Saved new user " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['userinsert'] = $ret;	
		}
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}


private function showPerms() {
	
	$ignore = array(
		"Globals" => True,
		"COS" => True,
		"Backup" => True,
		"Multi-Tenant" => True,
		"PCI Cards" => True,
		"Templates" => True,
		"Asterisk Edit" => True,
		"Users" => True
	);
	$userspan = array(
		"enduser" => 1,
		"poweruser" => 11,
		"tenant" => 21,
		"all" => 31
	);
		
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->override = "jsave";
	$this->myPanel->Button("save");
	echo '</div>';	
	
	$user_pkey = $_GET['pkey'];
			
	$this->myPanel->msg .= "Permissions for user $user_pkey"; 
	$this->myPanel->Heading();

	echo '<br/><br/>';
	
	$user = $this->dbh->query("select pkey,selection from user where pkey='" . $user_pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$userweight = $userspan[$user['selection']];
		
	$res = $this->dbh->prepare("select pkey,displayname,weight from panel order by displayname");
	$res->execute();
	$panel = $res->fetchAll();
	
	echo PHP_EOL;
//	echo '<br/><span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">User -> ' . $user_pkey . '</span><br/>';
	echo '<input type="hidden" name="pkey" value="' . $user_pkey  . '" />' . PHP_EOL; 
	
	foreach ($panel as $j => $panelrow) {
		if (array_key_exists($panelrow['displayname'],$ignore)) {
			continue;
		}
		if ($panelrow['weight'] > $userweight) {
			continue;
		}			
		$res = $this->dbh->query("SELECT user_pkey FROM userpanel where user_pkey = '" . 
									$user_pkey . 
									"' AND panel_pkey ='" . 
									$panelrow['pkey'] . 
									"'" )->fetch(PDO::FETCH_ASSOC);

		if (isset($res['User_pkey'])) {
			echo '<input type="checkbox"  checked="yes" name="appl[]" value="' . $panelrow['displayname'] . '" />' . $panelrow['displayname'] . "<br/>\n"; 
		}
		else {
			echo '<input type="checkbox"  name="appl[]" value="' . $panelrow['displayname'] . '" />' . $panelrow['displayname'] . "<br/>\n"; 
		}										
		echo PHP_EOL;
	} 	
}


private function setPerms() {

	$user_pkey = $_POST['pkey'];
	$arr = $_POST['appl'];
	$panel = array();
	
// clear the old relations	
//	$this->dbh->exec("DELETE FROM userpanel WHERE user_pkey = '" . $user_pkey . "'" ); 
	$this->helper->predDelTuple("userpanel","user_pkey",$user_pkey);
	
// add the new	
	foreach ($_POST['appl'] as $displayname) {
		$res = $this->dbh->query("SELECT pkey FROM panel where displayname = '" . $displayname . "'" )->fetch(PDO::FETCH_ASSOC);
		array_push($panel, $res['pkey']) ;		
	}
	
//  OK now we have an array of keys so we can build the relation
	$res=$this->dbh->prepare('INSERT INTO userpanel(user_pkey,panel_pkey) VALUES(?,?)');
	foreach ($panel as $panel_pkey) {	
		$res->execute(array($user_pkey,$panel_pkey));
	}
// turn commit ON
//	$this->helper->commitOn();	
	$this->message = "Updated Permissions " . $user_pkey . "!";
}

}
