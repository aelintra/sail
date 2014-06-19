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


Class callback {
	
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
	echo '<form id="sarkcallbackForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Callback';
	
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
 * start page output
 */
  
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	$tabname = 'callbacktable';
	if ( $_SERVER['REMOTE_USER'] == 'admin' ) {
		$tabname .= 'admin';
	}
	
	echo '<div class="datadivnarrow">';
	
	echo '<table class="display" id="' . $tabname . '"  >' ;	


	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('authnum');
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('description'); 	
	$this->myPanel->aHeaderFor('prefix');	
	$this->myPanel->aHeaderFor('backchan');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("callback");
	foreach ($rows as $row ) { 
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;	
		echo '<td >' . $row['cluster']  . '</td>' . PHP_EOL;		
		echo '<td >' . $row['desc']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['prefix']  . '</td>' . PHP_EOL;				 
		echo '<td >' . $row['channel']  . '</td>' . PHP_EOL;	
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
		
}

private function showNew() {
	$this->myPanel->msg .= "Add a Callback "; 
	
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
	
	$trunklist = array();
	array_push($trunklist, "None");
	$sql = "select li.pkey,ca.technology from lineio li inner join carrier ca on li.carrier=ca.pkey " .
			"where ca.technology='IAX2' OR ca.technology='SIP' OR ca.technology='DAHDI' ";			
	$rows = $this->helper->getTable("lineio", $sql);
  
	foreach ($rows as $row) {
		array_push($trunklist, $row['pkey']);
	} 	
	
	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('authnum');
	echo '<input type="text" name="pkey" size="20" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('cluster');
	$this->myPanel->displayCluster();		
	$this->myPanel->aLabelFor('description');
	echo '<input type="text" name="desc" id="desc" size="30"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('prefix');
	echo '<input type="text" name="prefix" id="prefix" size="3"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('channel');
	$this->myPanel->popUp('channel', $trunklist);
	echo '</div>';	
			
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in authorized callback number");
    $this->validator->addValidation("pkey","num","callback number  must be alpha numeric (no spaces, no special characters)");    
	$this->validator->addValidation("description","alnum_s","Description must be alphanumeric (no special characters)"); 

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->createTuple("callback",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved new callback " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['cosinsert'] = $ret;	
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
