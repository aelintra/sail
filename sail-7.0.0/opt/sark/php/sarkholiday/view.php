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
	protected $head = "Holiday Schedule";
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
		
	
	$this->myPanel->pagename = 'Holiday Scheduler';

	if ( isset($_POST['new']) || isset($_GET['new'] )) { 
		$this->showNew();
		return;
	}
	if (isset ($_REQUEST['edit'])) {
		$this->showEdit();
			return;
	}

	if ( isset($_POST['save']) || isset($_POST['endsave'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}
	}

	if ( isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		$this->showEdit();
		return;
	}	
	
	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
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
  
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarktimerForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

	echo '<form id="sarktimerForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	$this->myPanel->beginResponsiveTable('holidaytable',' w3-small');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('schedstart');
	$this->myPanel->aHeaderFor('schedend');	
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('description'); 
	$this->myPanel->aHeaderFor('route',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('state',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('ed',false,'editcol');	
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
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
		echo '<td class="cluster w3-hide-small  w3-hide-medium">' . $row['cluster']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $row['desc']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $row['route']  . '</td>' . PHP_EOL;
//		$this->helper->pkey = $row['route'];
//		echo '<td class="w3-hide-small">' . $this->helper->displayRouteClass($row['routeclass']) . '</td>' . PHP_EOL;
		$active = 'IDLE';
		if ($now > $row['stime'] && $now < $row['etime']) {
			$active = '*INUSE*';
		}
		echo '<td class="w3-hide-small">' . $active . '</td>' . PHP_EOL;		
		$get = '?id=' . $row['pkey'];
		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		
		$this->myPanel->ajaxdeleteClick($get);		
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();	
}

private function showNew() {
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarktimerForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Holiday");

	echo '<form id="sarktimerForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->displayInputFor('description','text');
	
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $extension['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	$date = date('d-m-Y');

	$this->myPanel->internalEditBoxStart();
	echo '<h2>Start of period</h2>';
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('Date');
	echo '</div>';
	
	echo '<input type="text" class="datepicker w3-input w3-border w3-round" name="sdate" id="sdate" value="' . $date . '"  />' . PHP_EOL;
	echo '<div class="w3-margin-bottom">';
	echo '<br/>';	
	$this->myPanel->displayInputFor("time",'time',"00:00",'stime');
	echo '</div>';
	echo '</div>';	
	
	$this->myPanel->internalEditBoxStart();
	echo '<h2>End of period</h2>';	
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('Date');
	echo '</div>';
	echo '<input type="text" class="datepicker w3-input w3-border w3-round" name="edate" id="edate" value="' . $date . '"  />' . PHP_EOL;
	echo '<div class="w3-margin-bottom">';
	echo '<br/>';
	$this->myPanel->displayInputFor("time",'time',"00:00",'etime');
	echo '</div>';
	echo '</div>';

	$this->myPanel->internalEditBoxStart();
	echo '<h2>Routing</h2>';		
	$this->myPanel->sysSelect('route',false,false,false,$extension['cluster']) . PHP_EOL;
	echo '<br/><br/>';
	echo '</div>';
		
	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	
	$this->myPanel->responsiveClose();
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

	$ehm = strip_tags($_POST['etime']);
	$edd = strip_tags($_POST['edate']);

// Check HH:MM format 	
	if (!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $shm)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertstarttime'] = "Illegal start time (hh:mm) $shm";
	}

// Check HH:MM format 	
	if (!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $ehm)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertendtime'] = "Illegal end time (hh:mm) $ehm";
	}		


// convert the inputs to Epoch time	
    $dtsplit = preg_split('/-/',$sdd);
	$hmsplit = preg_split('/:/',$shm);

	$tm = new DateTime();
	$tm-> setDate($dtsplit[2], $dtsplit[1], $dtsplit[0]);
	$tm-> setTime($hmsplit[0], $hmsplit[1], 00);
	$sepoch = $tm->getTimestamp();

	$dtsplit = preg_split('/-/',$edd);
	$hmsplit = preg_split('/:/',$ehm);

	$tm-> setDate($dtsplit[2], $dtsplit[1], $dtsplit[0]);
	$tm-> setTime($hmsplit[0], $hmsplit[1], 00);
	$eepoch = $tm->getTimestamp();

// check end > start		
	if ($sepoch > $eepoch) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertendtime'] = "End time must be after start time - " . date ('d-m-Y H:i:s', $sepoch) . " etime = " . date ('d-m-Y H:i:s', $eepoch);
	}

// check for overlap with existing rows in the same cluster (overlap between clusters is OK)
	$sql = $this->dbh->prepare("SELECT * FROM Holiday WHERE cluster=? AND ? < etime AND stime < ?") ;
	$sql->execute(array($tuple['cluster'],$sepoch,$eepoch));
	$res = $sql->fetch();
	if (!empty($res)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertoverlap'] = "Period overlaps an existing period in the same cluster stime = " . date ('d-m-Y H:i:s', $sepoch) . " etime = " . date ('d-m-Y H:i:s', $eepoch);
	}
			  	
// update	
	if (!$this->invalidForm) {
		if (array_key_exists('route',$tuple)) {
		 $tuple['routeclass'] = $this->helper->setRouteClass($tuple['route']);
		}
		$tuple['stime'] = $sepoch;
		$tuple['etime'] = $eepoch;
		$ret = $this->helper->createTuple("holiday",$tuple);
		if ($ret == 'OK') {
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

private function showEdit() {

	$pkey = $_REQUEST['pkey']; 

	$tuple = $this->dbh->query("SELECT * FROM Holiday WHERE pkey='" . $pkey ."'")->fetch(PDO::FETCH_ASSOC);;
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarktimerForm",false,true,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Update Holiday " . $pkey);

	echo '<form id="sarktimerForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->displayInputFor('description','text',$tuple['desc'],'desc');
	
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster',$tuple['cluster']);
    echo '</div>';
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	$date = date('d-m-Y');

	$this->myPanel->internalEditBoxStart();
	echo '<h2>Start of period</h2>';
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('Date');
	echo '</div>';
	
	echo '<input type="text" class="datepicker w3-input w3-border w3-round" name="sdate" id="sdate" value="' . date('d-m-Y', $tuple['stime']) . '"  />' . PHP_EOL;
	echo '<div class="w3-margin-bottom">';
	echo '<br/>';	
	$this->myPanel->displayInputFor("time",'time',date('H:i:s', $tuple['stime']),'stime');
	echo '</div>';
	echo '</div>';	
	
	$this->myPanel->internalEditBoxStart();
	echo '<h2>End of period</h2>';	
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('Date');
	echo '</div>';
	echo '<input type="text" class="datepicker w3-input w3-border w3-round" name="edate" id="edate" value="' . date('d-m-Y', $tuple['etime']) . '"  />' . PHP_EOL;
	echo '<div class="w3-margin-bottom">';
	echo '<br/>';
	$this->myPanel->displayInputFor("time",'time',date('H:i:s', $tuple['etime']),'etime');
	echo '</div>';
	echo '</div>';

	$this->myPanel->internalEditBoxStart();
	echo '<h2>Routing</h2>';
	$this->myPanel->selected = $tuple['route'];		
	$this->myPanel->sysSelect('route',false,false,false,$tuple['cluster']) . PHP_EOL;
	echo '<br/><br/>';
	echo '</div>';

	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
		
	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	
	$this->myPanel->responsiveClose();
}
private function saveEdit() {

	$tuple = array();
	$custom = array (
					'sdate' => True,
					'edate' => True
	);

	$this->helper->buildTupleArray($_POST,$tuple,$custom);	
	
//sort out the 2 dates

	$shm = strip_tags($_POST['stime']);
	$sdd = strip_tags($_POST['sdate']);

	$ehm = strip_tags($_POST['etime']);
	$edd = strip_tags($_POST['edate']);

// Check HH:MM format 	
	if (!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $shm)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertstarttime'] = "Illegal start time (hh:mm) $shm";
	}

// Check HH:MM format 	
	if (!preg_match("/(2[0-3]|[01][0-9]):([0-5][0-9])/", $ehm)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertendtime'] = "Illegal end time (hh:mm) $ehm";
	}		


// convert the inputs to Epoch time	
    $dtsplit = preg_split('/-/',$sdd);
	$hmsplit = preg_split('/:/',$shm);

	$tm = new DateTime();
	$tm-> setDate($dtsplit[2], $dtsplit[1], $dtsplit[0]);
	$tm-> setTime($hmsplit[0], $hmsplit[1], 00);
	$sepoch = $tm->getTimestamp();

	$dtsplit = preg_split('/-/',$edd);
	$hmsplit = preg_split('/:/',$ehm);

	$tm-> setDate($dtsplit[2], $dtsplit[1], $dtsplit[0]);
	$tm-> setTime($hmsplit[0], $hmsplit[1], 00);
	$eepoch = $tm->getTimestamp();

// check end > start		
	if ($sepoch > $eepoch) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertendtime'] = "End time must be after start time - stime = " . date ('d-m-Y H:i:s', $sepoch) . " etime = " . date ('d-m-Y H:i:s', $eepoch);
	}

// check for overlap with existing rows in the same cluster (overlap between clusters is OK)
	$sql = $this->dbh->prepare("SELECT * FROM Holiday WHERE cluster=? AND ? < etime AND stime < ? and pkey != ?") ;
	$sql->execute(array($tuple['cluster'],$sepoch,$eepoch,$pkey));
	$res = $sql->fetch();
	if (!empty($res)) {
		$this->invalidForm = True;
		$this->error_hash['schedinsertoverlap'] = "Period overlaps an existing period in the same Tenant stime = " . date ('d-m-Y H:i:s', $sepoch) . " etime = " . date ('d-m-Y H:i:s', $eepoch);
	}
			  	
// update	
	if (!$this->invalidForm) {
		$tuple['stime'] = $sepoch;
		$tuple['etime'] = $eepoch;

		$tuple['pkey'] = $_POST['pkey'];
		if (!empty($_POST['route'])) {
			$tuple['routeclass'] 	= $this->helper->setRouteClass($tuple['route'] );
		}
		$ret = $this->helper->setTuple("holiday",$tuple);
		if ($ret == 'OK') {
			$this->message = "Updated Schedule ";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['extenupdate'] = $ret;	
		}
	}
	else {
		$this->invalidForm = True;
		$this->message = "Validation Errors!";	
	}		
}
}
