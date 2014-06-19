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
require_once "../srkPageClass";
require_once "../srkDbClass";
require_once "../srkHelperClass";



Class timer {
	
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
	echo '<form id="sarktimerForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Timers';
	
	if (isset($_POST['new_x'])) { 
		$this->saveNew();
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
	if ( $_SERVER['REMOTE_USER'] == 'admin' ) {
		$tabname .= 'admin';
	}
	
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
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		echo '<input type="hidden" name="pkey" id="pkey" value="' . $row['pkey'] . '"  />' . PHP_EOL; 
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
}

private function saveNew() {
	
	$tuple = array();		
	$tuple['pkey'] 			= 'dateSeg' . rand(100000, 999999);

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
