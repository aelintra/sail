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


Class sarkuser {
	
	protected $message; 
	protected $head = "Users";
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
	
	
	$this->myPanel->pagename = 'Users';

	if (isset($_GET['reset'])) {
		if (isset($_GET['pkey'])) {
			$pkey = strip_tags($_GET['pkey']);
			$this->helper->resetPassword ($pkey);
			$this->message = "Password has been reset for user $pkey";
		}
		else {
			$this->invalidForm = True;
			$this->message = "System Error!";	
			$this->error_hash['pwdreset'] = "Password reset failed, no UID present.";
		}
	}

	if (isset($_POST['new']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;		
	}
	if (isset($_POST['save']) || isset($_POST['endsave'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->setPerms();
		$this->showPerms();	
		return;	
	}
	if (isset($_GET['permedit'])) { 
		$this->showPerms();	
		return;
	}
	
	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
		$this->helper->sysCommit();
		$this->message = "Updates have been Committed";	
	}

	if (isset($_GET['msg'])) { 
		$this->message = strip_tags($_GET['msg']);
	}
		
	$this->showMain();
	
	$this->dbh = NULL;
	return;		
}


private function showMain() {

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkuserForm",false,false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

	echo '<form id="sarkuserForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('usertable',' w3-small');
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;

	
	$this->myPanel->aHeaderFor('user');
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('realname',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('exten');		 		
//	$this->myPanel->aHeaderFor('useremail',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('resetpwd',false,'w3-hide-small');
//	$this->myPanel->aHeaderFor('userspan');
	$this->myPanel->aHeaderFor('edit');
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
			echo '<td class="w3-hide-medium w3-hide-small">' . $row['cluster'] . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-small">' . $row['realname'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['extension'] . '</td>' . PHP_EOL;
//			echo '<td class="w3-hide-small">' . $row['email'] . '</td>' . PHP_EOL;				
			echo '<td class="w3-hide-small"><a href="/php/sarkuser/main.php?reset=yes&pkey=' . $row['pkey'] . '" ><img src="/sark-common/icons/undo.png" border=0 title = "Click to Reset" )"></a></td>' . PHP_EOL;			
//			echo '<td >' . $row['selection'] . '</td>' . PHP_EOL;
//			echo '<td >' . $row['readonly'] . '</td>' . PHP_EOL;
			$get = '?permedit=yes&amp;pkey=';
			$get .= $row['pkey'];	
			$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		
			$get = '?id=' . $row['pkey'];		
			$this->myPanel->ajaxdeleteClick($get);			echo '</tr>'. PHP_EOL;
		}
	}
	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();	
}

private function showNew() {
	$this->myPanel->msg = "Add New User "; 

		if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkuserForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
	$this->myPanel->subjectBar("New User");

	echo '<form id="sarkuserForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->internalEditBoxStart();

	$this->myPanel->displayInputFor('extension','text');

	$this->myPanel->displayInputFor('realname','text',null,'realname');
//	$this->myPanel->aLabelFor('user');
//	echo '<input type="text" name="pkey" id="pkey"   />' . PHP_EOL;

//	$this->myPanel->displayInputFor('password','password');
//	$this->myPanel->aLabelFor('password');
//	echo '<input type="password" name="password" id="password"   />' . PHP_EOL;

	

//	$this->myPanel->displayInputFor('email','email');
//	$this->myPanel->aLabelFor('email');
//	echo '<input type="text" email" name="email" id="email"   />' . PHP_EOL;

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

//	$this->myPanel->aLabelFor('scope');
//	$this->myPanel->popUp('selection', array("enduser", "poweruser", "tenant","all"));	

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
	$tuple = array();
	
	$this->validator = new FormValidator();
	$this->validator->addValidation("realname","req","Please fill in User real name");
	$this->validator->addValidation("extension","req","Please fill in extension number");
    $this->validator->addValidation("email","email","The input for Email should be a valid email value i.e. <i> someuser@somedomain </i>");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
  //  	$pkey = mt_rand(100000,999999);
		$tuple['pkey']			=  strip_tags($_POST['extension']);
		$tuple['realname']  	=  strip_tags($_POST['realname']);
		$tuple['extension']  	=  strip_tags($_POST['extension']);
		$tuple['cluster'] 		=  strip_tags($_POST['cluster']);
		$tuple['email'] 		=  strip_tags($_POST['email']);
		$ret = $this->helper->createTuple("user",$tuple);		
		if ($ret == 'OK') {
			$this->helper->resetPassword($tuple['pkey']);
			$this->message = "Saved new user " . $tuple['pkey'] . "!";
			$this->myPanel->prg($this->message);
		}
		else {
			$this->invalidForm = True;
			$this->message = "Create Errors!";	
			$this->error_hash['userinsert'] = $ret;	
		}
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    
}


private function showPerms() {
	
			
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
		
	
	$res = $this->dbh->query("SELECT CLUSTER FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$cluster = $res['CLUSTER'];
	
	$user_pkey = $_REQUEST['pkey'];
			
	
	$buttonArray['cancel'] = true;

	$this->myPanel->actionBar($buttonArray,"sarkuserForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
	$this->myPanel->subjectBar('User ' . $user_pkey . ' Permissions');

	echo '<form id="sarkuserForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->internalEditBoxStart();
	
	$user = $this->dbh->query("select pkey,email,realname,selection from user where pkey='" . $user_pkey . "'")->fetch(PDO::FETCH_ASSOC);

		
	$res = $this->dbh->prepare("select pkey,displayname,weight,ability from panel order by displayname");
	$res->execute();
	$panel = $res->fetchAll();
	
//	echo '<br/><span style="color: rgb(0, 0, 0); font-weight:bold;font-size:small; ">User -> ' . $user_pkey . '</span><br/>';
	echo '<input type="hidden" name="pkey" value="' . $user_pkey  . '" />' . PHP_EOL; 


	$this->myPanel->internalEditBoxStart();
	$this->myPanel->displayInputFor('realname','text',$user['realname']);
//	$this->myPanel->displayInputFor('email','text',$user['email']);	
	echo '</div>';
	
	foreach ($panel as $j => $panelrow) {
		if (($panelrow['weight'] >= 50)) {
			continue;
		}
//		
//		if ($panelrow['weight'] > $userweight) {
//			continue;
//		}
//
		if ($panelrow['displayname'] == 'Directory' && 	$cluster == "ON") {
			continue;
		}		
		$res = $this->dbh->query("SELECT user_pkey,perms FROM userpanel where user_pkey = '" . 
									$user_pkey . 
									"' AND panel_pkey ='" . 
									$panelrow['pkey'] . 
									"'" )->fetch(PDO::FETCH_ASSOC);


		$this->myPanel->internalEditBoxStart();
/*		
		if (isset($res['User_pkey'])) {
			$this->myPanel->displayBooleanFor($panelrow['displayname'],true);
//			echo '<input type="checkbox"  checked="yes" name="appl[]" value="' . $panelrow['displayname'] . '" />' . $panelrow['displayname'] . "<br/>\n"; 
		}
		else {
			$this->myPanel->displayBooleanFor($panelrow['displayname']);
//			echo '<input type="checkbox"  name="appl[]" value="' . $panelrow['displayname'] . '" />' . $panelrow['displayname'] . "<br/>\n"; 
		}
*/
		if (!isset($res['perms'])) {
			$res['perms'] = 'off';
		}
		$perms = array("off");
		switch ($panelrow['ability']) {
			case "view":
				array_push($perms,"view");
				break;
			case "update":
				array_push($perms,"view","update");
				break;
			case "create":
				array_push($perms,"view","update","create");
				break; 
			default:
		}
		if ($panelrow['displayname'] == 'Password') {
			$perms = array("off","update");
		}

		$this->myPanel->radioSlide($panelrow['displayname'],$res['perms'],$perms);										
		echo '</div>';
	} 
	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL;
	echo '</form>' . PHP_EOL; // close the form
	echo '</div>';  
    $this->myPanel->responsiveClose();	
}


private function setPerms() {

	$user_pkey = $_POST['pkey'];
	$tuple = array();
	
	$this->validator = new FormValidator();
	$this->validator->addValidation("realname","req","Please fill in User real name");


    //Now, validate the form
    if ($this->validator->ValidateForm()) {
		$tuple['pkey']			=  strip_tags($_POST['pkey']);
		$tuple['realname']  	=  strip_tags($_POST['realname']);
//		$tuple['email'] 		=  strip_tags($_POST['email']);
		$ret = $this->helper->setTuple("user",$tuple);		
		if ($ret != 'OK') {
			$this->invalidForm = True;
			$this->message = "Update Errors!";	
			$this->error_hash['userupdate'] = $ret;	
			return;
		}
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";
		return;		
    }
	
// clear the old relations	
//	$this->dbh->exec("DELETE FROM userpanel WHERE user_pkey = '" . $user_pkey . "'" ); 
	$this->helper->predDelTuple("userpanel","user_pkey",$user_pkey);


	foreach ($_POST as $dname => $perm) {
		if ($dname=='pkey' || $dname=='update' || $dname=='endupdate') {
			continue;
		}
		if ($perm == 'off') {
			continue;
		}
/*
	$dname may have unwanted underscores in it from presentation.  we will remove them here
 */
		$dname = preg_replace("/_/"," ",$dname);
		$panel = $this->dbh->query("SELECT pkey FROM panel where displayname = '" . $dname . "'" )->fetch(PDO::FETCH_ASSOC);
		$res=$this->dbh->prepare('INSERT INTO userpanel(user_pkey,panel_pkey,perms) VALUES(?,?,?)');
		$res->execute(array($user_pkey,$panel['pkey'],$perm));
	}
/*	
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
*/
	$this->message = "Updated User " . $user_pkey . "!";
}

}
