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
	
	echo '<div class=chooser>' . PHP_EOL;

	echo '<form id="sarknavForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$url = explode('/', $_SERVER['SCRIPT_URL']);
	if ( !preg_match( '/^sarkstat/', $url[2] ) && !preg_match( '/^sarkmonitor/', $url[2] ) ) {
		if ($_SESSION['user']['pkey'] == 'admin') {
			$myPanel = new page;
			$myPanel->searchBox();
		}
	}
	echo '</form>' . PHP_EOL;	 
	
	echo '<ul class="myMenu">' . PHP_EOL;		
//	echo '<li><a target="_parent" href="/php/sarkglobal/main.php">Home</a></li>' . PHP_EOL;

	if ($_SESSION['user']['pkey'] == 'admin') {
?>
	<li>
		<a href="#">New</a>
		<ul>
<?php		
		$sql = $dbh->query("SELECT * FROM Panel ORDER BY displayname");
		$rows = $sql->fetchAll();
		foreach ($rows as $panel) {
			if ($panel['active'] == 'yes' && $panel['fastnew'] == 'yes') { 
				echo '<li><a href="/php/' . $panel['classname'] . '?new=yes">' . $panel['displayname'] . '</a></li>';
			}
		}
			
?>					
		</ul>
	</li>	
<?php
	}		
	$arch = 'x86';
	if (preg_match( " /armv/ ", `uname -a`)) {
		$arch = 'arm';
	}
	
	$sql =  "SELECT un.pkey, up.panel_pkey, p.classname, p.displayname, p.active, pg.groupname from user un " .
			"INNER JOIN UserPanel up on up.user_pkey = un.pkey " . 
			"INNER JOIN panel p on up.panel_pkey = p.pkey ".
			"INNER JOIN panelgrouppanel pgp on p.pkey = pgp.panel_pkey " .
			"INNER JOIN panelgroup pg on pgp.panelgroup_pkey = pg.pkey " . 
			"WHERE un.pkey='" .
			$_SESSION['user']['pkey'] .  
			"' AND p.active='yes' ORDER BY pgp.panelgroup_pkey,p.displayname ";
			
	foreach ($dbh->query($sql) as $row) {
		$path = '/php/';
		if (preg_match(" /.*\.pl$/",$row['classname'])) {
			$path = 'cgi-bin/';
		}		
		if (!isset($endpoint) ) {
			echo '<li>' . PHP_EOL;
			echo '<a href="#">' . $row['groupname'] . '</a>' . PHP_EOL;	
			echo '<ul>' . PHP_EOL;			
		}	
		else if ($endpoint != $row['groupname'] && $row['groupname'] != 'CDRs' ) {
			echo '</ul>' . PHP_EOL;
			echo '</li>' . PHP_EOL;
			echo '<li>' . PHP_EOL;
			echo '<a href="#">' . $row['groupname'] . '</a>' . PHP_EOL;
			echo '<ul>' . PHP_EOL;
		}
		if ( $row['displayname'] != 'CDRs' ) {
			if (preg_match(" /^sarkldap/ ",$row['classname'])) {
				exec( "ps aux | grep slapd | grep -v grep", $out, $retcode );
				if ($retcode) {
					continue;
				}
			}			
			if (preg_match(" /^sarkpci/ ",$row['classname']) && $arch == 'arm') {
				continue;
			}
			echo '<li><a href="' . $path . $row['classname'] . '">' . $row['displayname'] .  '</a></li>' .  PHP_EOL;			
		}
		else {
			$CDR = true;
		}		
		$endpoint = $row['groupname'];	
	}
	echo '</ul>' . PHP_EOL;
	echo '</li>' . PHP_EOL;
	$dbh = NULL;
	
	
	if ( $CDR ) {
?> 		
		<li >
			<a href="#">CDRs</a>
			<ul>
				<li><a target="_parent" href="/php/sarkstat1/main.php">Report</a></li>
				<li><a target="_parent" href="/php/sarkstat2/main.php">Compare</a></li>
				<li><a target="_parent" href="/php/sarkstat3/main.php">Monthly</a></li>
				<li><a target="_parent" href="/php/sarkstat4/main.php">Daily</a></li>
			</ul>
		</li>
<?php
	}
?>
<li><a target="_parent" href="/php/srksessions/logout.php">Logout</a></li>
</div>



	

