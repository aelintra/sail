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

Class sarkpcap {
	
	protected $message; 
	protected $head = "SIP PCAP";
	protected $myPanel;
	protected $helper;
	protected $logsize=2000;


public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->tail = new fileTail;
		
	
	$this->myPanel->pagename = 'SIP PCAP';
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}
	if (isset($_POST['sipcapFilter'])) { 
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
 
 	$pcapfiles = array();
	$tuple = array();	
/*
 * read the asterisk files and create an array of filename=>filetype
 */ 

	if ($handle = opendir('/var/log/siplog')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..') {
				array_push($pcapfiles, $entry);
			}
		}	
		closedir($handle);
		
	}
	
	$buttonArray=array();
	
	$this->myPanel->actionBar($buttonArray,"sarkpcapForm",false,false);
	
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

	echo '<form id="sarkpcapForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	$this->myPanel->beginResponsiveTable('logtable',' w3-small');	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('size',false,"w3-hide-small");	
	$this->myPanel->aHeaderFor('modified',false,"w3-hide-small");
	$this->myPanel->aHeaderFor('Download',false,'w3-hide-small w3-hide-medium');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;	
	echo '<tbody>' . PHP_EOL;
	
/**** table rows ****/	
	foreach ($pcapfiles as $file ) {
		if (file_exists("/var/log/siplog/" . $file)) {
			$rdate = `date -r /var/log/siplog/$file "+%m-%d-%Y %H:%M:%S"`;
			$fsize = filesize("/var/log/siplog/".$file);
			echo '<tr title = "Click to View">' . PHP_EOL;		
			echo '<td style="text-align:left; "><a href="' . $_SERVER['PHP_SELF'] . '?edit=yes&amp;pkey=' . $file . '" > ' . $file . '</a></td>';
			echo '<td class="w3-hide-small">' . $fsize . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-small" >' . $rdate . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-small w3-hide-medium"><a href="/php/downloadg.php?dfile=/var/log/siplog/' . $file . '"><img src="/sark-common/icons/download.png" alt="Download" title = "Click to Download"></a></td>' . PHP_EOL;	
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
	if (isset ($_REQUEST['savepkey'])) {
		$pkey = $_REQUEST['savepkey']; 
	}
	$filter=NULL;
	if (isset ($_REQUEST['logsipfilter'])) {		
		$filter = $_REQUEST['logsipfilter'];
	}
	$res = $this->dbh->query("SELECT LOGSIPDISPSIZE FROM globals WHERE pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$this->logsize = $res['LOGSIPDISPSIZE'];
	$fullPath = '/var/log/siplog/' . $pkey;
// turn on read access for the logfile	
 	$fileperms = trim(`stat -c %a $fullPath`);
	$this->helper->request_syscmd ( "/bin/chmod +r $fullPath" );
	$ngrepcmd = "ngrep  -I " . $fullPath . ' -qt -W byline "' . $filter . '" > /tmp/' . $pkey;
	`$ngrepcmd`;
	$outfile = $this->tail->tailCustom("/tmp/$pkey",$this->logsize,true);
	if (empty($outfile)) {
		$outfile = 'No entries in this log';
	}
// set the logfile back to its regular perms
	$this->helper->request_syscmd ( "chmod $fileperms $fullPath" );

/* 
 * start page output
 */
	$buttonArray=array();
	$buttonArray['cancel'] = true;
	$buttonArray['sipcapFilter'] = "w3-text-yellow";
 	$this->myPanel->actionBar($buttonArray,"sarklogForm",false,false);
 	$this->myPanel->responsiveSetup();

	$this->myPanel->subjectBar($pkey);
	
	echo '<form id="sarklogForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	echo '<input id="savepkey" type="hidden" name="savepkey" value="' . $pkey . '" >'. PHP_EOL;
	$this->myPanel->displayInputFor('logsipfilter','text',$filter);
		
//	echo '<div class="w3-col w3-border w3-light-grey w3-round-large w3-container">' . PHP_EOL;
//	echo '<button class="w3-button w3-right w3-small w3-block" form="sarklogForm" type="submit" name="cancel" value="cancel" title="Cancel">X close</button>';
	
	$this->myPanel->displayFile($outfile,"siplog",true);

//	echo '</div>';
	
	echo '</form>';
	$this->myPanel->responsiveClose();			
}

}
