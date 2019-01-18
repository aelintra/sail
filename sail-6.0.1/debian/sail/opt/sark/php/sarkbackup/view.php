<?php
//
// Developed by CoCo
// Copyright (C) 2012-2015 CoCoSoft
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


Class sarkbackup {
	
	protected $message; 
	protected $head = "Backup/Restore";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $log = NULL;
	protected $invalidForm;
	protected $error_hash = array();
	protected $reboot;
	protected $viewstate;
	protected $myBooleans = array(
		'resetdb',
		'resetasterisk',
		'resetvmail',
		'resetusergreets',
		'resetldap'
	);
	
public function showForm() {
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;

//	echo '<form id="sarkbackupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Backup/Restore';

	if (isset($_POST['restore'])) {
		if (!empty( $_POST['password'] )) {
			if ($this->helper->checkCreds( "admin",$_POST['password'],$this->message,false )) {
				$this->doRestore();			
				$this->message = "Restore complete";
				return;
//				$this->helper->request_syscmd ("reboot");
//				$this->reboot=true;
			}
			else {
				$this->message = "Password incorrect - enter the admin password to do the restore";
				$this->showRestore($_POST['rfile']);
				return;
			}		
		}
		else {
			$this->message = "No Password entered - enter the admin password to do the restore";
			$this->showRestore($_POST['rfile']);
		}
	}
	
	
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
	
	if (isset($_GET['regressbk'])) {
		if  (isset($_GET['dfile'])) { 
			$rfile = strip_tags($_GET['dfile']);
			$this->showRestore($rfile);
			return;
		}	
	}	

	if (isset($_POST['endbkup'])) { 
		$rc = $this->helper->request_syscmd ("/bin/sh /opt/sark/scripts/spin.sh");
		$this->message = "Backup taken!";	
	}

	if (isset($_POST['endsnap'])) { 
		$rc = $this->helper->request_syscmd ("/bin/sh /opt/sark/scripts/snap.sh");
		$this->message = "Snapshot taken!";
		$this->viewstate=true;	
	}
	
	if (!empty($_FILES['file']['name'])) { 
		$dir=NULL;
		$filename = strip_tags($_FILES['file']['name']);
		if (preg_match (' /^sark\.db\.\d{10}$/ ', $filename) ) {
			if (file_exists ("/opt/sark/snap/$filename")) {
				$this->message = "File already exists in snapshots";
			}
			else {
				$dir='snap';
			}
		}
		if (preg_match (' /^sarkbak\.\d{10}\.zip$/ ', $filename) ) {
			if (file_exists ("/opt/sark/bkup/$filename")) {
				$this->message = "File already exists in backups";
			}
			else {
				$dir='bkup';
			}
		}
		if ($dir) {
			$tfile = strip_tags($_FILES['file']['tmp_name']);
			if ($dir == 'snap') {						
				$ret = `/usr/bin/sqlite3 $tfile "pragma integrity_check;"`;
				if (! preg_match (' /ok/ ', $ret)) {
					$this->message = "Snap file is not an sqlite3 database - ignored!";
				}
			}
			if (!$this->message) {
				$this->helper->request_syscmd ("/bin/mv $tfile /opt/sark/$dir/$filename");
				$this->message = "File $filename uploaded!";
			}
		}
		else {
			if (!$this->message) {
				$this->message = "Filename is incorrect - ignored!";
			}
		}						
	}

	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
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
		rsort($bkup);
	}
	if ($handle = opendir('/opt/sark/snap')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..') {
				array_push($snap, $entry);
			}
		}
		closedir($handle);
		rsort($snap);
	}
