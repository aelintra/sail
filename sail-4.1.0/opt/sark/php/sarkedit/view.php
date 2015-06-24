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


Class edit {
	
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
		
	echo '<body>';
	echo '<form id="sarkeditForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Edit';
	
	if (isset($_GET['edit'])) { 
		$this->showEdit();	
		return;
	}	

	if (isset($_POST['update_x'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}					
	}
	
	if (isset($_POST['commit_x']) || isset($_POST['commitClick_x'])) { 
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}
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
	
	$astfiles = array();
	$tuple = array();	

/* 
 * start page output
 */
  
	echo '<div class="buttons">';	
	$this->myPanel->commitButton();
	echo '</div>';	
	
	$this->myPanel->Heading();
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
	
	echo '<div class="datadivedit">';

	echo '<table class="display" id="edittable">' ;	

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
	echo '</table>' . PHP_EOL;
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
	
	if (isset ($_GET['pkey'])) {
		$pkey = $_GET['pkey']; 
	}
	$file = file("/etc/asterisk/$pkey") or die("Could not read file $pkey !");
	$astfile='';
	
	foreach ($file as $rec) {
		$astfile .= $rec;
	}
	
	$printline = "File " . $pkey;
	$this->myPanel->msg .= $printline; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	} 
	
	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->override = "update";
	$this->myPanel->Button("save");
	echo '</div>';	
		
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	
	echo '<div class="datadivtabedit">';
	echo '<h2>Asterisk File ' . $pkey . ' </h2>' . PHP_EOL;
   	if (array_key_exists($pkey,$readOnlyFiles)) {
		echo '<textarea class="databox" readonly="readonly" style = "background-color: lightgrey" name="astfile" id="astfile">' . $astfile . '</textarea>' . PHP_EOL;
	}
	else { 	
		echo '<textarea class="databox" name="astfile" id="astfile">' . $astfile . '</textarea>' . PHP_EOL;  
	}
	echo '<input type="hidden" name="pkey" id="pkey" value="' . $pkey . '"  />' . PHP_EOL;	
	echo '</div>'; 
			
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
	}
	$this->message = " Updated $pkey";
}

}
