<?php
// 
// Developed by CoCo
// Copyright (C) 2016 CoCoSoFt
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
?>

<!DOCTYPE html>
<html  lang="en">
<head>
<title>Asterisk Call Status</title>
<meta http-equiv="refresh" content="5" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link rel="stylesheet" type="text/css" href="/sark-common/css/sark.css" />
<!--<script type="text/javascript" src="/sark-common/js/jquery-1.11.0.min.js" type="text/javascript"></script> --> 
<!--<script type="text/javascript" src="/php/sarkwallboard/javascript.js" type="text/javascript"></script> -->
</head>

<body>

<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
	
	$helper = new helper;

	if ( $helper->check_pid() ) {	
		$ccount = `/usr/sbin/asterisk -rx 'core show channels count'`;
	}
		
/*	
	$rawload = `/bin/cat /proc/loadavg`;
	$loadavg = explode (' ',$rawload);
	
	$rawmem = `/usr/bin/free -t`;
	$memlines = explode ("\n",$rawmem);
	$memlines[4] = preg_replace(' /\s+/ ', " ", $memlines[4]);
	$totcols = explode(" ",$memlines[4]);
*/	
	echo '<div class="statusnotice"/>';
/*
    echo 'Load Average: ' . $loadavg[0] . ', ' .  $loadavg[1] . ', ' .  $loadavg[2] .  '<br />';
    echo 'Memory: Total: ' . $totcols[1] . ', Used: ' . $totcols[2] . ', Free: ' . $totcols[3] . '<br />';
*/
    $data = explode("\n", $ccount);
	foreach($data as $line) {
		if (preg_match("/call/i", $line) || preg_match("/processed/i", $line)) {
      			echo '<strong>' . $line . '</strong><br/>';
    	}
    }
//    echo '<br/><strong>ACTIVE CALLS:-</strong><br/>';
	if ( !$helper->check_pid() ) {
		echo "Asterisk is not running<br/>";
		echo "</div>";
	}  
	else {
		echo "</div>";
		$result = `/usr/sbin/asterisk -rx 'core show channels concise'`;
		$ccount = `/usr/sbin/asterisk -rx 'core show channels count'`;
    	$data = explode("\n", $result);
		echo '<div class="statusdiv">'; 
    	echo '<table id="statustable">';
		echo '<thead>' . PHP_EOL;	
		echo '<tr>' . PHP_EOL;	
		echo '</tr>' . PHP_EOL;
		echo '</thead>' . PHP_EOL;
		echo '<tbody>' . PHP_EOL;      
		foreach($data as $line) {
			if (preg_match("/Up/", $line) && (preg_match("/!Dial!/", $line) 
				||  preg_match("/!ConfBridge!/i", $line) 
				||  preg_match("/!VoiceMail/i", $line) 
				|| preg_match("/!Queue!/i", $line)) ) {
          		$pieces = explode("!", $line);
          	// TOC and CLID are common to all
          		echo "<tr>";	
          	// time on call	
          		echo "<td>" . gmdate("H:i:s", $pieces[11]) . "</td>";
          	// CLID
          		$cn = preg_replace(' /^00/ ', '+', $pieces[7]);
          		echo "<td>" . $cn . "</td>";
// nice little arrow
          		echo '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          		
// regular Dial
          		if (preg_match("/!Dial!/i", $line)) {          	
          		// number connected
          			$dn = preg_replace(' /^00/ ', '+', $pieces[2]);
          			echo "<td>" . $dn . "</td>";
          			echo '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			echo "<td>" . $pieces[12] . "</td>";
          			continue;
          		}
// Confbridge          
          		if (preg_match("/!ConfBridge!/i", $line)) {
          			$dn = preg_replace(' /^00/ ', '+', $pieces[2]);
          			echo "<td>" . $dn . "</td>";
          			echo '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			echo "<td>ConfBridge</td>";
          			continue;
          		}
// Queue
          		if (preg_match("/!Queue!/i", $line)) {
          			$splitqueue = explode(',',$pieces[6]); 
          			echo "<td>" . $splitqueue[0] . "</td>";
          			echo '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			if ($pieces[12] == '(None)') {
          				echo "<td>In Queue</td>";
          			}
          			else {
          				echo "<td>" . $pieces[12] . "</td>";;
          			}	
          		} 
// Voicemail
			     if (preg_match("/!Voicemail/i", $line)) {
          			$splitqueue = explode(',',$pieces[6]); 
          			echo "<td>" . $splitqueue[0] . "</td>";
          			echo '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			echo "<td>Voicemail";
          			if (preg_match("/!VoicemailMain/i", $line)) {
          				echo " listen";
          			}
          			else {
          				echo " record";
          			}	
          			echo "</td>";	
          		}   			
          		
          	}
          	echo "</tr>";
		}
		echo '</tbody>';
		echo "</table>";	
	}
 	echo "</div>";
   	
?>
</body>
</html>
