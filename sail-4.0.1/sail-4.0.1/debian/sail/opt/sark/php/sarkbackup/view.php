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
require_once "../formvalidator.php";


Class backup {
	
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
//	print_r ($_GET);	
	echo '<body>';
	echo '<form id="sarkbackupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Backup/Snapshot';

	if (isset($_GET['regress'])) {
		if ($_GET['regress'] == 'yes') {
			if  (isset($_GET['dfile'])) { 
				$rfile = strip_tags($_GET['dfile']);
				$this->helper->request_syscmd ("/bin/cp /opt/sark/snap/$rfile /opt/sark/db/sark.db");
				$this->helper->commitOn (); 
				$this->message = "Database regressed to $rfile";
			}	
		}
	}

	if (isset($_POST['spin_x'])) { 
		$rc = $this->helper->request_syscmd ("/bin/sh /opt/sark/scripts/spin.sh");
		$this->message = "Backup taken!";	
	}
	
	if (isset($_POST['upload_x'])) { 
		$filename = strip_tags($_FILES['file']['name']);
		if (! preg_match (' /^sark\.db\.\d{10}$/ ', $filename)) {
			$this->message = "Filename is incorrect - ignored!";
		}
		else if (file_exists ("/opt/sark/snap/$filename")) {
			$this->message = "File already exists in snapshots";
		}
		else {	
			$tfile = strip_tags($_FILES['file']['tmp_name']);
			$ret = `/usr/bin/sqlite3 $tfile "pragma integrity_check;"`;
			if (! preg_match (' /ok/ ', $ret)) {
				$this->message = "File is not an sqlite3 database - ignored!";
			}
			else {
				$this->helper->request_syscmd ("/bin/mv $tfile /opt/sark/snap/$filename");
				$this->message = "File uploaded to snapshots!";
			}
		}
	}	

	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
		$this->helper->sysCommit();
		$this->message = "Updates have been Committed";	
	}	
	
	$this->showMain();
	$this->message = "";
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
/*
 * read the backup files and create 2 arrays 
 */ 
	$bkup = array();
	$snap = array();
	if ($handle = opendir('/opt/sark/bkup')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..') {
				if (preg_match (' /^sarkbak\.\d+\.zip$/ ', $entry)) {
					array_push($bkup, $entry);
				}
			}
		}	
		closedir($handle);
	}
	if ($handle = opendir('/opt/sark/snap')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..') {
				array_push($snap, $entry);
			}
		}	
		closedir($handle);
	}
/* 
 * start page output
 */

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	
	echo '<div class="buttons">';	
	$this->myPanel->Button("spin");
	$this->myPanel->Button("upload");
	$this->myPanel->commitButton();
	echo '</div>' . PHP_EOL;
  	
	$this->myPanel->Heading();

	echo '<div class="datadivtabedit">';
	
	echo '<div id="pagetabs" >' . PHP_EOL;
    echo '<ul>'.  PHP_EOL;
    echo '<li><a href="#backs">Backups</a></li>'. PHP_EOL;
    echo '<li><a href="#snaps">Snapshots</a></li>'.  PHP_EOL;
    echo '<li><a href="#uplds">Upload</a></li>'.  PHP_EOL;
    
    echo '</ul>'. PHP_EOL;
	
	echo '<div id="backs" >'. PHP_EOL;
	
	echo '<table class=display id="bkuptable"  >' ;	


	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('size');	
	$this->myPanel->aHeaderFor('astfiledate');	
	$this->myPanel->aHeaderFor('dl');	
	$this->myPanel->aHeaderFor('del');
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
/**** table rows ****/	
	foreach ($bkup as $file ) {
		
		preg_match( '/\.(\d+).zip$/',$file,$matches);		
		$rdate = date('D d M H:i:s Y', $matches[1]);
		$fsize = filesize("/opt/sark/bkup/".$file);
		
		echo '<tr>'. PHP_EOL; 
		echo '<td >' . $file . '</td>' . PHP_EOL;		
		echo '<td >' . $fsize . '</td>' . PHP_EOL;
		echo '<td >' . $rdate . '</td>' . PHP_EOL;
		echo '<td ><a href="/php/download.php?dtype=bkup&dfile=' . $file . '"><img src="/sark-common/actions/down.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;									
		echo '<td ><a class="table-action-deletelink" href="/php/sarkbackup/delete.php?id=/opt/sark/bkup/' . $file . '"><img src="/sark-common/edittrash.png" border=0 title = "Click to Delete" ></a></td>' . PHP_EOL;							
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
/*
 *      TAB DIVEND
 */
    echo '</div>'. PHP_EOL;
    
   	echo '<div id="snaps" >'. PHP_EOL;
   	
 	echo '<table class=display id="snaptable"  >' ;	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>'. PHP_EOL; 
	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('size');		
	$this->myPanel->aHeaderFor('astfiledate');	
	$this->myPanel->aHeaderFor('dl');
	$this->myPanel->aHeaderFor('regress');    		
	$this->myPanel->aHeaderFor('del');	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
/**** table rows ****/	
	foreach ($snap as $file ) {
		
		preg_match( '/\.(\d+)$/',$file,$matches);		
		$rdate = date('D d M H:i:s Y', $matches[1]);
		$fsize = filesize("/opt/sark/snap/".$file);
		
		echo '<tr>' . PHP_EOL; 
		echo '<td>' . $file . '</td>' . PHP_EOL;		
		echo '<td>' . $fsize . '</td>' . PHP_EOL;
		echo '<td>' . $rdate . '</td>' . PHP_EOL;
		echo '<td ><a href="/php/download.php?dtype=snap&dfile=' . $file . '"><img src="/sark-common/actions/down.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;									
		echo '<td ><a href="/php/sarkbackup/main.php?regress=yes&dfile=' . $file . '"><img src="/sark-common/actions/back.png" border=0 title = "Click to Regress" onclick="return confirmOK(\'Regress to this Snapshot - Confirm?\')"></a></td>' . PHP_EOL;	
		echo '<td ><a class="table-action-deletelink" href="/php/sarkbackup/delete.php?id=/opt/sark/snap/' . $file . '"><img src="/sark-common/edittrash.png" border=0 title = "Click to Delete" ></a></td>' . PHP_EOL;							
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;  	
   	
/*
 *      TAB DIVEND
 */
    echo '</div>' . PHP_EOL;	
	echo '<div id="uplds" >' . PHP_EOL;
	echo '<br />' . PHP_EOL;
	echo '<h3>select your candidate file and then click the upload button (above middle right)...</h3><br/><br/>' . PHP_EOL;
	echo '<label for="file">&nbsp</label>' . PHP_EOL;
	echo '<input type="file" name="file" id="file" />' . PHP_EOL;
	echo '<br />' . PHP_EOL;

/*
 *      TAB DIVEND
 */    
#
#  end of TABS DIV
#
    echo '</div>' . PHP_EOL;   
    
    echo '</div>'; 	
}

}
