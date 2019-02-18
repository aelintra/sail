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
	
	$this->myPanel->aHeaderFor('queuename');
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('queueoptions',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('devicerec',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('preannounce',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('qdd',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("queue");
	foreach ($rows as $row ) { 
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL;

		echo '<td >' . $row['pkey'] . '</td>' . PHP_EOL;	

		echo '<td class="w3-hide-small w3-hide-medium">' . $row['cluster']  . '</td>' . PHP_EOL;		 
		echo '<td class="w3-hide-small ">' . $row['options']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small ">' . $row['devicerec']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small ">' . $row['greetnum']  . '</td>' . PHP_EOL;
		$qdd = 12200 + $row['id'];
		echo '<td class="w3-hide-small ">' . $qdd  . '</td>' . PHP_EOL;
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
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

	$this->myPanel->displayInputFor('queuename','text',null,'pkey');

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

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
    $this->validator->addValidation("pkey","req","Please fill in Queue name");
    
 
    //Now, validate the form
    if ($this->validator->ValidateForm()) {

/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);			   
		$ret = $this->helper->createTuple("queue",$tuple);
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
	
	if (!$pkey) {
		$pkey = $_GET['pkey']; 
	}
	$res = $this->dbh->query("SELECT * FROM queue WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkqueueForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar('Queue' . " " . $pkey);

	echo '<form id="sarkqueueForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $res['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';


	$this->myPanel->displayInputFor('queueoptions','text',$res['options'],'options');
	$this->myPanel->radioSlide('devicerec',$res['devicerec'],array('OTR','OTRR','Inbound'));
	$this->myPanel->displayInputFor('preannounce','text',$res['greetnum'],'greetnum');

	echo '<div class="w3-margin-bottom">';	
	$this->myPanel->aLabelFor("conf");
	echo '</div>';	
	echo '<div id="queue" >';
	$this->myPanel->displayFile(htmlspecialchars($res['conf']),"conf");
	echo '</div>' . PHP_EOL;

	echo '<input type="hidden" name="pkey" id="pkey" value="' . $res['pkey'] . '"  />' . PHP_EOL;


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
/*
		$custom = array (
						'newkey' => True
        );
*/
		$this->helper->buildTupleArray($_POST,$tuple,$custom);

/*
 * update the SQL database
 */
 
// remove any escaped quotes 			
		$tuple['conf'] = preg_replace ( "/\\\/", '', $tuple['conf']);
		
		if (isset($_POST['newkey'])) {
			$newkey =  trim(strip_tags($_POST['newkey']));
		}
		
		if ($newkey && $newkey != $tuple['pkey']) {	
			$ret = $this->helper->setTuple("queue",$tuple,$newkey);
		}
		else {
			$ret = $this->helper->setTuple("queue",$tuple);
		}			 
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
