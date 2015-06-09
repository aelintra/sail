<?php
// sarkcluster class
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
require "../srkPageClass";
require "../srkDbClass";
require "../srkHelperClass";
require "../formvalidator.php";

Class cluster {
	
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
	echo '<form id="sarkclusterForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Tenants';
	
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
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();

	echo '<div class="datadivmax">';

	echo '<table class="display" id="clustertable">' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('tenantname');
	$this->myPanel->aHeaderFor('tenantoperator');
	$this->myPanel->aHeaderFor('include',false); 
    $this->myPanel->aHeaderFor('localarea');    
    $this->myPanel->aHeaderFor('localdplan');        	
	$this->myPanel->aHeaderFor('ato');	
	$this->myPanel->aHeaderFor('chanmax');
	$this->myPanel->aHeaderFor('masterclose');
	$this->myPanel->aHeaderFor('oclo');
	$this->myPanel->aHeaderFor('del');
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("cluster");
	foreach ($rows as $row ) {
		$ret = $this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'database get CustomDevstate " . $row['pkey'] . "'"); 
		$masterclose = "AUTO";
		if (preg_match("/Value: INUSE/",$ret)) {
			$masterclose = "CLOSED";
		}

		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;
/*
		ToDo - fix operator endpoint ID - there is no routecode so we need 
		something to figure out what we have (extension or callgroup).
*/
		echo '<td >' . $row['operator'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['include'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['localarea'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['localdplan'] . '</td>' . PHP_EOL;				
		echo '<td >' . $row['abstimeout'] . '</td>' . PHP_EOL;		
		echo '<td >' . $row['chanmax'] . '</td>' . PHP_EOL;
		echo '<td >' . $masterclose . '</td>' . PHP_EOL;
		echo '<td >' . $row['oclo'] . '</td>' . PHP_EOL;
		if ($row['pkey'] == 'default') {
			echo '<td class="center">N/A</td>' . PHP_EOL;
		}
		else {
			$get = '?id=' . $row['pkey'];		
			$this->myPanel->ajaxdeleteClick($get);
		}
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';

}
private function showNew() {
		
	$this->myPanel->msg .= "Add New Tenant " ; 
	
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
	$this->myPanel->aLabelFor('tenantname');
	echo '<input type="text" name="pkey" id="pkey"  rel="1" />' . PHP_EOL;
	$this->myPanel->aLabelFor('include');
	echo '<input type="text" name="include" id="include"  rel="2" />' . PHP_EOL;
	echo '</div>';	
}

private function saveNew() {
// save the data away	
	$tuple = array();
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Tenant name");
    $this->validator->addValidation("localarea","num","Local Area Code must be numeric"); 
    $this->validator->addValidation("localdplan","regexp=/^[_0-9XNZxnz!#\s\*\.\-\[\]]+$/","Local Dialplan must be a valid Asterisk dialplan");
    $this->validator->addValidation("abstimeout","num","Absolute Timeout must be numeric");
    $this->validator->addValidation("chanmax","num","Channels must be numeric");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
				
		$tuple['pkey']		=  strip_tags($_POST['pkey']);
		$tuple['include'] 	=  strip_tags($_POST['include']);

		$res = $this->dbh->query("SELECT SYSOP FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
		$tuple['operator'] = $res['SYSOP'];
		
		$ret = $this->helper->createTuple("cluster",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Saved new Tenant " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['clusterinsert'] = $ret;	
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
