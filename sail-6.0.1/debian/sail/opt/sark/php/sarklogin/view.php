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
/*
			if ( $_POST['pkey'] == 'admin' ) {
				header("Location: ../sarksplash/main.php"); 
				die("Redirecting to: sarksplash/main.php");
			}
*/
			else if ( $_POST['pkey'] == 'wallboard' ) {
				header("Location: ../sarksplash/main.php"); 
				die("Redirecting to: sarksplash/main.php");
			}
            else if ( $_POST['pkey'] == 'directory' ) {
                header("Location: ../sarkldap/main.php");
                die("Redirecting to: sarkldap/main.php");
            }			
			else {
				header("Location: ../sarkreception/main.php"); 
				die("Redirecting to: sarkreception/main.php");	
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

	echo '<div style="position: fixed; top: 0px; left: 0px; width: 100%; height: 100%; background: SmokeWhite;">';
	echo '<div class="w3-hide-small w3-hide-medium" style="width:100%;height:4em;"><span>&nbsp;</span></div>' . PHP_EOL;
	echo '<div class="w3-hide-large w3-hide-small" style="width:100%;height:2em;"><span>&nbsp;</span></div>' . PHP_EOL;
	echo '<div class="w3-hide-medium w3-hide-large" style="width:100%;height:4em;"><span>&nbsp;</span></div>' . PHP_EOL;
	echo '<div class="w3-row">' . PHP_EOL;   
    echo '<div class="w3-col m2 l4"><span>&nbsp;</span></div>' . PHP_EOL;
    echo '<div class="w3-col s12 m7 l4">' . PHP_EOL;
    echo '<div class="w3-card-4 w3-margin w3-border w3-white w3-round-large w3-margin-top" style="width:90%">' . PHP_EOL;
    if (file_exists("/sark-common/Customer_favicon.png")) {
    	echo '<div class="w3-panel" ><h2><img src="/sark-common/Customer_favicon.png" class=" w3-image" alt="SARK UCS" style="max-height: 3em;"></h2></div>' . PHP_EOL;
    }
    else {
    	echo '<div class="w3-panel" ><h2><img src="/sark-common/Sark_favicon.png" class=" w3-image" alt="SARK UCS" style="max-height: 3em;"></h2></div>' . PHP_EOL;
    }
    echo '<form class="w3-container" id="sarkloginForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;	
	echo '<p><input class="w3-input" type="text" id="pkey" name="pkey" placeholder="Username"></p>' . PHP_EOL;
    echo '<p><input class="w3-input" type="password" id="password" name="password" placeholder="Password"></p> ' . PHP_EOL;       
    echo '<div class="w3-margin-bottom"> ' . PHP_EOL;   
	echo '<span id="lmsg">' . $msg . '</span>' . PHP_EOL;	
    echo '</div>' . PHP_EOL; 
    echo '<input class="w3-input w3-blue w3-margin-bottom w3-right w3-round-xxlarge" style="width:5em" type="submit" value="Login"> '. PHP_EOL;                     
	echo '</form>' . PHP_EOL;
	echo '</div>' . PHP_EOL; 
	echo '</div>' . PHP_EOL; 
	echo '<div class="w3-rest"></div>' . PHP_EOL;	 
	echo '</div>' . PHP_EOL;  
	echo '</div>' . PHP_EOL; 
	return;		
}

}
