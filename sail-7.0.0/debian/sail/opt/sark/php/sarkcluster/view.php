<?php
// sarkcluster class
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


Class sarkcluster {
	
	protected $message; 
	protected $head = "Tenants";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $soundir = '/usr/share/asterisk/'; // set for Debian/Ubuntu
	protected $myBooleans = array(
		'usemohcustom'
	);
	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;

	
	$this->myPanel->pagename = 'Tenants';

	if (!empty($_FILES['file']['name'])) {
		$this->doUpload(); 
		$this->showEdit();
		return;									
	}	

	if (isset($_POST['new']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;		
	}
	if (isset($_POST['save']) || isset($_POST['endsave']) ) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}					
	}

	if (isset($_POST['edit']) || isset ($_GET['edit'])  ) { 
		$this->showEdit();
		return;		
	}
	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}					
	}

	if (isset($_REQUEST['delete'])  ) { 
		$pkey = $_REQUEST['pkey'];
		$this->helper->delTuple("cluster",$pkey);
		$ret = $this->helper->request_syscmd ("rm -rf " . $this->soundir . $_REQUEST['pkey']);
		$this->message = "Deleted " . $pkey;		
	}

	if (isset($_REQUEST['mohdelete'])  ) { 
		$file = $_REQUEST['file'];
		$dir = $_REQUEST['dir'];
		$ret = $this->helper->request_syscmd ("rm -rf /usr/share/asterisk/$dir/$file");
		$this->message = "Deleted " . $file . " from " . $dir;	
		$this->showEdit();
		return;	
	}	


	if (isset($_REQUEST['commit'])) { 
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
	  
	$this->myPanel->showErrors($this->error_hash);
	
	$buttonArray['new'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkclusterForm",false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup();

	echo '<form id="sarkclusterForm" action="' . $_SERVER['PHP_SELF'] . '"  enctype="multipart/form-data">' . PHP_EOL;

	$this->myPanel->beginResponsiveTable('clustertable',' w3-tiny');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('tenantname');
	$this->myPanel->aHeaderFor('clusterid');
	$this->myPanel->aHeaderFor('tenantoperator');
//	$this->myPanel->aHeaderFor('include',false);  
    $this->myPanel->aHeaderFor('clusterclid');      	
	$this->myPanel->aHeaderFor('ato');	
	$this->myPanel->aHeaderFor('chanmax');
	$this->myPanel->aHeaderFor('masterclose');
	$this->myPanel->aHeaderFor('oclo');
	$this->myPanel->aHeaderFor('ed');
	$this->myPanel->aHeaderFor('del');
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	$rows = $this->helper->getTable("cluster");
	foreach ($rows as $row ) {
		$hintKey = $row['pkey'];
		$ret = $this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'database get CustomDevstate " . $hintKey . "'"); 
		$masterclose = "AUTO";
		if (preg_match("/Value: INUSE/",$ret)) {
			$masterclose = "CLOSED";
		}

		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td >' . $row['pkey'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['id'] . '</td>' . PHP_EOL;
/*
		ToDo - fix operator endpoint ID - there is no routecode so we need 
		something to figure out what we have (extension or callgroup).
*/
		echo '<td >' . $row['operator'] . '</td>' . PHP_EOL;
//		echo '<td >' . $row['include'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['clusterclid'] . '</td>' . PHP_EOL;	

		echo '<td >' . $row['abstimeout'] . '</td>' . PHP_EOL;		
		echo '<td >' . $row['chanmax'] . '</td>' . PHP_EOL;
		echo '<td >' . $masterclose . '</td>' . PHP_EOL;
		echo '<td >' . $row['oclo'] . '</td>' . PHP_EOL;
		$get = '?edit=yes&amp;pkey=';
		$get .= $row['pkey'];	
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);
		if ($row['pkey'] == 'default') {
			echo '<td class="center">N/A</td>' . PHP_EOL;
		}
		else {
			$sql = $this->dbh->prepare("select count(*) from ipphone where cluster=?");
			$sql->execute(array($row['pkey']));
			$numexts = $sql->fetchColumn();
			if ($numexts) {
				echo '<td class="center">N/A</td>' . PHP_EOL;
			}
			else {
				$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$row['pkey']);
			}
		}	
		
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
	$this->myPanel->actionBar($buttonArray,"sarkclusterForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}

	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Tenant");

	echo '<form id="sarkclusterForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';

	$this->myPanel->displayInputFor('tenantname','text',null,'pkey');
//	$this->myPanel->aLabelFor('tenantname');
//	echo '<input type="text" name="pkey" id="pkey"  rel="1" />' . PHP_EOL;

//	$this->myPanel->displayInputFor('include','text');
//	$this->myPanel->aLabelFor('include');
//	echo '<input type="text" name="include" id="include"  rel="2" />' . PHP_EOL;

	echo '</div>';

	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL;
	echo '</form>' . PHP_EOL; // close the form
	echo '</div>';  
    $this->myPanel->responsiveClose();	
}

private function saveNew() {
// save the data away	
	$tuple = array();
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("pkey","req","Please fill in Tenant name");
    $this->validator->addValidation("localarea","num","Local Area Code must be numeric"); 
    $this->validator->addValidation("localdplan","regexp=/^[_0-9XNZxnz!#\s\*\.\-\[\]]+$/","Local Dialplan must be a valid Asterisk dialplan");
    $this->validator->addValidation("abstimeout","num","Absolute Timeout must be numeric");
    $this->validator->addValidation("chanmax","num","Channels must be numeric");

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
				
		$tuple['pkey']		=  strip_tags($_POST['pkey']);
//		$tuple['include'] 	=  strip_tags($_POST['include']);
		
		$ret = $this->helper->createTuple("cluster",$tuple);
		if ($ret == 'OK') {
			$this->helper->request_syscmd ("mkdir $this->soundir" . $tuple['pkey']);
			$this->helper->request_syscmd ("chown asterisk:asterisk $this->soundir" . $tuple['pkey']);
			$this->message = "Saved new Tenant " . $tuple['pkey'] . "!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['clusterinsert'] = $ret;	
		}
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function showEdit($pkey=false) {
	

	if (!$pkey) {
		if (isset ($_REQUEST['pkey'])) {
			$pkey = $_REQUEST['pkey']; 
		}
	}

	$hintKey = $pkey;

	$ret = $this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'database get CustomDevstate " . $hintKey . "'"); 
	$masterclose = "AUTO";
	if (preg_match("/Value: INUSE/",$ret)) {
		$masterclose = "CLOSED";
	}

	$res = $this->dbh->query("SELECT * FROM cluster WHERE pkey = '" . $pkey . "'")->fetch(PDO::FETCH_ASSOC);
	
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkclusterForm",false,false,false);
	
	$this->myPanel->showErrors($this->error_hash);
	
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Edit Tenant " . $pkey);

	echo '<form id="sarkclusterForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	

	echo '<div class="w3-margin-bottom">';
	$this->myPanel->aLabelFor('clustersysop');
	echo '</div>'; 
	$this->myPanel->selected = $res['operator'];
	$this->myPanel->sysSelect('operator',false,true,false,$res['pkey']) . PHP_EOL;
	$this->myPanel->aHelpBoxFor('clustersysop');

//	$this->myPanel->displayInputFor('include','text',$res['include']);
	$this->myPanel->displayInputFor('clusterclid','text',$res['clusterclid']);
	$this->myPanel->displayInputFor('localarea','text',$res['localarea']);
	$this->myPanel->displayInputFor('localdplan','text',$res['localdplan']);
	$this->myPanel->displayInputFor('abstimeout','number',$res['abstimeout']);
	$this->myPanel->displayInputFor('chanmax','number',$res['chanmax']);
	$this->myPanel->radioSlide('masterclose',$masterclose,array('AUTO','CLOSED'));
//	$this->myPanel->displayInputFor('oclo','text',$res['oclo'],null,null,true);
//	
	$mohlist = array();
	if (file_exists("/usr/share/asterisk/moh-" . $pkey)) {
		if ($handle = opendir("/usr/share/asterisk/moh-" . $pkey)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry == '.' || $entry == '..') {
					continue;
				}
				$mohlist[] = $entry;
			}
		closedir($handle);		
		}	
	}


	
	echo '<div class="w3-margin-top w3-margin-bottom">';
	$this->myPanel->aLabelFor("mohhead");
	$this->myPanel->aHelpBoxFor("mohhead");
	echo '</div>';
	echo '<span class="w3-button w3-blue w3-small w3-round-xxlarge w3-right" style="cursor:pointer" name="newmoh">Upload MOH</span>';	
	echo '<br/><br/><br/>' . PHP_EOL;

	if (!empty($mohlist)) {
		$this->myPanel->beginResponsiveTable('clustertable');
		echo '<thead>' . PHP_EOL;	
		echo '<tr>' . PHP_EOL;
		$this->myPanel->aHeaderFor('mohfilename');
		$this->myPanel->aHeaderFor('play'); 
		$this->myPanel->aHeaderFor('del');
		echo '</tr>' . PHP_EOL;
		echo '</thead>' . PHP_EOL;
		echo '<tbody>' . PHP_EOL;
		foreach ($mohlist as $row ) {
			echo '<tr id="' . $row . '">'. PHP_EOL; 
			echo '<td >' . $row . '</td>' . PHP_EOL;
 			echo '<td><audio controls><source src="/server-moh/moh-' . $pkey . '/' . $row . '"></audio></td>'; 
//			echo '<td ><a href="/server-moh/moh-' . $pkey . '/' . $row . '"><img src="/sark-common/icons/play.png" border=0 title = "Click to play" ></a></td>';
			echo '<td><a href="'; 
			echo $_SERVER['PHP_SELF'];
			echo '?mohdelete=yes&amp;pkey=';
			echo $pkey;
			echo '&amp;dir=';
			echo 'moh-' . $pkey;
			echo '&amp;file=';
			echo $row;			
			echo '"><img src="/sark-common/icons/delete.png" alt = "Click to Delete" title = "Click to Delete"';
        	echo ' onclick = "return confirmOK(\'Confirm Delete - Are you sure?\')"></a></td>';
//			$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$row);	
			echo '</td>' . PHP_EOL;
			echo '</tr>'. PHP_EOL;
		}
		echo '</tbody>' . PHP_EOL;
		$this->myPanel->endResponsiveTable();
		echo '<br/><br/>' . PHP_EOL;
		$this->myPanel->displayBooleanFor('usemohcustom',$res['usemohcustom']);
	}
	else {
		echo 'No soundfiles loaded for this tenant.  Defaults will be used.<br/><br/>';
	}
	echo '<input type="file" id="file" name="file" style="display: none;" />'. PHP_EOL;
	echo '<input type="hidden" id="newmohclick" name="newmohclick" />'. PHP_EOL;
	echo '<br/><br/>' . PHP_EOL;
	
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


	$tuple = array();
	
	$this->myPanel->xlateBooleans($this->myBooleans);
		
	$this->validator = new FormValidator();
	$this->validator->addValidation("pkey","req","Please fill in Tenant name");
    $this->validator->addValidation("localarea","num","Local Area Code must be numeric"); 
    $this->validator->addValidation("clusterclid","num","CLID must be numeric");
    $this->validator->addValidation("localdplan","regexp=/^[_0-9XNZxnz!#\s\*\.\-\[\]]+$/","Local Dialplan must be a valid Asterisk dialplan");
    $this->validator->addValidation("abstimeout","num","Absolute Timeout must be numeric");
    $this->validator->addValidation("chanmax","num","Channels must be numeric");
    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$custom = array (
			'masterclose' => True,
			'newmohclick' => True
        );

		$this->helper->buildTupleArray($_POST,$tuple,$custom);
		
//		$tuple[$key] = preg_replace ( "/\\\/", '', $tuple[$key]);

/*
 * update the SQL database
 */
		$ret = $this->helper->setTuple("cluster",$tuple);

		if (isset($_POST['masterclose'] )) {
			$hint = 'NOT_INUSE'; 
			if ($_POST['masterclose'] == 'CLOSED') {
				$hint = 'INUSE';
			}
			$hintKey = $_POST['pkey'];	
/*
			if ($_POST['pkey'] == 'default') {
				$hintKey = 'MASTER';
			}	
*/
			$set = $this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'devstate change Custom:" . $hintKey . " $hint' ");  
			$set = $this->helper->request_syscmd ("/usr/sbin/asterisk -rx 'database put " . $_POST['pkey'] . " OCSTAT " . $_POST['masterclose'] . "'");   

 		}			 
		
//		$ret = $this->helper->setTuple("ivrmenu",$tuple);
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = " Updated " . $_POST['pkey'];
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['clustsave'] = $ret;	
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    unset ($this->validator);
}

private function doUpload() {

		if ($_FILES['file']['error']) {
			$this->error_hash['Upload'] = "Upload failed.  Check PHP max filesize.";
			return -1;
		}

		$filename = strip_tags($_FILES['file']['name']);
		if (!preg_match (' /wav$/ ', $filename) ) {
			$this->error_hash['Format'] = "Upload file MUST be format wav";
			return -1;
		}
		$sox = "/usr/bin/sox " . $_FILES['file']['tmp_name'] . " -r 8000 -c 1 -e signed /tmp/" . $_FILES['file']['name'] . " -q";
		$rets = `$sox`;
		if ($rets) {
			$this->error_hash['fileconv'] = "Upload file conversion failed! - $rets";
			return -1;			
		}

		$dir='moh-' . $_POST['pkey'];
		$tfile = $_FILES['file']['tmp_name'];
		$this->helper->request_syscmd ("/bin/mv /tmp/" . $_FILES['file']['name'] . ' ' . $this->soundir . $dir);
		$this->message = "File $filename uploaded!";
		
}


}
