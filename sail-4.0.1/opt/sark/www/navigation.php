<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en-EN">
<head>
	<script type="text/javascript" src="sark-common/js/jquery-1.11.0.min.js" type="text/javascript"></script>
	<script src="sark-common/js/menu.js" type="text/javascript"></script>
	<link rel="stylesheet" type="text/css" href="sark-common/css/acc_style.css" />
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
	<title>SARK Server V4.0</title>
	<!--[if lt IE 9]>
   <style type="text/css">
   li a {display:inline-block;}
   li a {display:block;}
   </style>
   <![endif]-->
</head>
<br/>
<br/>
<br/>
<body>
	<ul class="menu collapsible">
<?php
require_once "../php/srkDbClass";

	$arch = 'x86';
	if (preg_match( " /armv/ ", `uname -a`)) {
		$arch = 'arm';
	}
	$dbh = DB::getInstance();
	$sql = "select un.pkey, up.panel_pkey, p.classname, p.displayname, pg.groupname from user un " .
			"inner join UserPanel up on up.user_pkey = un.pkey " . 
			"inner join panel p on up.panel_pkey = p.pkey ".
			"inner join panelgrouppanel pgp on p.pkey = pgp.panel_pkey " .
			"inner join panelgroup pg on pgp.panelgroup_pkey = pg.pkey where un.pkey='" .
			$_SERVER['REMOTE_USER'] .  "' order by pgp.panelgroup_pkey";
			
	foreach ($dbh->query($sql) as $row) {
		$path = '../php/';
		if (preg_match(" /.*\.pl$/",$row['classname'])) {
			$path = 'cgi-bin/';
		}
			
		if (!isset($endpoint) ) {
			echo '<li>' . PHP_EOL;
			echo '<a href="#">' . $row['groupname'] . '</a>' . PHP_EOL;	
			echo '<ul class="acitem">' . PHP_EOL;			
		}	
		else if ($endpoint != $row['groupname'] && $row['groupname'] != 'CDRs' ) {
			echo '</ul>' . PHP_EOL;
			echo '</li>' . PHP_EOL;
			echo '<li>' . PHP_EOL;
			echo '<a href="#">' . $row['groupname'] . '</a>' . PHP_EOL;
			echo '<ul class="acitem">' . PHP_EOL;
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
			echo '<li><a target="main" href="' . $path . $row['classname'] . '">' . $row['displayname'] .  '</a></li>' .  PHP_EOL;			
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
			<ul class="acitem">
				<li><a target="main" href="../stat/?s=1">Report</a></li>
				<li><a target="main" href="../stat/?s=2">Compare</a></li>
				<li><a target="main" href="../stat/?s=3">Monthly</a></li>
				<li><a target="main" href="../stat/?s=4">Daily</a></li>
			</ul>
		</li>
<?php
	}
?>

	
</body>
</html>
