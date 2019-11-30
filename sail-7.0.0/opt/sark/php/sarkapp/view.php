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



Class sarkapp {
	
	protected $message; 
	protected $head = "Apps";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $myBooleans = array(
		'striptags'		
	);

public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	
	if (isset($_POST['new']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;
	}
	
	if (isset($_POST['save']) || isset($_POST['endsave'])) {
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew(strip_tags($_POST['pkey']));
			return;
		}					
	}		

	if (isset($_POST['update']) || isset($_POST['endupdate'])) {  
		$this->saveEdit();
		$this->showEdit();
		return;			
	}	
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}

	if (isset($_GET['delete'])) { 
		$this->rowDelete($_GET['pkey']);	
	}			

	if (isset($_POST['commit'])) { 
		$this->helper->sysCommit();
		$this->message = "Committed";	
	}
	
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkappForm",false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);	
//	$this->myPanel->subjectBar("Custom Apps");

	echo '<form id="sarkappForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->beginResponsiveTable('apptable',' w3-small');	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('del',false,'delcol');
	$this->myPanel->aHeaderFor('context');
	$this->myPanel->aHeaderFor('cluster',false,'w3-hide-small'); 
	$this->myPanel->aHeaderFor('description',false,'w3-hide-small'); 	
	$this->myPanel->aHeaderFor('appspan',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('ed',false,'editcol');
		
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("appl",null,false);
	foreach ($rows as $row ) { 
				
		echo '<tr name="linekey" id="' . $row['pkey'] . '">'. PHP_EOL;
		$get = '?delete=yes&amp;pkey='. $row['pkey'];
		$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$get);		
		echo '</td>' . PHP_EOL;				
		echo '<td class="read_only">' . $row['pkey'] . '</td>' . PHP_EOL;			
		echo '<td class="w3-hide-small">' . $row['cluster']  . '</td>' . PHP_EOL;		 
		echo '<td class="w3-hide-small">' . $row['desc']  . '</td>' . PHP_EOL;	
		echo '<td class="w3-hide-small">' . $row['span']  . '</td>' . PHP_EOL;	
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		echo '</tr>'. PHP_EOL;				
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();
		
}

private function showEdit() {

	$tuple=array();
	if (isset($this->keychange)) {
		$pkey = $this->keychange;		
	}
	else {	
		$pkey = $_REQUEST['pkey'];
	}

	$buttonArray['cancel'] = true;

	$this->myPanel->actionBar($buttonArray,"sarkappForm",false,true,true);
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
//	$this->myPanel->subjectBar($pkey);

	echo '<form id="sarkappForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
		
	$app = $this->dbh->query("SELECT * FROM appl WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);

	$printline = "APP " . $pkey;
	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 

	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}

    if (!isset($app['striptags'])) {
    	$app['striptags'] = 'YES';
    }

    $this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Settings - App " . $pkey);
	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $app['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';
	
	$this->myPanel->displayInputFor('context','text',$pkey);
	$this->myPanel->displayInputFor('description','text',$app['desc'],"desc");
	$this->myPanel->radioSlide('span',$app['span'],array('Internal','External','Both','Neither'));		
//    $this->myPanel->displayBooleanFor('striptags',$app['striptags']);

	echo '</div>' . PHP_EOL;



//    $this->myPanel->responsiveTwoColRight();

    $this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Code");
    $this->myPanel->displayFile($app['extcode'],"extcode");
    echo '</div>';

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Xref");
	$xref = $this->helper->xRef($pkey,"app");
    $this->myPanel->aLabelFor('xref');
    $this->myPanel->displayXref($xref);
    echo '</div>';    
   	
	
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endupdate";	
	$this->myPanel->endBar($endButtonArray);   	
   	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL; 
   	echo '</form>';
   	$this->myPanel->responsiveClose();
			
}


private function saveEdit() {
// save the data away

	$tuple = array();
	$this->myPanel->xlateBooleans($this->myBooleans);

	$this->validator = new FormValidator();
	$this->validator->addValidation("context","regexp=/^[0-9a-zA-Z_-]+$/","Context name is invalid - must be [0-9a-zA-Z_-]"); 
    //Now, validate the form
    
    if ($this->validator->ValidateForm()) {
		$custom = array ('context' => True);	
			
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
/*
 * update the SQL database
 */
		
		$newkey =  trim(strip_tags($_POST['context']));
/*
 * check for keychange
 */
		if ($newkey != $tuple['pkey']) {
			$sql = $this->dbh->prepare("SELECT pkey FROM appl WHERE pkey=?");
			$sql->execute(array($newkey));
			$res = $sql->fetch();	
			if ( isset($res['pkey']) ) { 
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['extensave'] = " Attempt to change App name but " . $newkey . " already exists!";	
			}
			else {
				// signal a key change to the editor
				$this->keychange = $newkey;
				// set the mailbox to the new extension
				$ret = $this->helper->setTuple("appl",$tuple,$newkey);
				if ($ret == 'OK') {
					$this->message = " Updated! ";
				}
				else {
					$this->invalidForm = True;
					$this->message = "<B>  --  Validation Errors!</B>";	
					$this->error_hash['extensave'] = $ret;	
				}
			}
		}
		else {
			$ret = $this->helper->setTuple("appl",$tuple,$newkey);
			if ($ret == 'OK') {
				$this->message = " Updated! ";
			}
			else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";	
				$this->error_hash['extensave'] = $ret;	
			}
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function showNew() {
	$tuple=array();

	$pkey = 'NewApp' . rand(1000, 9999);
	$tuple['pkey'] 	= $pkey;

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkappForm",true,false);

	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}

	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);
	$this->myPanel->internalEditBoxStart();	
	$this->myPanel->subjectBar("New App");

	echo '<form id="sarkappForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;	

   	$this->myPanel->displayInputFor('context','text',$pkey);
   	
   	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = $app['cluster'];
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';
   
   	$this->myPanel->displayInputFor('description','text',$app['desc'],"desc");
	$this->myPanel->radioSlide('span',$app['span'],array('Internal','External','Both','Neither'));		
//    $this->myPanel->displayBooleanFor('striptags','YES');
    $this->myPanel->subjectBar("Code");
    $this->myPanel->displayFile($app['extcode'],"extcode");
    echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;
    echo '</div>';
	$this->myPanel->responsiveClose();	
	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
     
	echo '</form>';
   			
}


private function saveNew() {
// save the data away
	$tuple = array();
	$this->myPanel->xlateBooleans($this->myBooleans);
	if (isset($_POST['context'])) {
		$_POST['pkey'] = $_POST['context'];
	}
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in App name");
    
    //Now, validate the form
    if ($this->validator->ValidateForm()) {

/*
 * 	call the tuple builder to create a table row array 
 */  
		$this->helper->buildTupleArray($_POST,$tuple);			   
		$ret = $this->helper->createTuple("appl",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = "Saved new App " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['ivrinsert'] = $ret;	
		}		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
	
}

private function rowDelete($pkey) {
	$this->helper->delTuple("appl",$pkey); 
	$this->message = "Deleted " . $pkey . "!";
}

}
