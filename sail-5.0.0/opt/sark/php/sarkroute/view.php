<?php
//
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


Class sarkroute {
	
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

	echo '<form id="sarkrouteForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Routes';
			
	if ( isset($_POST['new_x']) || isset ($_GET['new'])  ){ 
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
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
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

	echo '<div class="titlebar">' . PHP_EOL; 
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();
	echo '</div>';	
	
//	echo '<hr class="hr" />';
	$tabname = 'routetable';
	
	echo '<div class="datadivwide">';
	
	echo '<table class="display" id="' . $tabname . '"  >' ;

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('route'); 
	$this->myPanel->aHeaderFor('dialplan');	
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('routedesc');
	$this->myPanel->aHeaderFor('strategy');	
	$this->myPanel->aHeaderFor('path1');
	$this->myPanel->aHeaderFor('path2');
//	$this->myPanel->aHeaderFor('path3');
//	$this->myPanel->aHeaderFor('path4');			
	$this->myPanel->aHeaderFor('auth');
	$this->myPanel->aHeaderFor('Act');	
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("route");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td >' . $row['pkey'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['dialplan'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['desc'] . '</td>' . PHP_EOL;		
		echo '<td >' . $row['strategy'] . '</td>' . PHP_EOL;		
		echo '<td >' . $row['path1'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['path2'] . '</td>' . PHP_EOL;
//		echo '<td >' . $row['path3'] . '</td>' . PHP_EOL;
//		echo '<td >' . $row['path4'] . '</td>' . PHP_EOL;		
		echo '<td class="icons">' . $row['auth'] . '</td>' . PHP_EOL;
		echo '<td class="icons">' . $row['active'] . '</td>' . PHP_EOL;		
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);	
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '<input type="hidden" name="pkey" id="pkey" value="' . $row['pkey'] . '"  />' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>' . PHP_EOL;

}

private function showNew() {
	$this->myPanel->msg .= "Add New Route "; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  
	echo '<div class="titlebar">' . PHP_EOL;
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
	echo '</div>';	
	$trunklist = array();
	array_push($trunklist, "None");
/*
	$sql = "select li.pkey,ca.technology from lineio li inner join carrier ca on li.carrier=ca.pkey " .
			"where ca.technology='IAX2' OR ca.technology='SIP' OR ca.technology='DAHDI' OR ca.technology='Custom'";	
*/

	$sql = "select li.pkey,ca.technology from lineio li inner join carrier ca on li.carrier=ca.pkey ";				
					
	$rows = $this->helper->getTable("lineio", $sql);
  
	foreach ($rows as $row) {
		if ($row['technology'] ='IAX2' || $row['technology'] ='SIP' || $row['technology'] ='DAHDI' || $row['technology'] ='Custom') {
			array_push($trunklist, $row['pkey']);
		}
	} 

	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('route');
	echo '<input type="text" name="pkey" id="pkey" size="20" placeholder="Route Name"  />' . PHP_EOL;
	
	$this->myPanel->aLabelFor('active');
	$this->myPanel->popUp('active', array('YES','NO'));		
		
	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->selected = 'default';	
	$this->myPanel->displayCluster();
	
	$this->myPanel->aLabelFor('auth');
	$this->myPanel->popUp('auth', array('NO','YES'));	
	
	$this->myPanel->aLabelFor('strategy');
	$this->myPanel->popUp('strategy', array('hunt','balance'));			
		
	$this->myPanel->aLabelFor('routedesc');
	echo '<input type="text" name="desc" size="50" id="desc"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('dialplan');
	echo '<input type="text" name="dialplan" size="50" id="dialplan"   />' . PHP_EOL;
	
	$this->myPanel->aLabelFor('path1');
	$this->myPanel->popUp('path1', $trunklist);	
	$this->myPanel->aLabelFor('path2');
	$this->myPanel->popUp('path2', $trunklist);		
	$this->myPanel->aLabelFor('path3');
	$this->myPanel->popUp('path3', $trunklist);	
	$this->myPanel->aLabelFor('path4');
	$this->myPanel->popUp('path4', $trunklist);	
	echo '</div>';	

				
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Route name");
    $this->validator->addValidation("pkey","regexp=/^[_0-9 A-Za-z-_]+$/","Route name must be alpha numeric (no spaces, no special characters)");    
    $this->validator->addValidation("desc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("dialplan","regexp=/^[\+0-9XNZxnz_!#\s\*\.\-\/\[\]]+$/","Dialplan must be a valid Asterisk dialplan");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {

/*
 * 	call the tuple builder to create a table row array 
 */  
//		$tuple['pkey'] 	 = '_route' . rand(1000, 9999);
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->createTuple("route",$tuple);
		if ($ret == 'OK') {
//			$this->message = "Saved new route " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['speedinsert'] = $ret;	
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
	
	$this->myPanel->msg .= "Edit Route " . $pkey; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 
	 
	echo '<div class="titlebar">' . PHP_EOL;
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
	echo '</div>';	
		
	$trunklist = array();
	array_push($trunklist, "None");
	$sql = "select li.pkey,ca.technology from lineio li inner join carrier ca on li.carrier=ca.pkey " .
			"where ca.technology='IAX2' OR ca.technology='SIP' OR ca.technology='DAHDI' OR ca.technology='Custom'";			
	$rows = $this->helper->getTable("lineio", $sql);
  
	foreach ($rows as $row) {
		array_push($trunklist, $row['pkey']);
	} 
	
	$res = $this->dbh->query("SELECT * FROM route where pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('route');
	echo '<input type="text" name="pkey" id="pkey" size="20" style = "background-color: lightgrey" readonly="readonly" value="' . $res['pkey'] . '"  />' . PHP_EOL;
	
	$this->myPanel->aLabelFor('active');
	$this->myPanel->selected = $res['active'];
	$this->myPanel->popUp('active', array('YES','NO'));		
		
	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->selected = $res['cluster'];
	$this->myPanel->displayCluster();
	
	$this->myPanel->aLabelFor('auth');
	$this->myPanel->selected = $res['auth'];
	$this->myPanel->popUp('auth', array('NO','YES'));	
	
	$this->myPanel->aLabelFor('strategy');
	$this->myPanel->selected = $res['strategy'];
	$this->myPanel->popUp('strategy', array('hunt','balance'));			
		
	$this->myPanel->aLabelFor('routedesc');
	echo '<input type="text" name="desc" id="desc size="50" value="' . $res['desc'] . '"   />' . PHP_EOL;
	$this->myPanel->aLabelFor('dialplan');
	echo '<input type="text" name="dialplan" id="dialplan"  size="50" value="' . $res['dialplan'] . '"   />' . PHP_EOL;
	
	$this->myPanel->aLabelFor('path1');
	$this->myPanel->selected = $res['path1'];
	$this->myPanel->popUp('path1', $trunklist);	
	$this->myPanel->aLabelFor('path2');
	$this->myPanel->selected = $res['path2'];
	$this->myPanel->popUp('path2', $trunklist);		
	$this->myPanel->aLabelFor('path3');
	$this->myPanel->selected = $res['path3'];
	$this->myPanel->popUp('path3', $trunklist);	
	$this->myPanel->aLabelFor('path4');
	$this->myPanel->selected = $res['path4'];
	$this->myPanel->popUp('path4', $trunklist);		
	echo '</div>';
}

private function saveEdit() {
// save the data away
	$tuple = array();

	$this->validator = new FormValidator();
    $this->validator->addValidation("desc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("dialplan","regexp=/^[\+0-9XNZxnz_!#\s\*\.\-\/\[\]]+$/","Dialplan must be a valid Asterisk dialplan");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$this->helper->buildTupleArray($_POST,$tuple);


/*
 * call the setter
 */ 
		$ret = $this->helper->setTuple("route",$tuple);
/*
 * flag errors
 */ 	
		if ($ret == 'OK') {
//				$this->helper->commitOn();	
				$this->message = "Updated route " . $tuple['pkey'] . "!";
		}
		else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash[route] = $ret;	
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
