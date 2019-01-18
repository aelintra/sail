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
		
	echo '<form id="sarkqueueForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Queues';
	
	if (isset($_POST['new_x']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;	
	}
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	
	

	if (isset($_POST['save_x'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}
	
	if (isset($_POST['update_x'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
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
	
	$tabname = 'queuetable';
	
	echo '<div class="datadivnarrow">';
	
	echo '<table class="display" id="' . $tabname . '"  >' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('queuename');
	$this->myPanel->aHeaderFor('cluster'); 	
	$this->myPanel->aHeaderFor('queueoptions');
	$this->myPanel->aHeaderFor('devicerec');
	$this->myPanel->aHeaderFor('preannounce');
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("queue");
	foreach ($rows as $row ) { 
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;			
		echo '<td >' . $row['cluster']  . '</td>' . PHP_EOL;		 
		echo '<td class="icons">' . $row['options']  . '</td>' . PHP_EOL;	
		echo '<td class="icons">' . $row['devicerec']  . '</td>' . PHP_EOL;	
		echo '<td class="icons">' . $row['greetnum']  . '</td>' . PHP_EOL;
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';	
}

private function showNew() {
	
	$this->myPanel->msg .= "Add New Queue "; 
	
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
	$this->myPanel->aLabelFor('queue');
	echo '<input type="text" name="pkey" size="20" id="pkey" placeholder="QueueName" />' . PHP_EOL;	
	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->displayCluster();	
	echo '</div>';
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
	$queue = $this->dbh->query("SELECT * FROM queue WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$printline = "Queue " . $pkey;
	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 

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
// ToDo - make into fancybox on main page
	echo '<div class="datadivtabedit">';
//    $this->myPanel->aLabelFor('queuename');
	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;	
	echo '<textarea class="qbox" name="conf" id="conf">' . $queue['conf'] . '</textarea>' . PHP_EOL;
	echo '<input type="hidden" name="pkey" id="pkey" value="' . $queue['pkey'] . '"  />' . PHP_EOL;
	echo '</div>'; 
			
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
