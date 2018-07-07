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


Class sarkwallboard {
	
	protected $head="Wallboard"; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $distro = array(); 
	protected $HA;
	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
			
//	echo '<form id="sarkwallboardForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	}
	$buttonArray=array();
	$buttonArray['refresh'] = True;
  	$this->myPanel->actionBar($buttonArray,"sarkreceptionForm",false,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head);

	$this->myPanel->responsiveSetup(2);

	?>
	<table id="chantable">
    <thead>
        <tr>
            <th></th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td></td>
        </tr>
    </tbody>
	</table>

	<?php
	


	$this->myPanel->responsiveClose();	
	return;
	
}
	

}
