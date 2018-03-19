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


Class sarkconference {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $rlse;

public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	
	$release = `/usr/sbin/asterisk -rx 'core show version'`;
	$this->rlse = '1.8';
	if (preg_match(' /Asterisk\s*(\d\d).*$/ ', $release,$matches)) {
		$this->rlse = $matches[1];
	}	
		
	echo '<form id="sarkconferenceForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Conference Rooms';

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
	
	$confarray = `/usr/sbin/asterisk -rx 'meetme list concise' | grep $search`; 

/* 
 * start page output
 */
  
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	echo '<div class="datadivnarrow">';

	echo '<table class="display" id="conferencetable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('confpkey'); 
	$this->myPanel->aHeaderFor('conftype');
	$this->myPanel->aHeaderFor('confpin');
	$this->myPanel->aHeaderFor('confadminpin');		
	$this->myPanel->aHeaderFor('description');
	$this->myPanel->aHeaderFor('status');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("meetme");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;				 
		echo '<td >' . $row['type']  . '</td>' . PHP_EOL;		
		echo '<td >' . $row['pin']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['adminpin']  . '</td>' . PHP_EOL;		
		echo '<td >' . $row['description']  . '</td>' . PHP_EOL;
		$search = $row['pkey'];
		$status = 'free';
		if ($this->rlse < 11) {
			$statusrow = `/usr/sbin/asterisk -rx 'meetme list concise' | grep $search`;
			if ($statusrow) {
				$roomarray = (explode('!', $statusrow));
				$status = $roomarray[1] . " users";
			}
		}
		else {
			$statusrow = `/usr/sbin/asterisk -rx 'confbridge list' | grep $search`;
			if ($statusrow) {
				$numusers = preg_match(' /^\d{3,4}\s*(\d{1,2})/ ',$statusrow,$matches);
				$status = $matches[1] . " users";
			}			
		}

		echo '<td >' . $status  . '</td>' . PHP_EOL;
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
	$this->myPanel->msg .= "Add a conference room "; 
	
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
	$this->myPanel->aLabelFor('confpkey');
	echo '<input type="text" name="pkey" size="6" id="pkey" size="4" placeholder="Number" />' . PHP_EOL;	
	$this->myPanel->aLabelFor('description');
	echo '<input type="text" name="description" id="description" size="30"  />' . PHP_EOL;	
	echo '</div>';		
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Room Number");
    $this->validator->addValidation("pkey","num","Multicast extension name must be numeric");    

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->createTuple("meetme",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved new Conference Room " . $tuple['pkey'] . "!";
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
