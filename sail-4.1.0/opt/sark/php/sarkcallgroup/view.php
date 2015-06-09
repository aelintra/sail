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

Class callgroup {
	
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
	echo '<form id="sarkcallgroupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Call Groups';		
	
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
	
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();

	echo '<div class="datadivwide">';

	echo '<table class="display" id="callgrouptable">' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('callgroup'); 	
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('description');
	$this->myPanel->aHeaderFor('grouptype');
	$this->myPanel->aHeaderFor('alphatag');
	$this->myPanel->aHeaderFor('groupstring');
	$this->myPanel->aHeaderFor('outcome');
	$this->myPanel->aHeaderFor('devicerec');
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;

/*** table rows ****/

	$rows = $this->helper->getTable("speed");
	foreach ($rows as $row ) {
		if ( $row['pkey'] != 'RINGALL') {
			echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
			echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;		
			echo '<td >' . $row['cluster'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['longdesc'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['grouptype'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['calleridname'] . '</td>' . PHP_EOL;
			echo '<td >' . $row['out'] . '</td>' . PHP_EOL;
			
			$this->helper->pkey = $row['outcome'];
			$displayout = $this->helper->displayRouteClass($row['outcomerouteclass']);
			echo '<td >' . $displayout . '</td>' . PHP_EOL;
			echo '<td >' . $row['devicerec'] . '</td>' . PHP_EOL;	
			
			$get = '?edit=yes&amp;pkey=';
			$get .= $row['pkey'];	
			$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		
			$get = '?id=' . $row['pkey'];		
			$this->myPanel->ajaxdeleteClick($get);			echo '</td>' . PHP_EOL;
			echo '</tr>'. PHP_EOL;
		}
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';		
}
private function showNew() {
	$this->myPanel->msg .= "Add New Call Group "; 
	
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

	$res = $this->dbh->query("SELECT INTRINGDELAY FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$intringdelay = $res['INTRINGDELAY'];
	
	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('callgroup');
	echo '<input type="text" name="pkey" id="pkey" size="4"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('grouptype');
	$this->myPanel->popUp('grouptype', array('Ring','Hunt','Page','Alias'));
	$this->myPanel->aLabelFor('cluster');
	$this->myPanel->displayCluster();
	$this->myPanel->aLabelFor('devicerec');
	$this->myPanel->popUp('devicerec', array('default','None','OTR','OTRR','Inbound'));
	$this->myPanel->aLabelFor('ringdelay');
	echo '<input type="text" name="ringdelay" id="ringdelay" size="2" value="' . $intringdelay . '"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('alphatag');
	echo '<input type="text" name="calleridname" id="calleridname"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('groupstring');
	echo '<input type="text" name="out" id="out" size="64"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('alertinfo');
	echo '<input type="text" name="speedalert" id="speedalert" size="64"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('description');
	echo '<input type="text" name="longdesc" id="longdesc" size="64" />' . PHP_EOL;
//	$this->myPanel->aLabelFor('obeydnd');
//	$this->myPanel->popUp('obeydnd', array('NO','YES'));	
	$this->myPanel->aLabelFor('outcome');
	$this->myPanel->sysSelect('outcome') . PHP_EOL;
	echo '</div>';
		
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Call Group name");
    $this->validator->addValidation("pkey","alnum","Call Group name must be alphanumeric(no spaces)");    
//	$this->validator->addValidation("ringdelay","num","Ringtime must be numeric"); 
    $this->validator->addValidation("longdesc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("out","regexp=/^[@A-Za-z0-9-_\/\s]{2,1024}$/","Target must be number or number/channel strings separated by whitespace");     

    //Now, validate the form
    if ($this->validator->ValidateForm()) {

/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
/*
 * calculate the route class if outcome is set
 */   
		if (isset($_POST['outcome']) ) {	  
			$tuple['outcomerouteclass'] = $this->helper->setRouteClass($_POST['outcome']);
		} 					
		if ($this->helper->loopcheck($tuple['pkey'] , $tuple['out'])) {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash[routename] = "Loop detected in target list!";
		}
		else {		 		   
			$ret = $this->helper->createTuple("speed",$tuple);
			if ($ret == 'OK') {
//				$this->helper->commitOn();	
				$this->message = "Saved new call group " . $tuple['pkey'] . "!";
			}
			else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['speedinsert'] = $ret;	
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

private function showEdit($key=False) {
	
	if ($key != False) {
		$pkey=$key;
	}
	else {
		$pkey = $_GET['pkey']; 
	}
	
	$this->myPanel->msg .= "Edit Call Group "; 
	
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

	$res = $this->dbh->query("SELECT INTRINGDELAY FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$intringdelay = $res['INTRINGDELAY'];	
	$res = $this->dbh->query("SELECT * FROM speed where pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
	echo '<div class="editinsert">';	
	$this->myPanel->aLabelFor('callgroup');
	echo '<input type="text" name="pkey" size="4" style = "background-color: lightgrey" readonly="readonly" id="pkey" value="' . $res['pkey'] . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('grouptype');
	$this->myPanel->selected = $res['grouptype'];
	$this->myPanel->popUp('grouptype', array('Ring','Hunt','Page','Alias'));
	$this->myPanel->aLabelFor('cluster');
	$this->myPanel->selected = $res['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aLabelFor('devicerec');
	$this->myPanel->selected = $res['devicerec'];
	$this->myPanel->popUp('devicerec', array('default','None','OTR','OTRR','Inbound'));
	$this->myPanel->aLabelFor('ringdelay');
	echo '<input type="text" name="ringdelay" id="ringdelay" size="2" value="' . $res['ringdelay'] . '"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('alphatag');
	echo '<input type="text" name="calleridname" id="calleridname"  value="' . $res['calleridname'] . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('groupstring');
	echo '<input type="text" name="out" id="out"   value="' . $res['out'] . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('alertinfo');
	echo '<input type="text" name="speedalert" id="speedalert"  value="' . $res['speedalert'] . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('description');
	echo '<input type="text" name="longdesc" id="longdesc"  value="' . $res['longdesc'] . '"  />' . PHP_EOL;
//	$this->myPanel->aLabelFor('obeydnd');
//	$this->myPanel->selected = $res['obeydnd'];
//	$this->myPanel->popUp('obeydnd', array('NO','YES'));		
	$this->myPanel->aLabelFor('outcome');
	$this->myPanel->selected = $res['outcome'];
	$this->myPanel->sysSelect('outcome') . PHP_EOL;	
	echo '</div>';		
}

private function saveEdit() {
// save the data away
	$tuple = array();

	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Call Group name");
    $this->validator->addValidation("pkey","alnum","Call Group name must be alphanumeric(no spaces)");    
//	$this->validator->addValidation("ringdelay","num","Ringtime must be numeric"); 
    $this->validator->addValidation("longdesc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("out","regexp=/^[@A-Za-z0-9-_\/\s]{2,1024}$/","Target must be number or number/channel strings separated by whitespace");     

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * calculate the route class if outcome has changed
 */   
		if (isset($_POST['outcome']) ) {	  
			$tuple['outcomerouteclass'] = $this->helper->setRouteClass($_POST['outcome']);
		} 				
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$this->helper->buildTupleArray($_POST,$tuple);

		if (array_key_exists('outcome',$tuple)) {
			$tuple['outcomerouteclass'] = $this->helper->setRouteClass($tuple['outcome']);
		}
/*
 * loopcheck
 */ 		
		if ($this->helper->loopcheck($tuple['pkey'], $tuple['out'])) {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash[routename] = "Loop detected in target list!";
		}
		else {
/*
 * call the setter
 */ 
			$ret = $this->helper->setTuple("speed",$tuple);
/*
 * flag errors
 */ 	
			if ($ret == 'OK') {
//				$this->helper->commitOn();	
				$this->message = "Updated callgroup " . $tuple['pkey'] . "!";
			}
			else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash[speed] = $ret;	
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

}
