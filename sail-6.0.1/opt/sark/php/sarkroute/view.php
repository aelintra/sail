<?php
//
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


Class sarkroute {
	
	protected $message; 
	protected $head = "Routes";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $myBooleans = array(
		'active',
		'auth'
	);
	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;

//	$this->myPanel->pagename = 'Outbound Routing';
			
	if ( isset($_POST['new']) || isset ($_GET['new'])) { 
		$this->showNew();
		return;	
	}
	
	if (isset($_POST['save']) || isset($_POST['endsave'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}	
	}
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}			
	
	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		$this->showEdit();
		return;				
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
	if ( $_SESSION['user']['pkey'] == 'admin' ) {
		echo '<a  href="/php/downloadpdf.php?pdf=routes"><img id="pdfprint" src="/sark-common/buttons/print.png" border=0 title = "Click to Download PDF" ></a>' . PHP_EOL;									
	}
*/

	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkrouteForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	
	$this->myPanel->responsiveSetup(2);
//	$this->myPanel->subjectBar("Routes");

	echo '<form id="sarkrouteForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('routetable',' w3-tiny');
	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;

	
	$this->myPanel->aHeaderFor('route'); 
	$this->myPanel->aHeaderFor('dialplan',false,'w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('cluster',false,'cluster w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('routedesc',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('strategy',false,'w3-hide-small');	
	$this->myPanel->aHeaderFor('path1',false,'w3-hide-small');
//	$this->myPanel->aHeaderFor('path2',false,'w3-hide-small w3-hide-medium');
//	$this->myPanel->aHeaderFor('path3');
//	$this->myPanel->aHeaderFor('path4');			
//	$this->myPanel->aHeaderFor('auth',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('Act',false,'w3-hide');	
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("route");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
	
		echo '</td>' . PHP_EOL;		
		echo '<td >' . $row['pkey'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['dialplan'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['cluster'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $row['desc'] . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small">' . $row['strategy'] . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-small">' . $row['path1'] . '</td>' . PHP_EOL;
//		echo '<td class="w3-hide-small w3-hide-medium">' . $row['path2'] . '</td>' . PHP_EOL;
//		echo '<td >' . $row['path3'] . '</td>' . PHP_EOL;
//		echo '<td >' . $row['path4'] . '</td>' . PHP_EOL;		
//		echo '<td class="w3-hide-small">' . $row['auth'] . '</td>' . PHP_EOL;
		echo '<td class="w3-hide">' . $row['active'] . '</td>' . PHP_EOL;		
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);	
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);	
		echo '<input type="hidden" name="pkey" id="pkey" value="' . $row['pkey'] . '"  />' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();

}

private function showNew() {

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkrouteForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Route");

	echo '<form id="sarkrouteForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$trunklist = $this->helper->getTrunklist();
	
	$this->myPanel->displayInputFor('route','text',null,'pkey');	
	$this->myPanel->displayBooleanFor('active','YES');		
		
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = null;
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';
	
	$this->myPanel->displayBooleanFor('auth','NO');	
	$this->myPanel->radioSlide('strategy','hunt',array('hunt','balance'));		
	$this->myPanel->displayInputFor('routedesc','text',null,'desc');
	$this->myPanel->displayInputFor('dialplan','text');	
	$this->myPanel->displayPopupFor('path1',null,$trunklist);
	$this->myPanel->displayPopupFor('path2',null,$trunklist);
	$this->myPanel->displayPopupFor('path3',null,$trunklist);
	$this->myPanel->displayPopupFor('path4',null,$trunklist);

	echo '</div>';
	

	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
	
	echo '</form>' . PHP_EOL; // close the form  
    $this->myPanel->responsiveClose();				
}

private function saveNew() {
// save the data away
	$this->myPanel->xlateBooleans($this->myBooleans);

	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Route name");
    $this->validator->addValidation("pkey","regexp=/^[_0-9 A-Za-z-_]+$/","Route name must be alpha numeric (no spaces, no special characters)");    
    $this->validator->addValidation("desc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("dialplan","regexp=/^[\+0-9XNZxnz_!#\s\*\.\-\/\[\]]+$/","Dialplan must be a valid Asterisk dialplan");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {

/*
 * 	call the tuple builder to create a table row array 
 */  
//		$tuple['pkey'] 	 = '_route' . rand(1000, 9999);
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->createTuple("route",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['speedinsert'] = $ret;	
		}
				
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    unset ($this->validator);

}

private function showEdit($key=False) {
	
	if ($key != False) {
		$pkey=$key;
	}
	else {
		$pkey = $_REQUEST['pkey']; 
	}

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkrouteForm",false,true,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar($pkey);

	echo '<form id="sarkrouteForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
		
	$trunklist = $this->helper->getTrunklist();;
	
	$res = $this->dbh->query("SELECT * FROM route where pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$this->myPanel->displayBooleanFor('active',$res['active']);	
	$this->myPanel->displayInputFor('route','text',$pkey);	
				
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $res['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';

	$trunklist = $this->helper->getTrunklist();
	
	$this->myPanel->displayBooleanFor('auth',$res['auth']);	
	$this->myPanel->radioSlide('strategy',$res['strategy'],array('hunt','balance'));		
	$this->myPanel->displayInputFor('routedesc','text',$res['desc'],'desc');
	$this->myPanel->displayInputFor('dialplan','text',$res['dialplan']);	
	$this->myPanel->displayPopupFor('path1',$res['path1'],$trunklist);
	$this->myPanel->displayPopupFor('path2',$res['path2'],$trunklist);
	$this->myPanel->displayPopupFor('path3',$res['path3'],$trunklist);
	$this->myPanel->displayPopupFor('path4',$res['path4'],$trunklist);

	echo '</div>';
		

	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);

	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '" />' . PHP_EOL;
	
	echo '</form>' . PHP_EOL; // close the form  
    $this->myPanel->responsiveClose();
}

private function saveEdit() {
// save the data away

	$tuple = array();
	$this->myPanel->xlateBooleans($this->myBooleans);

	$this->validator = new FormValidator();
    $this->validator->addValidation("desc","alnum_s","Description must be alphanumeric (no special characters)"); 
    $this->validator->addValidation("dialplan","regexp=/^[\+0-9XNZxnz_!#\s\*\.\-\/\[\]]+$/","Dialplan must be a valid Asterisk dialplan");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$this->helper->buildTupleArray($_POST,$tuple);

/*
 * call the setter
 */ 
		$ret = $this->helper->setTuple("route",$tuple);
/*
 * flag errors
 */ 	
		if ($ret == 'OK') {
//				$this->helper->commitOn();	
				$this->message = "Updated route " . $tuple['pkey'] . "!";
		}
		else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash[route] = $ret;	
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
