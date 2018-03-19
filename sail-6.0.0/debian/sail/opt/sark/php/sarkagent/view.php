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

	echo '<form id="sarkagentForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
		
	$this->myPanel->pagename = 'Agents';
		
	if ( isset($_POST['new_x']) || isset($_GET['new'] )) { 
		$this->showNew();
		return;
	}
	if ( isset($_POST['save_x']) ) { 
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
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->commitButton();
	echo '</div>';	

	$this->myPanel->Heading();
	$tabname = 'agenttable';
	
	echo '<div class="datadivnarrow">';
	
	echo '<table class="display" id="' . $tabname . '" >' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('agent');
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('agentname');	
	$this->myPanel->aHeaderFor('PIN');
	$this->myPanel->aHeaderFor('state');
	$this->myPanel->aHeaderFor('q1');
	$this->myPanel->aHeaderFor('q2');
	$this->myPanel->aHeaderFor('q3');
	$this->myPanel->aHeaderFor('q4');
	$this->myPanel->aHeaderFor('q5');
	$this->myPanel->aHeaderFor('q6');
	$this->myPanel->aHeaderFor('del');
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
	$sql = "select * from agent";
	$class = null;
	$state = 'logged-out';
	$locked = false;
 	foreach ($this->dbh->query($sql) as $row) {
		$agent = 'Agent\/' . $row['pkey'] ;
		if (preg_match ("/ $agent /", $amiQrets)) {
			preg_match ("/$agent.*Local\/(\d+)/", $amiQrets, $matches);
			$class = 'class="read_only"';
			$state = 'logged-in';
			if (!empty($matches[1])) {
				$state .= '(' . $matches[1] . ')';
			}
			$locked = true;
		}
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['name'] . '</td>' . PHP_EOL;		
		echo '<td ' . $class . '>***</td>' . PHP_EOL;
		echo '<td>' . $state . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['queue1'] . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['queue2'] . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['queue3'] . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['queue4'] . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['queue5'] . '</td>' . PHP_EOL;
		echo '<td ' . $class . '>' . $row['queue6'] . '</td>' . PHP_EOL;
		$get = '?id=' . $row['pkey'];
		if ($locked) {
			$this->myPanel->lockState();
		}
		else {		
			$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		}
		echo '</tr>'. PHP_EOL;
		$class = null;
		$state = 'logged-out';
		$locked = false;
	}
	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';

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
	$pkey		=  $agentstart;	
	$this->myPanel->msg .= "Add New Agent "; 
	
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
	$this->myPanel->aLabelFor('agent');
	echo '<input type="text" name="pkey" size="4" id="pkey" value=' . $pkey . ' />' . PHP_EOL;		
	$this->myPanel->aLabelFor('agentname');
	echo '<input type="text" name="name" size="15" id="name" placeholder="Name" />' . PHP_EOL;	
	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->displayCluster();	
	$this->myPanel->aLabelFor('PIN');
	echo '<input type="password" name="passwd" id="passwd" size="5" placeholder="PIN" />' . PHP_EOL;
	echo '</div>';
//	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '" />' . PHP_EOL;		
}


private function saveNew() {
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

}
?>



