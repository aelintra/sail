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
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $log = NULL;
	protected $invalidForm;
	protected $error_hash = array();
	protected $reboot;
	
public function showForm() {
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;

	echo '<form id="sarkbackupForm" action="' . $_SERVER['PHP_SELF'] . '" method="post" enctype="multipart/form-data">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Backup/Restore';

	if (!empty( $_POST['password'] )) {
		if ($this->helper->checkCreds( "admin",$_POST['password'],$this->message,false )) {
			$this->doRestore();			
			$this->message = "Restore complete";
			return;
//			$this->helper->request_syscmd ("reboot");
//			$this->reboot=true;
		}
		else {
			$this->showRestore($_POST['rfile']);
			return;
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

	if (isset($_POST['spin_x'])) { 
		$rc = $this->helper->request_syscmd ("/bin/sh /opt/sark/scripts/spin.sh");
		$this->message = "Backup taken!";	
	}
	
	if (!empty($_POST['upimgclick'])) { 
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
	echo '<img src="/sark-common/buttons/upload.png" id="upimg" alt="upload" title="Upload a saved snapshot or backup" />'. PHP_EOL;	
	$this->myPanel->commitButton();
	echo '</div>' . PHP_EOL;
	
	echo '<input type="file" id="file" name="file" style="display: none;" />'. PHP_EOL;
	echo '<input type="hidden" id="upimgclick" name="upimgclick" />'. PHP_EOL;
  	
	$this->myPanel->Heading();

	echo '<div class="datadivnarrow">';
	
	echo '<div id="pagetabs" >' . PHP_EOL;
    echo '<ul>'.  PHP_EOL;
    echo '<li><a href="#backs">Backups</a></li>'. PHP_EOL;
    echo '<li><a href="#snaps">Snapshots</a></li>'.  PHP_EOL;
//    echo '<li><a href="#uplds">Upload</a></li>'.  PHP_EOL;
    
    echo '</ul>'. PHP_EOL;
	
	echo '<div id="backs" >'. PHP_EOL;
	
	echo '<table class=display id="bkuptable"  >' ;	


	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('size');	
	$this->myPanel->aHeaderFor('astfiledate');	
	$this->myPanel->aHeaderFor('D/L');
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
		echo '<td >' . $fsize . '</td>' . PHP_EOL;
		echo '<td >' . $rdate . '</td>' . PHP_EOL;
		echo '<td class="center"><a href="/php/download.php?dtype=bkup&dfile=' . $file . '"><img src="/sark-common/icons/download.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;									
		echo '<td class="center"><a href="/php/sarkbackup/main.php?regressbk=yes&dfile=' . $file . '" ><img src="/sark-common/icons/undo.png" border=0 title = "Click to Restore" )"></a></td>' . PHP_EOL;	
		echo '<td class="icons"><a class="table-action-deletelink" href="delete.php?id=/opt/sark/bkup/' . $file . '"><img src="/sark-common/icons/delete.png" border=0 title = "Click to Delete" ></a></td>' . PHP_EOL;							
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
	$this->myPanel->aHeaderFor('D/L');
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
		echo '<td class="center"><a href="/php/download.php?dtype=snap&dfile=' . $file . '"><img src="/sark-common/icons/download.png" border=0 title = "Click to Download" ></a></td>' . PHP_EOL;									
		echo '<td class="center"><a href="/php/sarkbackup/main.php?regress=yes&dfile=' . $file . '"><img src="/sark-common/icons/undo.png" border=0 title = "Click to Regress" onclick="return confirmOK(\'Regress to this Snapshot - Confirm?\')"></a></td>' . PHP_EOL;	
		echo '<td class="center"><a class="table-action-deletelink" href="delete.php?id=/opt/sark/snap/' . $file . '"><img src="/sark-common/icons/delete.png" border=0 title = "Click to Delete" ></a></td>' . PHP_EOL;							
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;  	
   	
/*
 *      TAB DIVEND
 */

    echo '</div>' . PHP_EOL;   
#
#  end of TABS DIV
#        
    echo '</div>';	
    echo '</div>';	
}

private function showRestore($rfile) {

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	$this->myPanel->pagename = 'Restore ' . $rfile;
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	echo '<div class="buttons">';	
	$this->myPanel->Button("cancel");
	echo '</div>' . PHP_EOL;
	
	$this->printRestoreNotes ($rfile);
	
	if ($this->reboot) {
		echo '<div class="messagebox" >';
		echo '<div class="message" style="font-size: 2em;padding-left:10em;padding-top:2em;">';
		echo $this->log;
		echo '</div>';
		echo '</div>';
		return;
	}
	
	echo '<input id="rfile" type="hidden" name="rfile" value="' . $rfile . '" >'. PHP_EOL;		

    echo '<div class="datadivtabedit">';

	echo '<div id="reset" >'. PHP_EOL;
	
	echo '<h2>'. PHP_EOL;
	echo '<input id="resetdb" type="checkbox" name="resetdb" checked="checked" >'. PHP_EOL;
	echo ' :Restore Customer Database?';
	echo '<br/>';
	echo '<input id="asterisk" type="checkbox" name="asterisk" checked="checked" >'. PHP_EOL;
	echo ' :Restore Asterisk files?';				
	echo '<br/>';
	echo '<input id="vmail" type="checkbox" name="vmail" checked="checked" >'. PHP_EOL;
	echo ' :Restore Voicemail?';				
	echo '<br/>';
	echo '<input id="usergreets" type="checkbox" name="usergreets" checked="checked" >'. PHP_EOL;
	echo ' :Restore greetings?';				
	echo '<br/>';
	echo '<input id="ldap" type="checkbox" name="ldap" checked="checked" >'. PHP_EOL;
	echo ' :Restore LDAP Directory?';				
	echo '<br/>';																														
	echo '</h2>'. PHP_EOL;

    echo '</div>' . PHP_EOL;
    
    echo '<br/><br/>';
    echo '<div id="container">' . PHP_EOL;
    echo '<input type="password" id="password" name="password" placeholder="Enter Admin Password"> ' . PHP_EOL;       
    echo '<div id="lower"> ' . PHP_EOL;   	
    echo '</div>' . PHP_EOL; 
    echo '<br/><br/>';
    echo '<input type="submit" value="RESTORE"> '. PHP_EOL;                     

    echo '</div>' . PHP_EOL;      

    echo '</div>' . PHP_EOL;    
}

private function doRestore() {
	
/* 
 * Unzip the backup file
 */
	if (empty($_POST['rfile'])) {
		$this->message = "Oops!, lost filename";
		return;
	}
	$rfile =  strip_tags($_POST['rfile']);
/* 
 * start page output
 */

	$this->myPanel->pagename = 'Restore ' . $rfile;
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	echo '<div class="buttons">';	
	$this->myPanel->Button("cancel");
	echo '</div>' . PHP_EOL;
	
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
	
	if ( isset($_POST['resetdb'] ) ) {
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

	if ( isset($_POST['asterisk'] ) ) {
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
			 			
	if ( isset($_POST['usergreets'] ) ) {
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
		
	if ( isset($_POST['vmail'] ) ) {
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
	
	if ( isset($_POST['ldap'] ) ) {
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
	echo '<div class="messagebox" >';
	echo '<div class="message" style="font-size: 2em;padding-left:10em;padding-top:2em;">';
	echo $this->log;
	echo '</div>';
	echo '</div>';
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
	
	

	echo '<div  class="extnotes">' . PHP_EOL;
    echo '<span style="color: #696969;" >';
    echo '<span style="font-weight:bold; "></span><br/><br/>';
    echo 'Backup: <br/><strong>' . $rfile . '</strong><br/>' . PHP_EOL;
    echo 'Taken: <br/><strong>' . $fdate . '</strong><br/>' . PHP_EOL;
    echo 'Size: <strong>' . $fsize . '</strong><br/>' . PHP_EOL;
	
    echo '</div>' . PHP_EOL;

}
}
