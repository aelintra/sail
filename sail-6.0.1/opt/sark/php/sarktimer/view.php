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
	protected $head = "Timers";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $myBooleans = array(
		'allday'		
	);
	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
		
	$this->myPanel->pagename = 'Timers';

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
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}
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

	$this->myPanel->beginResponsiveTable('timertable',' w3-small');


	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium'); 	
	$this->myPanel->aHeaderFor('sclose');
	$this->myPanel->aHeaderFor('eclose');
	$this->myPanel->aHeaderFor('weekday',false,'w3-hide-small');
//	$this->myPanel->aHeaderFor('datemonth',false,'w3-hide-small');
//	$this->myPanel->aHeaderFor('month',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('description',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('state');
	$this->myPanel->aHeaderFor('ed');		
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("dateseg",NULL,true,false,"cluster,dayofweek");
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
		echo '<td class="cluster w3-hide-small  w3-hide-medium">' . $row['cluster']  . '</td>' . PHP_EOL;		 
		echo '<td >' . $beginclosed  . '</td>' . PHP_EOL;
		echo '<td >' . $endclosed  . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small">' . $row['dayofweek']  . '</td>' . PHP_EOL;
//		echo '<td class="w3-hide-small">' . $row['datemonth']  . '</td>' . PHP_EOL;
//		echo '<td class="w3-hide-small">' . $row['month']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $row['desc']  . '</td>' . PHP_EOL;
		echo '<td >' . $row['state']  . '</td>' . PHP_EOL;

		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);	

		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		
		echo '<input type="hidden" name="pkey" id="pkey" value="' . $row['pkey'] . '"  />' . PHP_EOL; 
		echo '</td>' . PHP_EOL;
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
	$this->myPanel->subjectBar("New Timer");

	echo '<form id="sarktimerForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->displayInputFor('description','text',null,'desc');
	
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $extension['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	echo '</div>';

	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL;
	echo '</form>' . PHP_EOL;		
	$this->myPanel->responsiveClose();	

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
		$this->message = "Validation Errors!";	
		$this->error_hash['exteninsert'] = $ret;	
	}	
}

private function showEdit() {

	$pkey = $_REQUEST['pkey']; 

	$tuple = $this->dbh->query("SELECT * FROM dateseg WHERE pkey='" . $pkey ."'")->fetch(PDO::FETCH_ASSOC);

	if ( $tuple['timespan'] == '*') {
		$beginclosed = '*';
	 	$endclosed = '*';
	}
	else {
		$stopstart = explode ('-', $tuple['timespan']);
		$beginclosed = $stopstart[0];
		$endclosed = $stopstart[1];
	}

	$alldaystyle = null;
	$allday = 'NO';
	if ($beginclosed == '*' && $endclosed == '*') {
		$allday = 'YES';
		$alldayStyle = 'style="display:none"';
	}
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarktimerForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Update Time rule " . $pkey);

	echo '<form id="sarktimerForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->displayBooleanFor('allday', $allday,'allday');
	
	$this->myPanel->internalEditBoxStart();
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $tuple['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';
	echo '</div>';

	
	

	echo '<div class="allday" ' . $alldayStyle . '>';
	$this->myPanel->internalEditBoxStart();
	
	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('sclose');
	echo '</div>';
    echo '<input class="w3-input w3-border w3-round timepicker" type="text" name="sdate" id="sdate" value="' . $beginclosed . '"/>';
    $this->myPanel->aHelpBoxFor('sclose');

	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('eclose');
	echo '</div>';
    echo '<input class="w3-input w3-border w3-round timepicker" type="text" name="edate" id="edate" value="' . $endclosed . '"/>';
    $this->myPanel->aHelpBoxFor('eclose');
    echo '</div>';
    echo '</div>';

    $this->myPanel->internalEditBoxStart();
    $mySelected = $tuple['dayofweek'];
    if ($mySelected == '*') {
    	$mySelected = 'Every Day';
    }
    $this->myPanel->selected = $mySelected;
	$this->myPanel->displayPopupFor('dayofweek',$mySelected,Array('Every Day','mon','tue','wed','thu','fri','sat','sun'));
	echo '</div>';

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->displayInputFor('description','text',$tuple['desc'],'desc');
	echo '</div>';

	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
		
	echo '</div>';
	echo '</div>';
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>' . PHP_EOL;
	
	$this->myPanel->responsiveClose();
}

private function saveEdit() {
//print_r($_REQUEST);
	$tuple = array();
	$custom = array (
			'sclose' => True,
			'eclose' => True,
			'sdate' => True,
			'edate' => True,
			'allday' => True
		);

	$this->myPanel->xlateBooleans($this->myBooleans);

	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Call Group name");
    $this->validator->addValidation("pkey","alnum","Call Group name must be alphanumeric(no spaces)");    
    $this->validator->addValidation("description","alnum_s","Description must be alphanumeric (no special characters)"); 
    
    //Now, validate the form
    if ($this->validator->ValidateForm()) {

	$this->helper->buildTupleArray($_POST,$tuple,$custom);	

    if ($_POST['allday'] == 'YES') {
    	$tuple['timespan'] = '*-*';
    }
    else {
    	$tuple['timespan'] = $_POST['sdate'] . '-' . $_POST['edate'];
    }

    if (isset($_POST['dayofweek'])) {
    	if ($tuple['dayofweek'] == 'Every Day') {
    		$tuple['dayofweek'] = '*';
    	}
    }
	  
	

	$tuple['pkey'] = $_POST['pkey'];

	$ret = $this->helper->setTuple("dateseg",$tuple);
	if ($ret == 'OK') {
		$this->message = "Updated Date Rule ";
	}
	else {
		$this->invalidForm = True;
		$this->message = "Validation Errors!";	
		$this->error_hash['extenupdate'] = $ret;	
	}	
}

}

}
