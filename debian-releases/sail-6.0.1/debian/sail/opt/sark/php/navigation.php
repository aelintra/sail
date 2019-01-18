<?php
//
// Developed by CoCo
// Copyright (C) 2015 CoCoSoft
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

	$dbh = DB::getInstance();
	$CDR = false; 
	
	$arch = 'x86';
	if (preg_match( " /armv/ ", `uname -a`)) {
		$arch = 'arm';
	}
	
	$res = $dbh->query("SELECT VCL,PKTINSPECT FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	
	$sql =  "SELECT un.pkey, up.panel_pkey, p.classname, p.displayname, p.active, pg.groupname from user un " .
			"INNER JOIN UserPanel up on up.user_pkey = un.pkey " . 
			"INNER JOIN panel p on up.panel_pkey = p.pkey ".
			"INNER JOIN panelgrouppanel pgp on p.pkey = pgp.panel_pkey " .
			"INNER JOIN panelgroup pg on pgp.panelgroup_pkey = pg.pkey " . 
			"WHERE un.pkey='" .
			$_SESSION['user']['pkey'] .  
			"' AND p.active='yes' ORDER BY pgp.panelgroup_pkey,p.displayname ";			
	

	echo '<div class="w3-bar  w3-white"> <!-- BEGIN NAVBAR -->' . PHP_EOL;


	$nogen=false;
	$path = '/php/';
	foreach ($dbh->query($sql) as $row) {			
		if (!isset($endpoint) ) {
			echo '<a href="/php/sarkglobal/main.php" class="w3-bar-item w3-button w3-hide-small w3-hide-medium"><i class="fas fa-home"></i></a>' . PHP_EOL;
			echo '<div class="w3-dropdown-hover w3-hide-small w3-hide-medium"> <!-- BEGIN MENU BLOCK -->' . PHP_EOL;
			echo '<button class="w3-button">' . $row['groupname'] . '</button>' . PHP_EOL;	
			echo '<div class="w3-dropdown-content w3-bar-block w3-card-4"> <!-- BEGIN MENU BLOCK -->' . PHP_EOL;			
		}	
		else if ($endpoint != $row['groupname']) {
			echo '</div> <!-- END OF DROPDOWNS -->' . PHP_EOL;
			echo '</div> <!-- END OF MENU BLOCK -->' . PHP_EOL;
			echo '<div class="w3-dropdown-hover w3-hide-small w3-hide-medium"> <!-- BEGIN MENU BLOCK -->' . PHP_EOL;
			echo '<button class="w3-button">' . $row['groupname'] . '</button>' . PHP_EOL;	
			echo '<div class="w3-dropdown-content w3-bar-block w3-card-4">' . PHP_EOL;
		}		
		if (preg_match(" /^sarkldap/ ",$row['classname'])) {
			exec( "ps aux | grep slapd | grep -v grep", $out, $retcode );
			if ($retcode) {
				$nogen=true;
			}
		}			
		if (preg_match(" /^sarkpci/ ",$row['classname']) && $arch == 'arm') {
			$nogen=true;
		}
		if (preg_match(" /^sarkpci/ ",$row['classname']) && $res['VCL'] == true ) {
			$nogen=true;
		}
		if (preg_match(" /^sarkpci/ ",$row['classname']) ) {
			if ( !file_exists("/etc/dahdi/system.conf")) {
				$nogen=true;
			}
		}					
		if (preg_match(" /^sarkthreat/ ",$row['classname']) && $res['PKTINSPECT'] == false ) {
			$nogen=true;
		}
		if (preg_match(" /^sarkdiscover/ ",$row['classname']) && $res['VCL'] == true ) {
			$nogen=true;
		}
		if (preg_match(" /^sarkmcast/ ",$row['classname']) && $res['VCL'] == true ) {
			$nogen=true;
		}			
		if (!$nogen) {						
			echo '<a class="w3-block w3-button w3-border-bottom w3-border-light-gray w3-left-align w3-white" style="width:15em;" href="' . $path . $row['classname'] . '">' . $row['displayname'] .  
			'<i class="fas fa-chevron-right w3-text-light-grey w3-right"></i></a>' .  PHP_EOL;
		}						
				
		$endpoint = $row['groupname'];
		$nogen=false;	
	}
	echo '</div> <!-- END OF DROPDOWNS -->' . PHP_EOL;
	$dbh = NULL;

	echo '</div> <!-- END OF MENU BLOCK -->' . PHP_EOL;
	echo '<a href="/php/srksessions/logout.php" target="_parent" class="w3-bar-item w3-button w3-hide-small w3-hide-medium">Logout</a>' . PHP_EOL;


	echo '<form id="sarknavForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	echo '<button class="w3-bar-item w3-button w3-right w3-blue" style="width:3em">Go</button>' . PHP_EOL;
	echo '<input type="text" class="w3-bar-item w3-light-grey w3-right w3-input" name="searchkey" size="10" id="searchkey" placeholder="Keysearch">' . PHP_EOL;
	echo '</form>'  . PHP_EOL;
	echo '<a href="javascript:void(0)" class="w3-bar-item w3-button w3-blue w3-left w3-hide-large" onclick="srkMenuFunction(' . "'srkMobileMenu'" . ')"><span class="fas fa-bars"></span></a>' . PHP_EOL;
	echo '</div> <!-- END OF top menu bar -->' . PHP_EOL;