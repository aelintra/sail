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


Class sarktimer {
	
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
		
	echo '<form id="sarktimerForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Timers';

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
  		$helper = new helper;
		$helper->sysCommit();
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
	$tabname = 'timertable';
	
	echo '<div class="datadivwide">';
	
	echo '<table class="display" id="' . $tabname . '" >' ;

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('cluster'); 	
	$this->myPanel->aHeaderFor('sclose');
	$this->myPanel->aHeaderFor('eclose');
	$this->myPanel->aHeaderFor('weekday');
	$this->myPanel->aHeaderFor('datemonth');
	$this->myPanel->aHeaderFor('month');
	$this->myPanel->aHeaderFor('description');
	$this->myPanel->aHeaderFor('state');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("dateseg");
	foreach ($rows as $row ) {
		if ( $row['timespan'] == '*') {
			$beginclosed = '*';
			$endclosed = '*';
		}
		else {
			$stopstart = explode ('-', $row['timespan']);
			$beginclosed = $stopstart[0];
			$endclosed = $stopstart[1];
		}
		
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 		
		echo '<td >' . $row['cluster']  . '</td>' . PHP_EOL;		 
		echo '<td >' . $beginclosed  . '</td>' . PHP_EOL;
		echo '<td >' . $endclosed  . '</td>' . PHP_EOL;		
		echo '<td >' . $row['dayofweek']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['datemonth']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['month']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['desc']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['state']  . '</td>' . PHP_EOL;
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		
		echo '<input type="hidden" name="pkey" id="pkey" value="' . $row['pkey'] . '"  />' . PHP_EOL; 
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
}

private function showNew() {
	
	$this->myPanel->msg .= "Add New Timer "; 
	
	$days = array();
	array_push($days, '*');
	for ($x = 1; $x <= 31; $x++) {
		array_push($days, $x);
	}
	
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
	
	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->displayCluster();
	$this->myPanel->aLabelFor('description');
	echo '<input type="test" name="desc" id="desc" size="30" placeholder="enter description"  />' . PHP_EOL;			
		

}

private function saveNew() {
	
	$tuple = array();		
	$tuple['pkey'] 			= 'dateSeg' . rand(100000, 999999);
	
	
	$this->helper->buildTupleArray($_POST,$tuple);		
	$ret = $this->helper->createTuple("dateSeg",$tuple);
	if ($ret == 'OK') {
//		$this->helper->commitOn();	
		$this->message = "Saved new Timer ";
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash['exteninsert'] = $ret;	
	}	
}

}
