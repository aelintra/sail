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
	protected $head = "Conferences";
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
		
	
	
	$this->myPanel->pagename = 'Conference Rooms';

	if ( isset($_POST['new']) || isset($_GET['new'] )) { 
		$this->showNew();
		return;
	}
	if (isset ($_REQUEST['edit'])) {
		$this->showEdit();
			return;
	}

	if ( isset($_POST['save']) || isset($_POST['endsave'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}
	}

	if ( isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}
	}	

	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
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
  
	$buttonArray=array();	
	$buttonArray['new'] = true;

	$this->myPanel->actionBar($buttonArray,"sarkconferenceForm",false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$bigTable=true;
	$this->myPanel->responsiveSetup(2);
//	$this->myPanel->subjectBar("Extensions");

	echo '<form id="sarkconferenceForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	$this->myPanel->beginResponsiveTable('conferencetable',' w3-small');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('confpkey'); 
	$this->myPanel->aHeaderFor('conftype',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('confpin',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('confadminpin',false,'w3-hide-small');		
	$this->myPanel->aHeaderFor('description',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('status');
	$this->myPanel->aHeaderFor('ed');	
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("meetme");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;				 
		echo '<td class="w3-hide-small">' . $row['type']  . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small">' . $row['pin']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $row['adminpin']  . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small">' . $row['description']  . '</td>' . PHP_EOL;
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
		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();
	
}

private function showNew() {
$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkconferenceForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
	$this->myPanel->subjectBar("New Conference Room");

	echo '<form id="sarkconferenceForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->internalEditBoxStart();

	$this->myPanel->displayInputFor('confpkey','number',null,'pkey');
//	$this->myPanel->aLabelFor('confpkey');
//	echo '<input type="text" name="pkey" size="6" id="pkey" size="4" placeholder="Number" />' . PHP_EOL;

	$this->myPanel->displayInputFor('description','text');
//	$this->myPanel->aLabelFor('description');
//	echo '<input type="text" name="description" id="description" size="30"  />' . PHP_EOL;

	echo '</div>';	

	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL;
	echo '</form>' . PHP_EOL; // close the form
	echo '</div>';  
    $this->myPanel->responsiveClose();
		
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Room Number");
    $this->validator->addValidation("pkey","num","Conference room must be numeric");    

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
			$this->message = "Validation Errors!</B>";	
			$this->error_hash['confinsert'] = $ret;	
		}
				
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function showEdit() {

	$pkey = $_REQUEST['pkey']; 

	$tuple = $this->dbh->query("SELECT * FROM meetme WHERE pkey='" . $pkey ."'")->fetch(PDO::FETCH_ASSOC);;
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkconferenceForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Update Conference " . $pkey);

	echo '<form id="sarkconferenceForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->internalEditBoxStart();
	
	
	$this->myPanel->radioSlide('type',$tuple['type'],array('simple','hosted'));
	$this->myPanel->displayInputFor('description','text',$tuple['description']);
	$this->myPanel->displayInputFor('pin','number',$tuple['pin']);
	$this->myPanel->displayInputFor('adminpin','number',$tuple['adminpin']);

	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
		
	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	
	$this->myPanel->responsiveClose();
}
private function saveEdit() {

	$tuple = array();
	$custom = array (
	);

	$this->helper->buildTupleArray($_POST,$tuple);	

	$tuple['pkey'] = $_POST['pkey'];

	$ret = $this->helper->setTuple("meetme",$tuple);
	if ($ret == 'OK') {
		$this->message = "Updated Conference ";
	}
	else {
		$this->invalidForm = True;
		$this->message = "Validation Errors!";	
		$this->error_hash['extenupdate'] = $ret;	
	}	
}

}