/* 
 * start page output
 */

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
/*	
	echo '<div class="buttons">';	
	$this->myPanel->Button("spin");
	echo '<img src="/sark-common/buttons/upload.png" id="upimg" alt="upload" title="Upload a saved snapshot or backup" />'. PHP_EOL;	
	$this->myPanel->commitButton();
	echo '</div>' . PHP_EOL;
*/

	$buttonArray = array();
	$buttonArray['upimg'] = true;
	$buttonArray['snap'] = true;
	$buttonArray['spin'] = true;
	
	$this->myPanel->actionBar($buttonArray,"sarkbackupForm",false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);

	echo '<form id="sarkbackupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;

	if ($this->viewstate) {
		echo '<div class="bkupsnap" id="backups" style="display:none">';
	}
	else {
		echo '<div class="bkupsnap" id="backups">';
	}
	
	$this->myPanel->aLabelFor("Saved Backups");
	echo '<div class="w3-padding w3-margin-bottom ">';
	$this->myPanel->beginResponsiveTable('bkuptable',' w3-tiny');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('size',false,"w3-hide-small");	
	$this->myPanel->aHeaderFor('astfiledate',false,"w3-hide-small");	
	$this->myPanel->aHeaderFor('D/L',false,"w3-hide-small");
	$this->myPanel->aHeaderFor('restore');    		
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
		echo '<td class="w3-hide-small">' . $fsize . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small" >' . $rdate . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small"><a href="/php/download.php?dtype=bkup&dfile=' . $file . '"><img src="/sark-common/icons/download.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;
		echo '<td class="icons"><a href="/php/sarkbackup/main.php?regressbk=yes&dfile=' . $file . '" ><img src="/sark-common/icons/undo.png" border=0 title = "Click to Restore" )"></a></td>' . PHP_EOL;	
		echo '<td class="icons"><a class="table-action-deletelink" href="delete.php?id=/opt/sark/bkup/' . $file . '"><img src="/sark-common/icons/delete.png" border=0 title = "Click to Delete" ></a></td>' . PHP_EOL;							
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
//	echo '</table>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</div>';	
	$endButtonArray['Take a Backup'] = "endbkup";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL;
	echo '</div>'; 
/*
 *      TAB DIVEND
 */
	
	if ($this->viewstate) {
		echo '<div class="bkupsnap" id="snapshots">';
	}
	else {
		echo '<div class="bkupsnap" id="snapshots" style="display:none">';
	}

	$this->myPanel->aLabelFor("Saved Snapshots");

	echo '<div class="w3-padding w3-margin-bottom ">';
	$this->myPanel->beginResponsiveTable('snaptable',' w3-tiny');
	echo '<thead>' . PHP_EOL;	
	echo '<tr>'. PHP_EOL; 
	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('size',false,"w3-hide-small");		
	$this->myPanel->aHeaderFor('astfiledate',false,"w3-hide-small");	
	$this->myPanel->aHeaderFor('D/L',false,"w3-hide-small");
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
		echo '<td class="w3-hide-small">' . $fsize . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $rdate . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small"><a href="/php/download.php?dtype=snap&dfile=' . $file . '"><img src="/sark-common/icons/download.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;
		echo '<td class="icons"><a href="/php/sarkbackup/main.php?regress=yes&dfile=' . $file . '"><img src="/sark-common/icons/undo.png" border=0 title = "Click to Regress" onclick="return confirmOK(\'Regress to this Snapshot - Confirm?\')"></a></td>' . PHP_EOL;	
		echo '<td class="icons"><a class="table-action-deletelink" href="delete.php?id=/opt/sark/snap/' . $file . '"><img src="/sark-common/icons/delete.png" border=0 title = "Click to Delete" ></a></td>' . PHP_EOL;							
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</div>';
	
	$snapButtonArray['Take a Snapshot'] = "endsnap";
	$this->myPanel->endBar($snapButtonArray);
	echo '<br/>' . PHP_EOL; 
	echo '</div>';
	echo '<input type="file" id="file" name="file" style="display: none;" />'. PHP_EOL;
	echo '<input type="hidden" id="upimgclick" name="upimgclick" />'. PHP_EOL;
	echo '</form>';
	$this->myPanel->responsiveClose();
   	
/*
 *      TAB DIVEND
 */

 //   echo '</div>' . PHP_EOL;   
#
#  end of TABS DIV
#        
//    echo '</div>';	

}

