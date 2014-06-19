<?php
// sarkuser class
// Developed by CoCo
// Copyright (C) 2012 CoCoSoFt
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

Class passwd {
	
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
	
//	if (isset($_SERVER['HTTP_REFERER']))  {

//	}
			
	echo '<body>';	
	echo '<form id="sarkpasswdForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Password change';
	
	if (isset($_POST['save_x'])) { 
		$this->saveNew();		
	}
	
	$this->showMain();
	
	return;		
}


private function showMain() {

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	echo '<div class="buttons">';	
	$this->myPanel->Button("save");
	echo '</div>';
		
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}	
	echo '<div>';	
	echo '<br/><br/><br/><br/>';
/*
	$this->myPanel->aLabelFor('chooser');
 	$this->myPanel->selected = 'webserver';
 	$this->myPanel->popUp('chooser', array('web access','ssh access'));
 */
	$this->myPanel->aLabelFor('httppassword');
	echo '<input type="password" name="password" id="password"/>' . PHP_EOL;
	$this->myPanel->aLabelFor('newpassword');
	echo '<input type="password" name="newpass" id="newpass"/>' . PHP_EOL;
	$this->myPanel->aLabelFor('newpassword2');
	echo '<input type="password" name="newpass2" id="newpass2"/>' . PHP_EOL;		
	echo '</div>';		
}

private function saveNew() {
	
	$record = array();
 	$pfile = "/opt/sark/passwd/htpasswd";

	$row = false;
	$handle = fopen($pfile, "r") or die('Could not read file!');
	while (!feof($handle)) {
		$row = fgets($handle);
		array_push($record,trim($row));
	}
	fclose($handle);
	print_r($record);
	$i = 0;
	
	while ($i < sizeof($record)) {
		$pos = strpos($record[$i], $_SERVER['REMOTE_USER']);
		if ($pos === false) {
			$i++;
		}
		else {
			$splits = explode(':',$record[$i]);
			break;
		}
	}

	if ($pos === false) {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash['user'] = "ERROR! - USER does not exist!!";
		return ;
	}
	
	$salt = substr($splits[1],0,2);
	
    $cp = crypt ( $_POST['password'],$salt );
    print_r($cp);
    
    if ( ! empty($_POST['password']) ) {
		if ( ! empty($_POST['newpass']) ) { 
    		if ( $_POST['newpass'] == $_POST['newpass2'] ) {
				if ( preg_match ( ' /(?=^.{8,}$)/ ',  $_POST['newpass'] )) {
    				if ( $cp == $splits[1]  ) {
//						$record[$i] = "\n" . $splits[0] . ':' . crypt ( $_POST['newpass'], $_POST['newpass'] );
						$data = '/usr/bin/htpasswd -bd /opt/sark/passwd/htpasswd ' . $splits[0] . ' ' . $_POST['newpass'];
						$this->helper->request_syscmd ($data);
						$this->message = "Password successfully changed";	
					}
					else {
						$this->invalidForm = True;
						$this->message = "<B>  --  Validation Errors!</B>";					
						$this->error_hash['user'] = "Old Password incorrect!";
					}
				}
				else {
					$this->invalidForm = True;
					$this->message = "<B>  --  Validation Errors!</B>";
					$this->error_hash['user'] = "Password must contain at least 8 characters!";
				}	
    		}
    		else {
				$this->invalidForm = True;
				$this->message = "<B>  --  Validation Errors!</B>";
    			$this->error_hash['user'] = "New Passwords do not match!";
    		}
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";
			$this->error_hash['user'] = "New Password not entered!";
		}
    }
    else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";
		$this->error_hash['user'] = "Password not entered!";
    }
/*
	if ( ! $this->invalidForm ) { 
		$fh = fopen($pfile, 'wb') or die('Could not open htpasswd');
		foreach ($record as $row) {
			if (isset($row)) {
				fwrite($fh,$row );
			}
		}
		fclose($fh);
	}
*/
}

}
