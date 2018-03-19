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


Class support {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $distro = array();

public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->helper->qDistro($distro);

	
	echo '<body>';
//	echo '<form id="sarkgreetingForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Support Resources';
	$this->showMain();
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	$file = '/opt/sark/www/support.htm';
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	
	$fgreeting = array();
	$tuple = array();	

/* 
 * start page output
 */
  
	
	$this->myPanel->Heading();
	$rec = file($file) or die('Could not read support file!');
	foreach ($rec as $htmlout) { 
		echo $htmlout;
	} 

		
}

}
