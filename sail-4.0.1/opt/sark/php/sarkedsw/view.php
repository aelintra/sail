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



Class edsw {
	
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
	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Firewall';
	
	if (isset($_POST['new_x'])) { 
		$this->showNew();
	}
	
	if (isset($_POST['restfw_x'])) { 
		$this->restartFirewall();				
	}
	
	if (isset($_GET['delete'])) { 
		$this->ruleDelete();
	}	

		
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}

private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	else {
		$this->myPanel->msg = "N.B. restart the firewall to action your changes!";
	}
/* 
 * start page output
 */
  
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
	$this->myPanel->Button("restfw");
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	echo '<div class="datadivnarrow">';

	echo '<table class="display" id="edswtable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('action'); 	
	$this->myPanel->aHeaderFor('fwsource');
	$this->myPanel->aHeaderFor('fwdest');
	$this->myPanel->aHeaderFor('fwproto');
	$this->myPanel->aHeaderFor('fwdestports');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
	$file = '/etc/shorewall/sark_rules';
	if (file_exists($file)) {
		$rec = file($file) or die('Could not read file!');
	}
	$pkey=1;
	foreach ($rec as $row ) {
		if (preg_match(" /^#|^\s/ ", $row)) {
			$pkey++;
			continue;			
		}
		else {
			$elements = explode(" ",$row);
			echo '<tr id="' . $pkey . '">'. PHP_EOL; 
			echo '<td class="read_only">' . $elements[0] . '</td>' . PHP_EOL;				 
			echo '<td >' . $elements[1]  . '</td>' . PHP_EOL;		
			echo '<td >' . $elements[2]  . '</td>' . PHP_EOL;
			echo '<td >' . $elements[3]  . '</td>' . PHP_EOL;
			echo '<td >' . $elements[4]  . '</td>' . PHP_EOL;
			$get = '?delete=yes&amp;pkey='. $pkey;
			$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$get);				
			echo '</td>' . PHP_EOL;
			echo '</tr>'. PHP_EOL;
			$pkey++;
		}
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;	
	echo '</div>';
	
}

private function ruleDelete() {
	$rc = $this->helper->request_syscmd ("/bin/sed -i '".$_GET['pkey']."d' /etc/shorewall/sark_rules");
}


private function showNew() {
	
	$rc = $this->helper->request_syscmd ("/bin/echo 'ACCEPT net:\$LAN \$FW tcp 65535' >>  /etc/shorewall/sark_rules");			
}

private function restartFirewall() {
	
	$rc = $this->helper->request_syscmd ("/etc/init.d/shorewall restart");
	$this->message = $rc;				
}

}
