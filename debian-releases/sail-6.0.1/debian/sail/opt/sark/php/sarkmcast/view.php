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


Class sarkmcast {
	
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
		
	echo '<form id="sarkmcastForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Multicast Page groups';

	if (isset($_POST['new_x']) || isset ($_GET['new'])  ) { 
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
	
	echo '<div class="datadivnarrow">';

	echo '<table class="display" id="mcasttable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('mcastpkey'); 
	$this->myPanel->aHeaderFor('mcastip');
	$this->myPanel->aHeaderFor('mcastport');
	$this->myPanel->aHeaderFor('mcastlport');
	$this->myPanel->aHeaderFor('description');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("mcast");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;				 
		echo '<td >' . $row['mcastip']  . '</td>' . PHP_EOL;		
		echo '<td >' . $row['mcastport']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['mcastlport']  . '</td>' . PHP_EOL;		
		echo '<td >' . $row['mcastdesc']  . '</td>' . PHP_EOL;
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';	
	
}

private function showNew() {
	$this->myPanel->msg .= "Add a Multicast Page Group "; 
	
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
	$this->myPanel->aLabelFor('mcastpkey');
	echo '<input type="text" name="pkey" size="6" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;	
//	$this->myPanel->aLabelFor('mcastip');
//	echo '<input type="text" name="mcastip" id="mcastip" size="20"  />' . PHP_EOL;		
	$this->myPanel->aLabelFor('mcastport');
	echo '<input type="text" name="mcastport" id="mcastport" size="5"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('mcastlport');
	echo '<input type="text" name="mcastlport" id="mcastlport" size="5"  />' . PHP_EOL;	
	$this->myPanel->aLabelFor('description');
	echo '<input type="text" name="mcastdesc" id="mcastdesc" size="30"  />' . PHP_EOL;	
	echo '</div>';		
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in mcast group (extension)");
    $this->validator->addValidation("pkey","num","Multicast Group must be numeric");    
/*
	$this->validator->addValidation("mcastip",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"Multicast IP address is invalid");
*/
    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->createTuple("mcast",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved new Multicast group " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['mcastinsert'] = $ret;	
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
