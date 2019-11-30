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


Class sarkholiday {
	
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
	
	$this->myPanel->pagename = 'Holiday Scheduler';

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
	$tabname = 'holidaytable';
	
	echo '<div class="datadivnarrow">';
	
	echo '<table class="display" id="' . $tabname . '" >' ;

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('schedstart');
	$this->myPanel->aHeaderFor('schedend');	
	$this->myPanel->aHeaderFor('cluster');
	$this->myPanel->aHeaderFor('description'); 
	$this->myPanel->aHeaderFor('route');
	$this->myPanel->aHeaderFor('state');	
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

//	$rows = $this->helper->getTable("Holiday");
	$rows = $this->dbh->query("SELECT * FROM Holiday ORDER BY stime");
	$now = time();
	foreach ($rows as $row ) {
		
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 		
		echo '<td >' . date('d-m-Y H:i:s', $row['stime'])  . '</td>' . PHP_EOL;
		echo '<td >' . date('d-m-Y H:i:s', $row['etime'])  . '</td>' . PHP_EOL;					 
		echo '<td >' . $row['cluster']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['desc']  . '</td>' . PHP_EOL;
		$this->helper->pkey = $row['route'];
		echo '<td >' . $this->helper->displayRouteClass($row['routeclass']) . '</td>' . PHP_EOL;
		$active = 'IDLE';
		if ($now > $row['stime'] && $now < $row['etime']) {
			$active = '*INUSE*';
		}
		echo '<td >' . $active . '</td>' . PHP_EOL;		
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
	
	$this->myPanel->msg .= "Add New Schedule "; 
	
	$days = array(	'01','02','03','04','05','06','07','08','09','10','11','12','13','14','15',
					'16','17','18','19','20','21','22','23','24','25','26','27','28','29','30','31'
	);

	$months = array('01','02','03','04','05','06','07','08','09','10','11','12');
	
	$years = array();
	$thisyear = date("Y");	
	for ($x = $thisyear; $x <= ($thisyear + 4) ; $x++) {
		array_push($years, $x);
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
	echo '<br/><br/>';	
	echo '<div class="editinsert">';
	
	echo '<h2>Schedule</h2>';
	$this->myPanel->aLabelFor('description');
	echo '<input type="test" name="desc" id="desc" size="30" placeholder="enter a description"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('cluster','cluster');
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();	
	echo '<h2>Start of period</h2>';
		
	$this->myPanel->aLabelFor('hhmm');
	echo '<input type="test" name="stime" id="stime" size="5" value="00:00"  />' . PHP_EOL;	
	
	$this->myPanel->aLabelFor('day');
	$this->myPanel->popUp('sdate', $days);	
	
	$this->myPanel->aLabelFor('month');
	$this->myPanel->popUp('smonth', $months);	
	
	$this->myPanel->aLabelFor('year');
	$this->myPanel->selected = $thisyear;
	$this->myPanel->popUp('syear', $years);	
	
	
	echo '<h2>End of period</h2>';	
	$this->myPanel->aLabelFor('hhmm');
	echo '<input type="test" name="etime" id="etime" size="5" value="00:00"  />' . PHP_EOL;	
	
	$this->myPanel->aLabelFor('day');
	$this->myPanel->popUp('edate', $days);	
	
	$this->myPanel->aLabelFor('month');
	$this->myPanel->popUp('emonth', $months);	
	
	$this->myPanel->aLabelFor('year');
	$this->myPanel->selected = $thisyear;
	$this->myPanel->popUp('eyear', $years);
	
	echo '<h2>Routing</h2>';		
	$this->myPanel->aLabelFor('route');
	$this->myPanel->sysSelect('route',false,false,false) . PHP_EOL;
		
		
	echo '</div>';
}

private function saveNew() {
	
	$tuple = array();	
		
	$tuple['pkey'] 			= 'sched' . rand(100000, 999999);
	if (!empty($_POST['cluster'])) {
		$tuple['cluster'] 		= strip_tags($_POST['cluster']);
	}
	if (!empty($_POST['desc'])) {
		$tuple['desc'] 			= strip_tags($_POST['desc']);
	}
	if (!empty($_POST['route'])) {
		$tuple['route'] 		= strip_tags($_POST['route']);
		$tuple['routeclass'] 	= $this->helper->setRouteClass( $tuple['route'] );
	}
	
	
//sort out the 2 dates

	$shm = strip_tags($_POST['stime']);
	$sdd = strip_tags($_POST['sdate']);
	$smm = strip_tags($_POST['smonth']);
	$syy = strip_tags($_POST['syear']);

// Check HH:MM format 	
	if (!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $shm)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertstarttime'] = "Illegal start time (hh:mm) $shm";
	}
			
// check date is sensible		
	if (!checkdate($smm,$sdd,$syy)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertstartdate'] = "Illegal start date $smm:$sdd:$syy";
	}
	
	$ehm = strip_tags($_POST['etime']);
	$edd = strip_tags($_POST['edate']);
	$emm = strip_tags($_POST['emonth']);
	$eyy = strip_tags($_POST['eyear']);	
	
// Check HH:MM format 	
	if (!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $ehm)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertendtime'] = "Illegal start time (hh:mm) $ehm";
	}		

// check date is sensible	
	if (!checkdate($emm,$edd,$eyy)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertendtime'] = "Illegal end date $smm:$sdd:$syy";
	}

// convert the inputs to Epoch time	
	$hmsplit = preg_split('/:/',$shm);
	$tm = new DateTime();
	$tm-> setDate($syy, $smm, $sdd);
	$tm-> setTime($hmsplit[0], $hmsplit[1], 00);
	$sepoch = $tm->getTimestamp();	
	$hmsplit = preg_split('/:/',$ehm);
	$tm-> setDate($eyy, $emm, $edd);
	$tm-> setTime($hmsplit[0], $hmsplit[1], 00);
	$eepoch = $tm->getTimestamp();

// check end > start		
	if ($sepoch > $eepoch) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertendtime'] = "End time must be after start time - stime = $sepoch, etime = $eepoch";
	}

// check for overlap with existing rows in the same cluster (overlap between clusters is OK)
	$sql = $this->dbh->prepare("SELECT * FROM Holiday WHERE cluster=? AND ? < etime AND stime < ?") ;
	$sql->execute(array($tuple['cluster'],$sepoch,$eepoch));
	$res = $sql->fetch();
	if (!empty($res)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertoverlap'] = "Period overlaps an existing period in the same cluster stime = $sepoch, etime = $eepoch";
	}
			  	
// update	
	if (!$this->invalidForm) {
		$tuple['stime'] = $sepoch;
		$tuple['etime'] = $eepoch;
		$ret = $this->helper->createTuple("holiday",$tuple);
		if ($ret == 'OK') {
//		$this->helper->commitOn();	
			$this->message = "Saved new Schedule ";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['exteninsert'] = $ret;	
		}
	
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
	}		
}

}
