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


Class sarkcos {
	
	protected $message; 
	protected $head = "Class of Service";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $myBooleans = array(
		'active',
		'defaultopen',
		'defaultclosed',		
		'orideopen',
		'orideclosed',	
	);

public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
		
	$this->myPanel->pagename = 'Class of Service';
	
	if (isset($_POST['new']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;	
	}
	

	if (isset($_POST['delete'])) { 
		$this->deleteRow();
		return;  		
	}
		
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	
	
	if (isset($_POST['save']) || isset($_POST['endsave'])) {
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
		}
		else {
			$this->showEdit();
		}
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
 * start page output
 */
 
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkcosForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$bigTable=true;
	$this->myPanel->responsiveSetup(2);
//	$this->myPanel->subjectBar("Trunks");

	echo '<form id="sarkcosForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->beginResponsiveTable('costable',' w3-tiny');
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('cosname'); 	
	$this->myPanel->aHeaderFor('cosdialplan');
	$this->myPanel->aHeaderFor('cosopen',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('orideopen',false,'w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('cosclosed',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('orideclosed',false,'w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('active',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del',false,'delcol');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("cos");
	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;			
		echo '<td >' . $row['dialplan']  . '</td>' . PHP_EOL;		 
		echo '<td class="w3-hide-small">' . $row['defaultopen']  . '</td>' . PHP_EOL;		
		echo '<td class="w3-hide-medium w3-hide-small">' . $row['orideopen']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $row['defaultclosed']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-medium w3-hide-small">' . $row['orideclosed']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $row['active']  . '</td>' . PHP_EOL;
		$get = '?edit=yes&amp;pkey=';
		$get .= urlencode($row['pkey']);	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		$get = '?id=' . $row['pkey'];		
		$this->myPanel->ajaxdeleteClick($get);		
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
	$this->myPanel->actionBar($buttonArray,"sarkcosForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New CoS Rule");

	echo '<form id="sarkcosForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->displayInputFor('cosname','text',null,'pkey');		
	$this->myPanel->displayBooleanFor('active','NO');
    $this->myPanel->displayInputFor('cosdialplan','text',null,'dialplan');
    $this->myPanel->displayBooleanFor('defaultopen','NO');
    $this->myPanel->displayBooleanFor('orideopen','NO');
    $this->myPanel->displayBooleanFor('defaultclosed','NO');
    $this->myPanel->displayBooleanFor('orideclosed','NO');

	echo '</div>';

    $endButtonArray['cancel'] = true;
    $endButtonArray['save'] = "endsave";
    $this->myPanel->endBar($endButtonArray);
    echo '<br/>' . PHP_EOL;
    echo '</form>' . PHP_EOL; // close the form
    echo '</div>';  
    $this->myPanel->responsiveClose();  		
}

private function saveNew() {
// save the data away
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in COS name");
    $this->validator->addValidation("pkey","alnum","COS name must be alpha numeric (no spaces, no special characters)");    
    $this->validator->addValidation("dialplan","regexp=/^[\+0-9XNZxnz_!#\s\*\.\-]+$/","Dialplan must be a valid Asterisk dialplan");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->createTuple("cos",$tuple);
		if ($ret == 'OK') {
			$this->message = "Saved new COS " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['cosinsert'] = $ret;	
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
/*
 * General update page.  Jquery controls tabs
 */

    if (isset($_POST['pkey'])) {
        $pkey = $_POST['pkey'];
    }
    else if (isset($_GET['pkey'])) {
        $pkey = $_GET['pkey'];
    } 

    $tuple = $this->dbh->query("select * from cos where pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
    $buttonArray['cancel'] = true;
    $this->myPanel->actionBar($buttonArray,"sarkcosForm",false,false,true);

    if ($this->invalidForm) {
        $this->myPanel->showErrors($this->error_hash);
    }
    $this->myPanel->Heading($this->head,$this->message);
    $this->myPanel->responsiveSetup(2);

    $this->myPanel->internalEditBoxStart();
    $this->myPanel->subjectBar("Edit CoS Rule " . $pkey);

    echo '<form id="sarkcosForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
    
    $this->myPanel->displayBooleanFor('active',$tuple['active']);
    $this->myPanel->displayInputFor('cosdialplan','text',$tuple['dialplan'],'dialplan');
    $this->myPanel->displayBooleanFor('defaultopen',$tuple['defaultopen']);
    $this->myPanel->displayBooleanFor('orideopen',$tuple['orideopen']);
    $this->myPanel->displayBooleanFor('defaultclosed',$tuple['defaultclosed']);
    $this->myPanel->displayBooleanFor('orideclosed',$tuple['orideclosed']);

    echo '</div>';

    echo '<input type="hidden" name="pkey" id="pkey" size="20"  value="' . $pkey . '"  />' . PHP_EOL; 

    $endButtonArray['cancel'] = true;
    $endButtonArray['update'] = "endupdate";
    $this->myPanel->endBar($endButtonArray);
    echo '<br/>' . PHP_EOL;
    echo '</form>' . PHP_EOL; // close the form
    echo '</div>';  
    $this->myPanel->responsiveClose();    
}

private function saveEdit() {
// save the data away
	
	$this->myPanel->xlateBooleans($this->myBooleans);
	
	$this->validator = new FormValidator();  
    $this->validator->addValidation("dialplan","regexp=/^[\+0-9XNZxnz_!#\s\*\.\-]+$/","Dialplan must be a valid Asterisk dialplan");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);	
			  
		$ret = $this->helper->setTuple("cos",$tuple);
		if ($ret == 'OK') {
			$this->message = "Updated COS " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['cosinsert'] = $ret;	
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
