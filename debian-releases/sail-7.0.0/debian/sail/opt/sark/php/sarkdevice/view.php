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


Class sarkdevice {
	
	protected $message; 
	protected $head = "Templates";
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
	
	
	$this->myPanel->pagename = 'Devices';
	
	if (isset($_GET['pkey'])) {
		$this->showEdit($_GET['pkey']);
		return;
	}
			
	if (isset($_POST['new'])) { 
		$this->showNew();
		return;		
	}
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}
	
	if (isset($_POST['newblf'])) { 
		$this->saveNewBlf();
		$this->showEdit();
		return;
	}	

	if (isset($_POST['delblf'])) { 
		$this->deleteLastBlf();
		$this->showEdit();
		return;
	}
		
	if (isset($_POST['save'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (isset($_POST['update'])) { 
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

/* 
 * start page output
 */

	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkdeviceForm",false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup();

	echo '<form id="sarkdeviceForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('devicetable',' w3-small');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('device'); 	
	$this->myPanel->aHeaderFor('description');
//	$this->myPanel->aHeaderFor('technology');
	$this->myPanel->aHeaderFor('blfkeyname');
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/


	$rows = $this->helper->getTable("device", "select * from device where legacy is null");
	foreach ($rows as $row ) {
		if ($row['pkey'] == 'AnalogFXS') {
			continue;
		}
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;			
		echo '<td >' . $row['desc']  . '</td>' . PHP_EOL;		 
//		echo '<td >' . $row['technology']  . '</td>' . PHP_EOL;

		if ($row['technology'] != 'SIP' ) {
			echo '<td class="read_only">N/A</td>' . PHP_EOL;	
		}
		else {
			echo '<td >' . $row['blfkeyname']  . '</td>' . PHP_EOL;	
		}			 
 		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		if ( $row['technology'] == 'Analogue') {
			echo '<td class="center">N/A</td>' . PHP_EOL;	
		}
		else {
			$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		}
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
	$this->myPanel->msg .= "Add a Device "; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkdeviceForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
	$this->myPanel->subjectBar("New Template");

	echo '<form id="sarkdeviceForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';


	
	$res = $this->dbh->query("SELECT pkey from device WHERE (technology='SIP' OR technology='Descriptor') AND legacy is null ORDER BY pkey");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$devices = $res->fetchAll(); 
	array_unshift($devices, "New");	

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->displayInputFor('devicename','text');
//	$this->myPanel->aLabelFor('device');
//	echo '<input type="text" name="pkey" size="20" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;

	$this->myPanel->radioSlide('devtech','SIP',array('SIP', 'Descriptor','BLF Template'));
//	$this->myPanel->aLabelFor('technology');
//	$this->myPanel->popUp('technology', array('SIP', 'Descriptor','BLF Template'  ));	


	$this->myPanel->aLabelFor('copy');
	$this->myPanel->popUp('copy', $devices);
	$this->myPanel->aHelpBoxFor('copy');

	$this->myPanel->displayInputFor('description','text');
//	$this->myPanel->aLabelFor('description');
//	echo '<input type="text" name="desc" id="desc" size="30"  />' . PHP_EOL;

	echo '</div>';
	echo '</form>' . PHP_EOL; // close the form 
    $this->myPanel->responsiveClose();	
}

private function saveNew() {
// save the data away
	
	$tuple = array();
	
	$this->validator = new FormValidator();
	$this->validator->addValidation("devicename","req","Please fill in device name");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
		
		$tuple['pkey'] 			= strip_tags($_POST['devicename']);
		$tuple['desc'] 			= strip_tags($_POST['description']);
		$tuple['owner'] 		= 'cust';
		if ($_POST['copy'] != 'New') {
			$resdevice = $this->dbh->query("SELECT sipiaxfriend,provision,technology FROM device WHERE pkey = '" . $_POST['copy'] . "'")->fetch(PDO::FETCH_ASSOC);
			$tuple['sipiaxfriend'] 	= $resdevice['sipiaxfriend'];
			$tuple['provision']		= $resdevice['provision'];
			$tuple['technology']	= $resdevice['technology'];	
		}
		else {
			$tuple['technology'] 	= $_POST['devtech'];
		}	

		$ret = $this->helper->createTuple("device",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Saved new device " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['exteninsert'] = $ret;	
		}
//		}		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    unset ($this->validator);
}

private function saveNewBlf() {
// save the data away
	$pkey = $_POST['pkey'];
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $pkey . '" />' . PHP_EOL;
	echo '<input type="hidden" id="tabselect" name="tabselect" value="2" />' . PHP_EOL;	
	
	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $pkey . "'")->fetchColumn();
	$seq++;
	$res=$this->dbh->prepare('INSERT INTO ipphone_fkey(pkey,seq,type,label,value) VALUES(?,?,?,?,?)');
	$res->execute(array( $pkey,$seq,'Default','None','None'));
	
}

private function deleteLastBlf() {
// save the data away
	$pkey = $_POST['pkey'];
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $pkey . '" />' . PHP_EOL;
	echo '<input type="hidden" id="tabselect" name="tabselect" value="2" />' . PHP_EOL;	
	
	$seq = $this->dbh->query("select count(*) from ipphone_fkey where pkey='" . $pkey . "'")->fetchColumn();
	if ($seq) {
		$res=$this->dbh->prepare('DELETE FROM ipphone_fkey WHERE pkey=? AND seq=?');
		$res->execute(array( $pkey,$seq));
	}
	
}

private function showEdit() {
	if (isset($_POST['pkey'])) {
		$pkey = $_POST['pkey'];
	}
	else {
		$pkey = $_GET['pkey'];
	}
	$device = $this->dbh->query("SELECT * FROM device WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$buttonArray['cancel'] = true;
	$buttonArray['delete'] = true;

	$this->myPanel->actionBar($buttonArray,"sarkdeviceForm",false,true,true);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head . " " . $pkey,$this->message);

	$this->myPanel->responsiveTwoCol();

	echo '<form id="sarkdeviceForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
/*
 * 	TAB General
 */  
    echo '<div class="w3-margin-bottom">';	
	$this->myPanel->aLabelFor("General Settings");
	echo '</div>';

    $this->myPanel->internalEditBoxStart();
    $this->myPanel->displayInputFor('device','text',$device['pkey'],'pkey',null,'readonly');    
    $this->myPanel->displayInputFor('devtech','text', $device['technology'],null,null,'readonly');
	$this->myPanel->displayInputFor('description','text', $device['desc'],'desc');
	echo '</div>' . PHP_EOL;


	$this->myPanel->responsiveTwoColRight();
/*
 *   TAB Provisioning
 */
	echo '<div class="w3-margin-bottom">';	
	$this->myPanel->aLabelFor("Provisioning Rules");
	echo '</div>';
	echo '<div id="provisioning" >';
	$this->myPanel->displayFile(htmlspecialchars($device['provision']),"provision");
	echo '</div>' . PHP_EOL;

	
/*
 *   TAB Asterisk
 */
	if ($device['technology'] == 'SIP') {
		echo '<div class="w3-margin-bottom">';	
		$this->myPanel->aLabelFor("sipiaxfriend");
		echo '</div>';
		echo '<div id="asterisk" >';
		$this->myPanel->displayFile(htmlspecialchars($device['sipiaxfriend']),"sipiaxfriend");
		echo '</div>' . PHP_EOL;
	}
	

	echo '</div>';
 	echo '</form>' . PHP_EOL; // close the form 
    $this->myPanel->responsiveClose();

	
/*
 * 	TAB BLF/DSS Keys
 */ 

/* not used 
	if ($device['technology'] == 'SIP' ) {
		if ( preg_match(' /\.Fkey/ ', $device['blfkeyname'] )) {
			echo '<div id="blf">';
       
			echo '<table id="blftable">' ;
			echo '<thead>' . PHP_EOL;	
			echo '<tr>' . PHP_EOL;
	
			$this->myPanel->aHeaderFor('blfkey',false); 	
			$this->myPanel->aHeaderFor('blftype',false);
			$this->myPanel->aHeaderFor('blflabel',false);
			$this->myPanel->aHeaderFor('blfvalue',false);
	
			echo '</tr>' . PHP_EOL;
			echo '</thead>' . PHP_EOL;
			echo '<tbody>' . PHP_EOL;
		

			$sql = "select * from ipphone_FKEY where pkey='" . $pkey . "'";
			$rows = $this->helper->getTable("ipphone_fkey",$sql);
			foreach ($rows as $row ) {
				echo '<tr id="' . $row['seq'] . '~' . $row['pkey'] . '">'. PHP_EOL; 
				echo '<td>' . $row['seq'] . '</td>' . PHP_EOL;		
				echo '<td >' . $row['type'] . '</td>' . PHP_EOL;
				echo '<td >' . $row['label'] . '</td>' . PHP_EOL;
				echo '<td >' . $row['value'] . '</td>' . PHP_EOL;	
				echo '</tr>'. PHP_EOL;
			}

			echo '</tbody>' . PHP_EOL;
			echo '</table>' . PHP_EOL;	
			echo '</div>' . PHP_EOL;
		}
	}	
	echo '</div>' . PHP_EOL;		
	echo '</div>' . PHP_EOL;
/* 	NOT used on this panel	
 *	$this->myPanel->navRowDisplay("device", $pkey);
 */	
 		
}

private function saveEdit() {
// save the data away
//print_r ($_POST) . "\n";

	$tuple = array();
		
	$this->validator = new FormValidator();

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
    	$custom = array (
						'devtech' => True
		);

/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple,$custom);

/*
 * update the SQL database
 */
 
// remove any escaped quotes 
		$tuple['technology'] 	= $_POST['devtech'];			
		$tuple['provision'] = preg_replace ( "/\\\/", '', $tuple['provision']);
		$tuple['provision'] = preg_replace ( "/&type/", '&amp;type', $tuple['provision']);
		if (isset($tuple['sipiaxfriend'])) {
			$tuple['sipiaxfriend'] = preg_replace ( "/\\\/", '', $tuple['sipiaxfriend']);
		}

		$ret = $this->helper->setTuple("device",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Updated device ";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['extensave'] = $ret;	
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
