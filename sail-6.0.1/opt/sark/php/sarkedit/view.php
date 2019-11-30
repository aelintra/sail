
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


Class sarkedit {
	
	protected $message;
	protected $head = "Asterisk Files";
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
	
	$this->myPanel->pagename = 'Edit';
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	

	if (isset($_POST['update']) || isset($_POST['endupdate'])) { 
		$this->saveEdit();
		$this->showEdit();
		return;				
	}
	
	if (isset($_POST['commit']) ) { 
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}
		$this->helper->sysCommit();
		$this->message = "Committed";	
	}	
	
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
	$astfiles = array();
	$tuple = array();	
/*
 * read the asterisk files and create an array of filename=>filetype
 */ 

	if ($handle = opendir('/etc/asterisk')) {
		while (false !== ($entry = readdir($handle))) {
			if ($entry != '.' && $entry != '..') {
				array_push($astfiles, $entry);
			}
		}	
		closedir($handle);
	}
	$buttonArray=array();	
	$this->myPanel->actionBar($buttonArray,"sarkeditForm",false);
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup(2);	
//	$this->myPanel->subjectBar("Asterisk Files");

	echo '<form id="sarkeditForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	$this->myPanel->beginResponsiveTable('edittable');	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('astfilename');	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
/**** table rows ****/	
	foreach ($astfiles as $file ) {
		echo '<tr id="' . $file . '" title = "Click to View/Edit">' . PHP_EOL;		
		echo '<td style="text-align:left; "><a href="' . $_SERVER['PHP_SELF'] . '?edit=yes&amp;pkey=' . $file . '"</a>' . $file . '</td>';
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();
}

private function showEdit() {
	$readOnlyFiles = array(
        "agents.conf"						=> null,
        "dahdi-channels.conf"				=> null,
		"extensions.conf"					=> null,
        "features.conf"						=> null,
        "iax.conf"							=> null,
        "queues.conf"						=> null,
        "sip.conf"							=> null,
        "sark_agents_main.conf"  			=> null,
        "sark_iax_localnet_header.conf"  	=> null,
        "sark_iax_main.conf"  				=> null,      
        "sark_iax_registrations.conf"     	=> null,
        "sark_meetme.conf"     				=> null,
        "sark_queues_main.conf"  			=> null,
        "sark_sip_localnet_header.conf"  	=> null,
        "sark_sip_main.conf"  				=> null,
        "sark_sip_registrations.conf"  		=> null,
        "cdr_mysql.conf"  					=> null,
	);
	
	if (isset ($_REQUEST['pkey'])) {
		$pkey = $_REQUEST['pkey']; 
	}
	$file = file("/etc/asterisk/$pkey") or die("Could not read file $pkey !");
	$astfile='';
	
	foreach ($file as $rec) {
		$astfile .= $rec;
	}
	
	$printline = "File " . $pkey;
	$buttonArray['cancel'] = true;
	if (!array_key_exists($pkey,$readOnlyFiles)) {
		$this->myPanel->actionBar($buttonArray,"sarkeditForm",false,true,true);
	}
	else {
		$this->myPanel->actionBar($buttonArray,"sarkeditForm",false,false);
	}
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$this->myPanel->responsiveSetup();	
	$this->myPanel->subjectBar($pkey);
	echo '<form id="sarkeditForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
   	if (array_key_exists($pkey,$readOnlyFiles)) {
   		$readonly=true;
//		echo '<h2>Asterisk File ' . $pkey . ' (Readonly) </h2>' . PHP_EOL;
		$this->myPanel->displayFile($astfile,"astfile",true);
	}
	else {
		$this->myPanel->displayFile($astfile,"astfile");	  
	}
	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;


	$endButtonArray['cancel'] = true;
	if (!array_key_exists($pkey,$readOnlyFiles)) {
		$endButtonArray['update'] = "endupdate";
	}
	$this->myPanel->endBar($endButtonArray);
		

	echo '</form>'; 
	$this->myPanel->responsiveClose();
			
}


private function saveEdit() {
// save the data away
//print_r ($_POST) ;

	$tuple = array();
	if (isset ($_POST['pkey'])) {
		$pkey = $_POST['pkey']; 
	}
	if (isset ($_POST['astfile'])) {
		$astfile = strip_tags($_POST['astfile']); 
	}
	$astfile = preg_replace ( "/\\\/", '', $astfile);	
		
	$this->validator = new FormValidator();

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
		$fh = fopen("/etc/asterisk/$pkey", 'w') or die("Could not open file $pkey!");
		fwrite($fh,$astfile) or die("Could not write to file $pkey");
		fclose($fh);
		// cleanse the file
		$this->helper->request_syscmd ( "/usr/bin/dos2unix /etc/asterisk/$pkey" );
	}
	$this->message = "Updated!";
}

}
