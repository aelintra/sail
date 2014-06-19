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
require "../srkPageClass";
require "../srkDbClass";
require "../srkHelperClass";

Class agent {
	
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
	echo '<form id="sarkagentForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Agents';
		
	if (isset($_POST['new_x'])) { 
//		syslog(LOG_WARNING, "New Agent ");
		$this->saveNew();	
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
	$tabname = 'agenttable';
	if ( $_SERVER['REMOTE_USER'] == 'admin' ) {
		$tabname .= 'admin';
	}
	
	echo '<div class="datadivwide">';
	
	echo '<table class="display" id="' . $tabname . '" >' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('agent');
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('agentname');	
	$this->myPanel->aHeaderFor('PIN');
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
 	foreach ($this->dbh->query($sql) as $row) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['name'] . '</td>' . PHP_EOL;		
		echo '<td>***</td>' . PHP_EOL;
		echo '<td >' . $row['queue1'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['queue2'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['queue3'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['queue4'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['queue5'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['queue6'] . '</td>' . PHP_EOL;
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';

}

private function saveNew() {
// save the data away	
	$tuple = array();	
	
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
    
	$tuple['pkey'] 		=  $agentstart;
	$tuple['passwd']	=  $agentstart;
	$ret = $this->helper->createTuple("agent",$tuple);
	if ($ret == 'OK') {
//		$this->helper->commitOn();	
		$this->message = "Saved new agent " . $tuple['pkey'] . "!";
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash['agentinsert'] = $ret;	
	}

}

}
