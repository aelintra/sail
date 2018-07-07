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
//


//
// This is just a stub to integrate Chris Wild's recording retrieval code into the mainline
//

Class sarkrecordings {

	protected $message; 
	protected $head = "Recordings";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $netHelper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $astrunning=false;
	protected $keychange=NULL;
	protected $cosresult;
	protected $passwordLength=12;
	protected $myBooleans = array();


public function showForm() {

//	print_r($_REQUEST);

	$this->myPanel = new page;
		
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}	

private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 


/* 
 * start page output
 */
	
	$buttonArray=array();
	$this->myPanel->actionBar($buttonArray,"sarkrecordingForm",false,false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

//	$this->myPanel->responsiveSetup();	

//	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/sarkrecordings/recordings.php";
	echo '<div class="fluidMedia" >';
	echo '<iframe  src="/origrecs" style="width:100%;min-height:40em";border:0 !important;></iframe>' . PHP_EOL;
	echo '</div>';

	echo '</div>';
	
}
}
