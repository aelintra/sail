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
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkPageClass";
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";

Class sarklogin {
	
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
	$login = true;
				
	if(!empty($_POST)) {		
		if ($this->helper->checkCreds( $_POST['pkey'],$_POST['password'],$this->message,$login )) {
	// force password change if it is set to the default
			if ($_SESSION['nag']) {
				header("Location: ../sarkpasswd/main.php"); 
				die("Redirecting to: sarkpasswd/main.php"); 
			}
			if ( $_POST['pkey'] == 'admin' ) {
				header("Location: ../sarkglobal/main.php"); 
				die("Redirecting to: sarkglobal/main.php");
			}
			else if ( $_POST['pkey'] == 'wallboard' ) {
				header("Location: ../sarkwallboard/main.php"); 
				die("Redirecting to: sarkwallboard/main.php");
			}
			else {
				header("Location: ../sarkphone/main.php"); 
				die("Redirecting to: sarkphone/main.php");	
			}			 
        }		
	}

//	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/banner.php";	
	
	$msg = NULL;
	
	if (isset($_GET['reset'])) {
		$msg = 'Password reset; please login';
	}
	if (isset($this->message)) {
		$msg = $this->message;
	} 
	
	echo '<form id="sarkloginForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;	
    echo '<div id="container">' . PHP_EOL;
	echo '<input type="text" id="pkey" name="pkey" placeholder="Username">' . PHP_EOL;
    echo '<input type="password" id="password" name="password" placeholder="Password"> ' . PHP_EOL;       
    echo '<div id="lower"> ' . PHP_EOL;   
	echo '<span id="lmsg">' . $msg . '</span>' . PHP_EOL;	
    echo '</div>' . PHP_EOL; 
    echo '<input type="submit" value="Login"> '. PHP_EOL;                     
	echo '</form>' . PHP_EOL;
    echo '</div>' . PHP_EOL; 
	
	return;		
}

}
