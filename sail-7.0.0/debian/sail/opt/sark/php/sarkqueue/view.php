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


Class sarkqueue {
	
	protected $message;
	protected $head = "Queues"; 
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
		
	$this->myPanel->pagename = 'Queues';
	
	if (isset($_POST['new']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;	
	}

	if (isset($_REQUEST['delete'])) { 
		$pkey = $_REQUEST['pkey'];
		$this->helper->delTuple("queue",$pkey); 
		$this->message = "Deleted Queue " . $pkey; 		
	}	
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
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
	$this->myPanel->actionBar($buttonArray,"sarkqueueForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);


	echo '<form id="sarkqueueForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('queuetable',' w3-small');	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;

	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('qdd');
	$this->myPanel->aHeaderFor('queuename');
	$this->myPanel->aHeaderFor('description',false,'w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('queueoptions',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('devicerec',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('preannounce',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("queue");
	foreach ($rows as $row ) { 
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL;
		echo '<input type="hidden" name="id" value="' . $row['id'] . '"  />' . PHP_EOL;		
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['cluster']  . '</td>' . PHP_EOL;
		echo '<td >' . substr($row['pkey'],2) . '</td>' . PHP_EOL;
		echo '<td>' . $row['name']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['description']  . '</td>' . PHP_EOL;					 
		echo '<td class="w3-hide-small ">' . $row['options']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small ">' . $row['devicerec']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small ">' . $row['greetnum']  . '</td>' . PHP_EOL;
		
		$get = '?edit=yes&amp;id=' . $row['id']; 
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
//		$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$row['pkey']);
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();	
}

private function showNew() {
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkqueueForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Queue");

	echo '<form id="sarkqueueForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';	

	$this->myPanel->displayInputFor('qdd','text',null,'pkey');
	$this->myPanel->displayInputFor('queuename','text',null,'name');
	$this->myPanel->displayInputFor('description','text');
		
	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	$this->myPanel->responsiveClose();
}


private function saveNew() {
// save the data away

	$tuple = array();

	$this->validator = new FormValidator();
    $this->validator->addValidation("name","req","Please fill in Queue name");
    $this->validator->addValidation("pkey","req","Please supply Queue direct dial"); 
    $this->validator->addValidation("pkey","num","Queue direct dial must be numeric");    
    $this->validator->addValidation("pkey","maxlen=4","Queue direct dial must be 3 or 4 digits");     
	$this->validator->addValidation("pkey","minlen=3","Queue direct dial must be 3 or 4 digits");     
 
    //Now, validate the form
    if ($this->validator->ValidateForm()) {

// create full pkey
    	$res = $this->dbh->query("SELECT id FROM cluster WHERE pkey = '" . $_POST['cluster'] . "'")->fetch(PDO::FETCH_ASSOC);
		$_POST['pkey'] = $res['id'] . $_POST['pkey']; 
		$res=NULL;
		
// check for dups
	
    $retc = $this->helper->checkXref($_POST['pkey'],$_POST['cluster']);
    if ($retc) {
    	$this->invalidForm = True;
    	$this->error_hash['extinsert'] = "Duplicate found in table $retc - choose a different extension number";
    	return;    	
    }
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);			   
		$ret = $this->helper->createTuple("queue",$tuple,true,$tuple['cluster']);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Saved new Queue " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['queueinsert'] = $ret;	
		}		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);	
	
}

private function showEdit($pkey=false) {
	
	/*
 * pkey could be POST or GET, depending upon the iteration
 */	
	if (isset ($_GET['id'])) {
		$id = $_GET['id']; 
	}
	
	if (isset ($_POST['id'])) {		
		$id = $_POST['id']; 
		$this->saveEdit();
	}
	$res = $this->dbh->query("SELECT * FROM queue WHERE id = '" . $id . "'")->fetch(PDO::FETCH_ASSOC);

	$greetings = $this->helper->getTable("greeting", "SELECT pkey FROM greeting WHERE cluster = '" . $res['cluster'] . "'");
	foreach ($greetings as $greeting) {
		$clusterGreetings[] = substr($greeting['pkey'],12);
	}
	asort($clusterGreetings);
	$clusterGreetings[] = 'None';	

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkqueueForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar('Edit Queue ' . substr($res['pkey'],2));

	echo '<form id="sarkqueueForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	echo '<div id="clustershow">';
	$this->myPanel->displayInputFor('cluster','text',$res['cluster'],'cluster');
	echo '</div>';
	$this->myPanel->displayInputFor('queuename','text',$res['name'],'name');
/*	
	echo '<div id="pkeyshow">';
	$this->myPanel->displayInputFor('qdd','text',substr($res['pkey'],2),'pkey');
	echo '</div>';
*/
	$this->myPanel->displayInputFor('description','text',$res['description']);
	$this->myPanel->displayInputFor('queueoptions','text',$res['options'],'options');
	$this->myPanel->radioSlide('devicerec',$res['devicerec'],array('OTR','OTRR','Inbound'));

	$this->myPanel->aLabelFor('preannounce'); 	
	echo '<br/><br/>';
	$this->myPanel->selected = $res['greetnum'];
	$this->myPanel->popUp('greetnum',$clusterGreetings);
	
	echo '<br/><br/>';	

	echo '<div class="w3-margin-bottom">';	
	$this->myPanel->aLabelFor("conf");
	echo '</div>';	
	echo '<div id="queue" >';
	$this->myPanel->displayFile(htmlspecialchars($res['conf']),"conf");
	echo '</div>' . PHP_EOL;

	echo '<input type="hidden" name="id" value="' . $res['id'] . '"  />' . PHP_EOL;


	echo '</div>';	    			  	 	
	
	
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL; 
	$this->myPanel->responsiveClose();		
}

private function saveEdit() {
// save the data away
//print_r ($_POST) . "\n";

	$tuple = array();
		
	$this->validator = new FormValidator();

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  	

		$this->helper->buildTupleArray($_POST,$tuple);

/*
 * update the SQL database
 */
 
// remove any escaped quotes 			
//		$tuple['conf'] = preg_replace ( "/\\\/", '', $tuple['conf']);
		
		
		$ret = $this->helper->setTupleById("queue",$tuple);
		 
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Updated queue ";
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