private function showRestore($rfile) {

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 

	$buttonArray['cancel'] = true;

	$this->myPanel->actionBar($buttonArray,"sarkbackupForm",false,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);
	echo '<form id="sarkbackupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;

	$this->myPanel->internalEditBoxStart("w3-white");
	$this->printRestoreNotes ($rfile);
	echo '</div>';
	
	if ($this->reboot) {
		echo '<div class="messagebox" >';
		echo '<div class="message" style="font-size: 2em;padding-left:10em;padding-top:2em;">';
		echo $this->log;
		echo '</div>';
		echo '</div>';
		return;
	}
	
	echo '<input id="rfile" type="hidden" name="rfile" value="' . $rfile . '" >'. PHP_EOL;		

    $this->myPanel->internalEditBoxStart();

//	echo '<div id="reset" >'. PHP_EOL;
	
//	echo '<h2>'. PHP_EOL;
	$this->myPanel->displayBooleanFor('resetdb','');
//	echo '<input id="resetdb" type="checkbox" name="resetdb" checked="checked" >'. PHP_EOL;
//	echo ' :Restore Customer Database?';
//	echo '<br/>';
	$this->myPanel->displayBooleanFor('resetasterisk','');
//	echo '<input id="asterisk" type="checkbox" name="asterisk" checked="checked" >'. PHP_EOL;
//	echo ' :Restore Asterisk files?';				
//	echo '<br/>';
	$this->myPanel->displayBooleanFor('resetvmail','');
//	echo '<input id="vmail" type="checkbox" name="vmail" checked="checked" >'. PHP_EOL;
//	echo ' :Restore Voicemail?';				
//	echo '<br/>';
	$this->myPanel->displayBooleanFor('resetusergreets','');
//	echo '<input id="usergreets" type="checkbox" name="usergreets" checked="checked" >'. PHP_EOL;
//	echo ' :Restore greetings?';				
//	echo '<br/>';
	$this->myPanel->displayBooleanFor('resetldap','');
//	echo '<input id="ldap" type="checkbox" name="ldap" checked="checked" >'. PHP_EOL;
//	echo ' :Restore LDAP Directory?';				
//	echo '<br/>';																														
//	echo '</h2>'. PHP_EOL;

    echo '</div>' . PHP_EOL;
    
    echo '<div class="w3-container" id="container">' . PHP_EOL;
    echo '<input type="password" id="password" name="password" placeholder="Admin Password"> ' . PHP_EOL;       
    echo '</div>';

 	$endButtonArray['Restore'] = "restore";
	$this->myPanel->endBar($endButtonArray);
	echo '<br/>' . PHP_EOL; 
	echo '</div>';
	echo '</form>';
    $this->myPanel->responsiveClose();

}

