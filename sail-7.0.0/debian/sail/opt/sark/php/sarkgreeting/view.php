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


Class sarkgreeting {
	
	protected $message;
	protected $head = "Greetings"; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $soundir = '/usr/share/asterisk/sarksounds/'; // set for Debian/Ubuntu

public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	
	$this->myPanel->pagename = 'Greeting';

	if (!empty($_FILES['file']['name'])) { 
		$filename = strip_tags($_FILES['file']['name']);
		if (preg_match (' /^(usergreeting\d{4})\.(wav)$/ ', $filename, $matches) ) {
			if (glob($this->soundir . '/' . $_REQUEST['cluster'] . '/' . $matches[1] . '.*')) {
				$this->error_hash['duplicate'] = $matches[1] . " already exists";
			}
			else {
				$sox = "/usr/bin/sox " . $_FILES['file']['tmp_name'] . " -r 8000 -c 1 -e signed /tmp/" . $_FILES['file']['name'] . " -q";
				$rets = `$sox`;
				if (!$rets) {
					$this->helper->request_syscmd ("/bin/mv /tmp/" . $_FILES['file']['name'] . ' ' . $this->soundir . $_REQUEST['cluster']);				
					$this->helper->request_syscmd ("/bin/chown asterisk:asterisk $this->soundir" . $_REQUEST['cluster'] .  "/$filename");
					$this->helper->request_syscmd ("/bin/chmod 664 $this->soundir" . $_REQUEST['cluster'] .  "/$filename");
					$this->message = "File $filename uploaded!";
				}
				else {					
					$this->error_hash['fileconv'] = "Upload file conversion failed! - $rets";				
				}
			}
		}
		else {
			$this->error_hash['format'] = "*ERROR* - File name must be format usergreeting{9999}.wav";
		}					
	}
	
	if (isset($_POST['new']) || isset ($_GET['new'])  ) { 
		$this->showNew();
		return;		
	}	

	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	

	if (isset($_POST['save']) || isset($_POST['endsave'])) {  
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}					
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
	
	$fgreeting = array();
	$tuple = array();	

/* 
 * start page output
 */
  	

	$buttonArray=array();
	$buttonArray['new'] = true;
//	$buttonArray['upimg'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkgreetingForm",false,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

	echo '<form id="sarkgreetingForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	
	$this->myPanel->beginResponsiveTable('greetingtable',' w3-small');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('cluster',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('greetingnum');
	$this->myPanel->aHeaderFor('description'); 	
	$this->myPanel->aHeaderFor('filesize',false,'w3-hide-small w3-hide-medium');	
	$this->myPanel->aHeaderFor('filetype',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('D/L',false,'w3-hide-small');
	$this->myPanel->aHeaderFor('play'); 
	$this->myPanel->aHeaderFor('ed',false,'editcol');
	$this->myPanel->aHeaderFor('del');
	 
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
/*
 * read the greeting files and create an array of cluster=>filename=>filetype
 */ 

	if ( $handle = opendir($this->soundir) ) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry == '.' || $entry == '..') {
				continue;
			}
			if (is_dir($this->soundir . $entry)) {
				if ( $handleSubD = opendir($this->soundir . $entry) ) {
					while (false !== ($fileEntry = readdir($handleSubD))) {	
						if (preg_match(' /(^usergreeting\d*)\.(wav|gsm|mp3)$/ ',$fileEntry,$matches)) {
							$fgreeting[$entry][$matches[1]] = $matches[2];
						}
					}
					closedir($handleSubD);
				}	
			}
		}	
		closedir($handle);
	}

/*
 * If a table row exists without a corresponding sound file then delete it.
 */ 
	$rows = $this->helper->getTable("greeting",NULL,true,false,'cluster');
	foreach ($rows as $row ) {
		$globarray = glob ($this->soundir . $row['cluster'] . '/' . $row['pkey'] . '*');
		if (empty($globarray)) {
			$ret = $this->helper->delTuple("greeting",$row['pkey']);
		}	
	}
/*
 * attempt to insert an entry for each sound file into the database
 */ 
	foreach ($fgreeting as $dkey=>$dir) {
		foreach ($dir as $key=>$value) {
			$tuple['pkey'] = $key;
			$tuple['type'] = $value;
			$tuple['cluster'] = $dkey;
			$this->helper->createTuple("greeting",$tuple,true,true);
		}		 
    }
/*
 * read the table again - it should be consistent with the files now.
 */
  		
	$rows = $this->helper->getTable("greeting",NULL,true,false,'cluster,pkey');	
	
