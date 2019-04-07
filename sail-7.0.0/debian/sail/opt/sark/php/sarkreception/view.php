<?php
//
// Developed by CoCo
// Copyright (C) 2018 CoCoSoft
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


Class sarkreception {
	
	protected $message=NULL;
	protected $head="SARK PBX"; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $HA;


	
public function showForm() {

	$this->myPanel = new page;
//	$this->dbh = DB::getInstance();
	$this->helper = new helper;
			
	$this->myPanel->pagename = 'SARK PBX';
 
//	Debugging		
//	$this->helper->logit(print_r($_POST, TRUE));
	
	
	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
		$this->helper->sysCommit();
		$this->message = "Committed!";			
	}


			
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() { 

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	}
	$buttonArray=array();
  	$this->myPanel->actionBar($buttonArray,"sarkForm",false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

	echo '<h1 class="w3-center w3-jumbo">Welcome</h1>';

	
//	echo '<div class="w3-display-container" style="min-height:7em">';
	if ( $_SESSION['user']['pkey'] == 'admin' ) {
		echo '<div class="w3-container">';
		echo '<form id="sarkForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;         
		echo '<input class=" w3-input w3-card-4 w3-round-large" type="text" name="searchkey">';
		echo '</form>';	
		$this->myPanel->aHelpBoxFor('searchkey');
		
		echo '</div>' . PHP_EOL;

		$this->myPanel->printSysNotes();
//		echo '</div>' . PHP_EOL;
	}

	
	echo '</div>';
    $this->myPanel->responsiveClose();
    
}
 
}
