<?php
// sarkagent class
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

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/AsteriskManager.php";

Class sarkagent {
	
	protected $message; 
	protected $head = "Agents";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $params = array('server' => '127.0.0.1', 'port' => '5038');
	protected $astrunning=false;
	protected $error_hash = array();
	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}	

	$this->myPanel->pagename = 'Agents';
		
	if ( isset($_POST['new']) || isset($_GET['new'] )) { 
		$this->showNew();
		return;
	}

	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	

	if ( isset($_POST['save']) || isset($_POST['endsave']) ) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}
	}

	if (isset($_POST['update']) || isset($_POST['endupdate']) ) { 
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
	
	$queues = array(); 	
	if ( $this->astrunning ) {			
		$ami = new ami($this->params);
		$amiconrets = $ami->connect();
		if ( !$amiconrets ) {
			$this->myPanel->msg .= "  (AMI Connect failed)";
		}
		else {
			$ami->login('sark','mysark');
			$amiQrets = $ami->getQueues();
			$ami->logout();

/*			
			$qlines = explode("\r\n",$amiQrets);	
			foreach ($qlines as $line) {
				echo $line . '<br/>';
			}
*/			
		}
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}	
	
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 

/* 
 * start page output
 */
  
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkagentForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);


	echo '<form id="sarkagentForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('agenttable',' w3-small');	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('agent');
	$this->myPanel->aHeaderFor('agentname');	
	$this->myPanel->aHeaderFor('PIN',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('state',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('q1',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('q2',false,'w3-hide-small');
/*	
	$this->myPanel->aHeaderFor('q3',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('q4',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('q5',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('q6',false,'w3-hide-small');
*/
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
	$sql = "select * from agent ORDER by cluster COLLATE NOCASE ASC";
	$class = null;
	$state = '<i class="fas fa-thumbs-down"></i>';
	$locked = false;
 	foreach ($this->dbh->query($sql) as $row) {
		$agent = 'Agent\/' . $row['pkey'] ;
		if (preg_match ("/ $agent /", $amiQrets)) {
			preg_match ("/$agent.*Local\/(\d+)/", $amiQrets, $matches);
			$class = 'class="read_only"';
			$state = '<i class="fas fa-thumbs-up"></i>';
			if (!empty($matches[1])) {
				$state .= '(' . $matches[1] . ')';
			}
			$locked = true;
		}
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="cluster  w3-hide-small w3-hide-medium">' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td >' . substr($row['pkey'],2) . '</td>' . PHP_EOL;		
		echo '<td >' . $row['name'] . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small ">***</td>' . PHP_EOL;
		echo '<td class="w3-hide-small ">' . $state . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small" >' . $row['queue1'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small ">' . $row['queue2'] . '</td>' . PHP_EOL;
/*		
		echo '<td class="w3-hide-small ">' . $row['queue3'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small ">' . $row['queue4'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small ">' . $row['queue5'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small ">' . $row['queue6'] . '</td>' . PHP_EOL;
*/
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);

		$get = '?id=' . $row['pkey'];
		if ($locked) {
			$this->myPanel->lockState();
		}
		else {		
			$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		}
		echo '</tr>'. PHP_EOL;
		$class = null;
		$state = '<i class="fas fa-thumbs-down"></i>';
		$locked = false;
	}
	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();	

}

private function showNew() {
	
	$res = $this->dbh->query("SELECT AGENTSTART FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$agentstart = $res['AGENTSTART'];
	
	while (1) {		
		$res = $this->dbh->query("SELECT pkey FROM agent where pkey = '" . $agentstart . "'")->fetch(PDO::FETCH_ASSOC);
		if ( isset($res['pkey']) ) {
			$agentstart++;
		}
		else {
			break;
		}
	}   
	$pkey	=  $agentstart;	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkagentForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Agent");

	echo '<form id="sarkagentForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->displayInputFor('agentname','text',null,'name');

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = "default";
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	$this->myPanel->displayInputFor('pin','number',"1001",'passwd');



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
    $this->validator->addValidation("name","req","Please fill in Agent name");
    $this->validator->addValidation("passwd","req","Please fill in Agent PIN");

 
    //Now, validate the form
    if ($this->validator->ValidateForm()) {
    

    
/*
 *	get the cluster id 
 */
    	$sql = $this->dbh->prepare("SELECT id FROM cluster WHERE pkey = ?");
		$sql->execute(array($_POST['cluster']));
		$resid = $sql->fetch();
		$sql=NULL;
		
		$res = $this->dbh->query("SELECT startagent FROM cluster where pkey = '" . $_POST['cluster'] . "'")->fetch(PDO::FETCH_ASSOC);
		$startagent = $resid['id'] .$res['startagent'];
	
		while (1) {		
			$res = $this->dbh->query("SELECT pkey FROM agent where pkey = '" . $startagent . "' AND cluster = '" . $_POST['cluster'] . "'")->fetch(PDO::FETCH_ASSOC);
			if ( isset($res['pkey']) ) {
				$startagent++;
			}
			else {
				break;
			}
		}    		

		$_POST['pkey'] = $startagent;	;
		
		
    
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
				   
		$ret = $this->helper->createTuple("agent",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Saved new Agent " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['agentinsert'] = $ret;	
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

	$pkey = $_GET['pkey']; 
	
	$res = $this->dbh->query("SELECT * FROM agent where pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkagentForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Edit Agent " . substr($pkey,2));

	echo '<form id="sarkagentForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';	

	echo '<div id="clustershow">';
	$this->myPanel->displayInputFor('cluster','text',$res['cluster'],'cluster');
	echo '</div>';

	$this->myPanel->displayInputFor('agentname','text',$res['name'],'name');

	$this->myPanel->displayInputFor('pin','number',$res['passwd'],'passwd');

	$user =  $_SESSION['user']['pkey'];
  	$wherestring = 'ORDER BY pkey';
	
  	if ($_SESSION['user']['pkey'] == 'admin') {
		
  	}
  	else {
		$cluster = $dbh->query("SELECT cluster from user where pkey='" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);		
  	}

  	
  	$queuelist = array();
  	$sql = "select * from Queue WHERE cluster='" . $res['cluster'] . "'";
  	foreach ($this->dbh->query($sql) as $row) { 
  		$queueList[] = $row['pkey'];
  	}
  	
  	$queueList[] = 'None';

	$i = 1;
	while ($i < 6) {
		$this->myPanel->displayPopupFor('queue'.$i,$res['queue'.$i],$queueList);
		$i++;
	}

	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";
	$this->myPanel->endBar($endButtonArray);

	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '" />' . PHP_EOL;

	echo '</form>' . PHP_EOL;
	
	$this->myPanel->responsiveClose();	
}


private function saveEdit() {
// save the data away

	$this->validator = new FormValidator();
    $this->validator->addValidation("name","req","Please fill in Agent name");
    $this->validator->addValidation("passwd","req","Please fill in Agent PIN");
    
 
    //Now, validate the form
    if ($this->validator->ValidateForm()) {


/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);			   
		$ret = $this->helper->setTuple("agent",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Updated Agent " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['agentupdate'] = $ret;	
		}		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    unset ($this->validator);
}

}
?>