private function doRestore() {
	
/* 
 * Unzip the backup file
 */
	if (empty($_POST['rfile'])) {
		$this->message = "Oops!, lost filename";
		return;
	}

	$this->myPanel->xlateBooleans($this->myBooleans);

	$rfile =  strip_tags($_POST['rfile']);

/* 
 * start page output
 */

	$this->myPanel->pagename = 'Restore ' . $rfile;
	$buttonArray['cancel'] = true;

	$this->myPanel->actionBar($buttonArray,"sarkbackupForm",false,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);
	echo '<form id="sarkbackupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	
	$this->printRestoreNotes ($rfile);
	
	
	$tempDname = "/tmp/bkup" . time();
	`mkdir $tempDname`;
	`unzip /opt/sark/bkup/$rfile -d $tempDname`;
	if (!file_exists($tempDname)) {
		$this->message = "Oops!, Directory not instantiated";
		return;
	}
	
/*
 * now we can begin the restore
 */   	
	
	if ( isset($_POST['resetdb'] ) && $_POST['resetdb'] == 'YES') {
		if (file_exists($tempDname . '/opt/sark/db/sark.db')) {
			$this->helper->request_syscmd ("cp -f $tempDname/opt/sark/db/sark.db  /opt/sark/db/sark.db");
			$this->helper->request_syscmd ("chown www-data:www-data  /opt/sark/db/sark.db");
			$this->helper->request_syscmd ("sh /opt/sark/scripts/srkV4reloader.sh");
			$this->log .= "<p>Database RESTORED</p>";
		}
		else {
			$this->log .= "<p>No Database in backup set; request ignored </p>";
			$this->log .= "<p>Database PRESERVED</p>";
		}			
	}
	else {
		$this->log .= "<p>Database PRESERVED</p>";	
	}

	if ( isset($_POST['resetasterisk'] ) && $_POST['resetasterisk'] == 'YES') {
		if (file_exists($tempDname . '/etc/asterisk')) {
			$this->helper->request_syscmd ("rm -rf /etc/asterisk/*");
			$this->helper->request_syscmd ("cp -a  $tempDname/etc/asterisk/* /etc/asterisk");
			$this->helper->request_syscmd ("chown asterisk:asterisk /etc/asterisk/*");
			$this->log .= "<p>Asterisk files RESTORED</p>";
		}
		else {
			$this->log .= "<p>No Asterisk files in backup set; request ignored </p>";
			$this->log .= "<p>Asterisk Files PRESERVED</p>";
		}		
	}
	else {
		$this->log .= "<p>Asterisk Files PRESERVED</p>";	
	}	
			 			
	if ( isset($_POST['resetusergreets'] ) && $_POST['resetusergreets'] == 'YES') {
		if (glob($tempDname . '/usr/share/asterisk/sounds/usergreeting*')) {
			$this->helper->request_syscmd ("rm -rf /usr/share/asterisk/sounds/usergreeting*");
			$this->helper->request_syscmd ("cp -a  $tempDname/usr/share/asterisk/sounds/usergreeting* /usr/share/asterisk/sounds");
			$this->helper->request_syscmd ("chown asterisk:asterisk /usr/share/asterisk/sounds/usergreeting*");
			$this->log .= "<p>Greeting files RESTORED</p>";
		}
		else {
			$this->log .= "<p>No greeting files in backup set; request ignored </p>";
			$this->log .= "<p>Greeting files PRESERVED</p>";
		}
	}
	else {
		$this->log .= "<p>Greeting files PRESERVED</p>";	
	}
		
	if ( isset($_POST['resetvmail'] ) && $_POST['resetvmail'] == 'YES') {
		if (file_exists($tempDname . '/var/spool/asterisk/voicemail/default')) {
			$this->helper->request_syscmd ("rm -rf /var/spool/asterisk/voicemail/default");
			$this->helper->request_syscmd ("cp -a $tempDname/var/spool/asterisk/voicemail/default /var/spool/asterisk/voicemail");
			$this->helper->request_syscmd ("chown -R asterisk:asterisk /var/spool/asterisk/voicemail/default");
			$this->log .= "<p>Voicemail files RESTORED</p>";
		}
		else {
			$this->log .= "<p>No voicemail files in backup set; request ignored </p>";
			$this->log .= "<p>Voicemail files PRESERVED</p>";
		}
	}
	else {
		$this->log .= "<p>Voicemail files PRESERVED</p>";	
	}
	
	if ( isset($_POST['resetldap'] ) && $_POST['resetldap'] == 'YES') {
		if (file_exists($tempDname . '/tmp/sark.local.ldif')) {
			$this->helper->request_syscmd ("/etc/init.d/slapd stop");
			$this->helper->request_syscmd ("rm -rf /var/lib/ldap/*");
			$this->helper->request_syscmd ("slapadd -l " . $tempDname . "/tmp/sark.local.ldif");
			$this->helper->request_syscmd ("chown openldap:openldap /var/lib/ldap/*");
			$this->helper->request_syscmd ("/etc/init.d/slapd start");	
			$this->log .= "<p>LDAP Directory RESTORED</p>";
		}
		else {
			$this->log .= "<p>No LDAP Directory in backup set; request ignored </p>";
			$this->log .= "<p>LDAP Directory PRESERVED</p>";
		}
	}
	else {
		$this->log .= "<p>LDAP Directory PRESERVED</p>";	
	}	
	
	`rm -rf $tempDname`;
	$this->log .= "<p>Temporary work files deleted</p>";
	$this->helper->request_syscmd ("sh /opt/sark/scripts/srkV4reload");
	$this->log .= "<p>System Regen complete</p>";


//	echo '<div class="messagebox" >';
//	echo '<div class="message" style="font-size: 2em;padding-left:10em;padding-top:2em;">';

	echo '<p class="w3-container w3-small w3-margin w3-white">';
	echo $this->log;
	
	echo '</p>' . PHP_EOL; 
//	echo '</div>';
	echo '</form>';
    $this->myPanel->responsiveClose();	
	return;	
}	

private function printRestoreNotes ($rfile) {
#
#   prints info Box
#
	$helper = new helper;
	$dbh = DB::getInstance();

	preg_match( '/\.(\d+).zip$/',$rfile,$matches);
	$fdate = date('D d M H:i:s Y', $matches[1]);
	$fsize = filesize("/opt/sark/bkup/".$rfile);
	
    echo '<span style="color: #696969;" >';
    echo '<span style="font-weight:bold; "></span>';
    echo 'Backup: <strong>' . $rfile . '</strong><br/>' . PHP_EOL;
    echo 'Taken: <strong>' . $fdate . '</strong><br/>' . PHP_EOL;
    echo 'Size: <strong>' . $fsize . '</strong><br/>' . PHP_EOL;
	

}
}
