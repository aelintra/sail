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


Class sarkshell {
	

	protected $myPanel;
	protected $head = "System Shell";
	protected $dbh;
	protected $helper;
	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$buttonArray=array();

	$this->myPanel->actionBar($buttonArray,"sarkshellForm",false,false,false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup();

//	echo '<form id="sarkshellForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;



/*
 *  Start the iFrame
 */
	echo '<div class="w3-container w3-paddingw3-margin-right">';
	echo '<iframe class="w3-margin-right w3-card-4 w3-white" id="shellframe" scrolling="no" src="/console" style="height:40em;width:100%;" ></iframe>' . PHP_EOL;
	echo '</div>';
/*
 * done
 */	
//	echo '</form>';
	$this->myPanel->responsiveClose();	

	return;
	
}
}
