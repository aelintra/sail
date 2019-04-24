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
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkFileTailClass";

Class sarklog {
	
	protected $message; 
	protected $head = "System Logs";
	protected $myPanel;
	protected $helper;
	protected $logsize=100;

/*
	List of logfiles we'll show 
 */
	protected $logFiles = array(
        "asterisk/messages"				=> true,
        "asterisk/full"					=> true,
        "asterisk/cdr-csv/Master.csv"	=> true,
        "asterisk/queue_log"			=> true,
        "syslog"						=> true,
        "shorewall.log"					=> true,
		"siplog"						=> true,
        "mail.log"						=> true,
        "fail2ban.log"					=> true,
        "auth.log"						=> true
  	);

public function showForm() {
	
	$this->myPanel = new page;
	$this->helper = new helper;
	$this->tail = new fileTail;
		
	
	$this->myPanel->pagename = 'System Logs';
		if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	
	$this->showMain();
	return;	
}
	
private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 	
/* 
 * start page output
 */
	
 	$this->myPanel->actionBar($buttonArray,"sarklogForm",false,false);
	
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

//	$this->myPanel->subjectBar("System Logs");
	echo '<form id="sarklogForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	$this->myPanel->beginResponsiveTable('logtable',' w3-small');	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('Download',false,'w3-hide-small w3-hide-medium');
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;	
	echo '<tbody>' . PHP_EOL;
	
/**** table rows ****/	
	foreach ($this->logFiles as $key => $file ) {
		if (file_exists("/var/log/" . $key)) {
			echo '<tr title = "Click to View">' . PHP_EOL;		
			echo '<td style="text-align:left; "><a href="' . $_SERVER['PHP_SELF'] . '?edit=yes&amp;pkey=' . $key . '">/var/log/' . $key . '</a></td>';
			echo '<td class="w3-hide-small w3-hide-medium"><a href="/php/downloadg.php?dfile=/var/log/' . $key . '"><img src="/sark-common/icons/download.png" alt="Download" title = "Click to Download" ></a></td>' . PHP_EOL;	
			echo '</tr>'. PHP_EOL;	
		}
	}
	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>' . PHP_EOL;
	$this->myPanel->responsiveClose();	
}

private function showEdit() {
	
	if (isset ($_GET['pkey'])) {
		$pkey = $_GET['pkey']; 
	}
	$fullPath = '/var/log/' . $pkey;
// turn on read access for the logfile	
 	$fileperms = trim(`stat -c %a $fullPath`);
	$this->helper->request_syscmd ( "/bin/chmod +r $fullPath" );
	$outfile = $this->tail->tailCustom("/var/log/$pkey",$this->logsize,true);
	if (empty($outfile)) {
		$outfile = 'No entries in this log';
	}
// set the logfile back to its regular perms
	$this->helper->request_syscmd ( "chmod $fileperms $fullPath" );
	$printline = "/var/log/$pkey";
/* 
 * start page output
 */
	$buttonArray=array();
	$buttonArray['cancel'] = true;
 	$this->myPanel->actionBar($buttonArray,"sarklogForm",false,false);
 	$this->myPanel->responsiveSetup();

	$this->myPanel->subjectBar($pkey);
	
	echo '<form id="sarklogForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
		
//	echo '<div class="w3-col w3-border w3-light-grey w3-round-large w3-container">' . PHP_EOL;
	echo '<button class="w3-button w3-right w3-small w3-block" form="sarklogForm" type="submit" name="cancel" value="cancel" title="Cancel">X close</button>';
	
	$fArray = explode(PHP_EOL,$outfile);
	echo '<p class=" w3-border w3-white">';
	foreach ($fArray as $row) {
		echo '<span class="w3-small w3-white" style="overflow-wrap:break-word;" >' . $row . '</span><br/>' . PHP_EOL;
	}
	echo '</p>';

//	echo '</div>';
	echo '</form>';
	$this->myPanel->responsiveClose();			
}

}