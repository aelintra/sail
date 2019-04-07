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

  
//	syslog(LOG_WARNING, "channel reader running");

/* memory jogger ToDo
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkAmiHelperClass";
  $amiHelper = new amiHelper;
  $channels = $amiHelper->get_coreShowChannels();
  syslog(LOG_WARNING, $channels);
*/

	$result = `sudo /usr/sbin/asterisk -rx 'core show channels concise'`;
  $data = explode("\n", $result);

  $stream = null;
 
    $stream .= '<table class="w3-table w3-card-4 w3-text-gray w3-striped w3-hoverable" id="chantable">';
		$stream .= '<thead>';	
		$stream .= '<tr>';	
		$stream .= '</tr>';
		$stream .= '</thead>';
		$stream .= '<tbody>';      
		foreach($data as $line) {
			if (preg_match("/Up/", $line) && (preg_match("/!Dial!/", $line) 
				||  preg_match("/!ConfBridge!/i", $line) 
				||  preg_match("/!VoiceMail/i", $line) 
				|| preg_match("/!Queue!/i", $line)) ) {
          		$pieces = explode("!", $line);
          	// TOC and CLID are common to all
          		$stream .= "<tr>";	
          	// time on call	
          		$stream .= "<td>" . gmdate("H:i:s", $pieces[11]) . "</td>";
          	// CLID
          		$cn = preg_replace(' /^00/ ', '+', $pieces[7]);
          		$stream .= "<td>" . $cn . "</td>";
// nice little arrow
          		$stream .= '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          		
// regular Dial
          		if (preg_match("/!Dial!/i", $line)) {          	
          		// number connected
          			$dn = preg_replace(' /^00/ ', '+', $pieces[2]);
          			$stream .= "<td>" . $dn . "</td>";
          			$stream .= '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			$stream .= "<td>" . $pieces[12] . "</td>";
          			continue;
          		}
// Confbridge          
          		if (preg_match("/!ConfBridge!/i", $line)) {
          			$dn = preg_replace(' /^00/ ', '+', $pieces[2]);
          			$stream .= "<td>" . $dn . "</td>";
          			$stream .= '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			$stream .= "<td>ConfBridge</td>";
          			continue;
          		}
// Queue
          		if (preg_match("/!Queue!/i", $line)) {
          			$splitqueue = explode(',',$pieces[6]); 
          			$stream .= "<td>" . $splitqueue[0] . "</td>";
          			$stream .= '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			if ($pieces[12] == '(None)') {
          				$stream .= "<td>In Queue</td>";
          			}
          			else {
          				$stream .= "<td>" . $pieces[12] . "</td>";;
          			}	
          		} 
// Voicemail
			     if (preg_match("/!Voicemail/i", $line)) {
          			$splitqueue = explode(',',$pieces[6]); 
          			$stream .= "<td>" . $splitqueue[0] . "</td>";
          			$stream .= '<td class="icons"><img src="/sark-common/icons/arrowright.png" border=0 title = "Direction of call"></td>';
          			$stream .= "<td>Voicemail";
          			if (preg_match("/!VoicemailMain/i", $line)) {
          				$stream .= " listen";
          			}
          			else {
          				$stream .= " record";
          			}	
          			$stream .= "</td>";	
          		}   			
          		
          	}
          	$stream .= "</tr>";
		}
		$stream .= '</tbody>';
		$stream .= "</table>";	
//    syslog(LOG_WARNING, "$stream");
    echo $stream;
   	
?>

