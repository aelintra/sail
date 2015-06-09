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



Class cos {
	
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
	echo '<form id="sarkcosForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Class of Service';
	
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
	
	echo '<div class="datadivwide">';

	echo '<table class="display" id="costable">' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('cosname'); 	
	$this->myPanel->aHeaderFor('cosdialplan');
	$this->myPanel->aHeaderFor('cosopen');
	$this->myPanel->aHeaderFor('orideopen');
	$this->myPanel->aHeaderFor('cosclosed');
	$this->myPanel->aHeaderFor('orideclosed');
	$this->myPanel->aHeaderFor('active');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("cos");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;			
		echo '<td >' . $row['dialplan']  . '</td>' . PHP_EOL;		 
		echo '<td >' . $row['defaultopen']  . '</td>' . PHP_EOL;		
		echo '<td >' . $row['orideopen']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['defaultclosed']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['orideclosed']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['active']  . '</td>' . PHP_EOL;
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;	
	echo '</div>' . PHP_EOL;	
}

private function showNew() {
	$this->myPanel->msg .= "Add a Class of Service "; 
	
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
	$this->myPanel->aLabelFor('cosname');
	echo '<input type="text" name="pkey" size="20" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('active');
	$this->myPanel->popUp('active', array('YES', 'NO'));	
	
	$this->myPanel->aLabelFor('cosdialplan');
	echo '<input type="text" name="dialplan" id="desc" size="30"  />' . PHP_EOL;
		
	$this->myPanel->aLabelFor('defaultopen');
	$this->myPanel->popUp('defaultopen', array('NO','YES'));	
	$this->myPanel->aLabelFor('orideopen');
	$this->myPanel->popUp('orideopen', array('NO','YES'));	
	$this->myPanel->aLabelFor('defaultclosed');
	$this->myPanel->popUp('defaultclosed', array('NO','YES'));	
	$this->myPanel->aLabelFor('orideclosed');
	$this->myPanel->popUp('orideclosed', array('NO','YES'));
	echo '</div>';		
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in COS name");
    $this->validator->addValidation("pkey","alnum","COS name must be alpha numeric (no spaces, no special characters)");    
    $this->validator->addValidation("dialplan","regexp=/^[\+0-9XNZxnz_!#\s\*\.\-]+$/","Dialplan must be a valid Asterisk dialplan");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->createTuple("cos",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved new COS " . $tuple['pkey'] . "!";
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