/**** table rows ****/	
	foreach ($rows as $row ) {
		$file = glob( $this->soundir . $row['cluster'] . '/' . $row['pkey'] . '*');
		$filesize = filesize($file[0]);
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 

		preg_match(" /^.*(\d{4}$)/ ", $row['pkey'],$matches);

		echo '<td class="w3-hide-small">' . $row['cluster']  . '</td>' . PHP_EOL;
		echo '<td class="read_only">' . $matches[1] . '</td>' . PHP_EOL;	
				
		echo '<td >' . $row['desc']  . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small w3-hide-medium">' . $filesize. '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $row['type']  . '</td>' . PHP_EOL;
		if (preg_match('/mp3|wav$/',$file[0])) {
			echo '<td class="center w3-hide-small"><a href="/php/downloadg.php?dtype=greet&dfile=' . $file[0] . '"><img src="/sark-common/icons/download.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;
		}
		else {
			echo '<td class="center w3-hide-small">N/A</td>' . PHP_EOL;
		}
									
		if (preg_match('(wav|mp3)',$row['type'])) {
/*
 * HTML5 audio tag doesn't look nice on this page.   
 * ToDo - implement jPlayer or similar
 *
 *			echo '<td><audio controls><source src="/server-moh/moh-' . $pkey . '/' . $row . '"></audio></td>';
 */
			echo '<td ><a href="/server-sounds/' . $row['cluster'] . '/' . $row['pkey'] . '.' .$row['type'] . '"><img src="/sark-common/icons/play.png" border=0 title = "Click to play" ></a></td>' . PHP_EOL; 
		}
		else {
			echo '<td title = "Only  files of type \'wav\' and \'mp3\'  may be played">N/A</td>' . PHP_EOL;
		}

		$get = '?edit=yes&amp;id=' . $row['id']; 
		$this->myPanel->editClick($_SERVER['PHP_SELF'],$get);		

		echo '<td ><a class="table-action-deletelink" href="/php/sarkgreeting/delete.php?id=' . $row['id'] . '&key=' . $row['pkey'] . '&cluster=' . $row['cluster'] . '"><img src="/sark-common/icons/delete.png" border=0 title = "Click to Delete" ></a></td>' . PHP_EOL;				

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
	$this->myPanel->actionBar($buttonArray,"sarkgreetingForm",false,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}

	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("New Greeting");

	echo '<form id="sarkgreetingForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">';

	echo '<div class="cluster">';
	echo '<div class="cluster w3-margin-bottom">';
    $this->myPanel->aLabelFor('cluster','cluster');
    echo '</div>';
	$this->myPanel->selected = 'default';
	$this->myPanel->displayCluster();
	$this->myPanel->aHelpBoxFor('cluster');
	echo '</div>';	


	$endButtonArray['Upload'] = "upimg";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL;
	echo '<input type="file" id="file" name="file" style="display: none;" />'. PHP_EOL;
	echo '<input type="hidden" id="upimgclick" name="upimgclick" />'. PHP_EOL;		
	echo '</form>' . PHP_EOL; // close the form
	echo '</div>';  
    $this->myPanel->responsiveClose();	
}


private function showEdit() {
	
	if (isset ($_GET['id'])) {
		$id = $_GET['id']; 
	}
	
	
	$res = $this->dbh->query("SELECT * FROM greeting WHERE id = '" . $id . "'")->fetch(PDO::FETCH_ASSOC);

	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkgreetingForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup();	
	$this->myPanel->subjectBar($res['pkey']);
	echo '<form id="sarkgreetingForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	
	echo '<div id="clustershow">';
	$this->myPanel->displayInputFor('cluster','text',$res['cluster'],'cluster');
	echo '</div>';

	$this->myPanel->displayInputFor('description','text',$res['desc'],'desc');
	
    $this->myPanel->internalEditBoxStart();
	echo '<div class="w3-margin-bottom">';	
	$this->myPanel->aLabelFor("xref");
	echo '</div>';	

    $xref = $this->xRef(substr($res['pkey'],12));

    $this->myPanel->displayXref($xref);


	echo '</div>' . PHP_EOL;  

	
	echo '<input type="hidden" name="id" value="' . $id . '"  />' . PHP_EOL;

	$endButtonArray = array();
	$endButtonArray['cancel'] = true;
	$endButtonArray['update'] = "endsave";

	$this->myPanel->endBar($endButtonArray);
		

	echo '</form>'; 
	$this->myPanel->responsiveClose();
			
}


private function saveEdit() {
// save the data away
//print_r ($_POST) ;

	$tuple = array();
	
	$this->validator = new FormValidator();

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
		$this->helper->buildTupleArray($_POST,$tuple);
		

/*
 * update the SQL database
 */

		$ret = $this->helper->setTupleById("greeting",$tuple);
			 
		if ($ret == 'OK') {
//			$this->helper->commitOn();	
			$this->message = " Updated ";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['extensave'] = $ret;	
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}

private function xRef($greetnum) {

/*
 * Build Xrefs
 */
	$xref = '';
	$tref = '';
    
	$sql = $this->dbh->prepare("SELECT id,pkey,cluster FROM ivrmenu WHERE greetnum = ?");
	$sql->execute(array($greetnum));		
	$result = $sql->fetchall();	
	foreach ($result as $row) {
        $tref .= "IVR <a href='javascript:window.top.location.href=" . '"/php/sarkivr/main.php?edit=yes&id=' . $row['id'] . '"' . "' >" . $row['pkey'] . ' </a> in tenant ' . 
        	$row['cluster'] . ' references this greeting <br>' . PHP_EOL;
	}
	if ($tref != "") {
    	$xref .= $tref;
    }
    else {
    	$xref .= "No IVR's reference this greeting<br/>" . PHP_EOL;
    } 
    
	$sql = $this->dbh->prepare("SELECT id,pkey,cluster FROM queue WHERE greetnum = ?");
	$sql->execute(array($greetnum));		
	$result = $sql->fetchall();	
	$tref = '';
	foreach ($result as $row) {
         $tref .= "Queue <a href='javascript:window.top.location.href=" . '"/php/sarkqueue/main.php?edit=yes&id=' . $row['id'] . '"' . "' >" . $row['pkey'] . ' </a> in tenant ' . 
         	$row['cluster'] . ' references this greeting <br>' . PHP_EOL;
	}

	if ($tref != "") {
    	$xref .= $tref;
    }
    else {
    	$xref .= "No Queues reference this greeting<br/>" . PHP_EOL;
    }

    return $xref;  		   				
}

}
