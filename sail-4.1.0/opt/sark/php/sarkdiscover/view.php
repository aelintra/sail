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
require_once "../formvalidator.php";
require_once "../AsteriskManager.php";


Class app {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $params = array('server' => '127.0.0.1', 'port' => '5038');
	protected $astrunning=false;	
	protected $error_hash = array();

public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;	
	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}	
		
	echo '<body>';
	echo '<form id="sarkdiscoverForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Discover SIP devices';
	
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
/*
 * sign on to the AMI and build a peer array
 */
		
	$sip_peers = array(); 	
	if ( $this->astrunning ) {			
		$ami = new ami($this->params);
		$amiconrets = $ami->connect();
		if ( !$amiconrets ) {
			$this->myPanel->msg .= "  (AMI Connect failed)";
		}
		else {
			$ami->login('sark','mysark');
			$amisiprets = $ami->getSipPeers();
			$sip_peers = $this->build_peer_array($amisiprets);
			$ami->logout();
		}
	}
	else {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}	

/* 
 * start page output
 */
  
	echo '<div class="buttons">';	
	echo '</div>';	
	
	$this->myPanel->Heading();
	
/*
 *  Do network discovery malarky
 */		
	$myip = $this->helper->ret_localip();
	$fvar = filter_var($myip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE);
	if ($fvar) {
		echo '<h2>Cannot perform search from a public IP address - ' . $fvar . '!</h2>' . PHP_EOL;
		return;
	}
		
	$networkstr = $this->helper->request_syscmd ( "ipcalc $myip | grep Network");
	$networkstr = preg_replace('/\s\s+/', ' ',$networkstr);
	$networkarray = explode(" ",$networkstr);
	$netcidr = explode("/", $networkarray[1]);
	$cidr = $netcidr[0];
	
/*
 *  Limit search to 8 bits, otherwise it will take forever on Class B networks
 */ 
	$cidr .= '/24';
 
	$nmapcmd= array(
	"nmap -sP -n $cidr | grep -v Host | grep -v '$myip' | awk '/Nmap scan/ {printf \$0 \" \"; getline; print $0}' | grep -i 'Snom\|Panasonic\|Yealink\|Polycom\|Cisco\|Gigaset\|Aastra\|Grandstream\|2N ' > /tmp/netfile",
	"sed -i 's/Nmap scan report for //' /tmp/netfile",
	"sed -i 's/MAC Address: //' /tmp/netfile",
	"sed -i 's/://g' /tmp/netfile",
	"sed -i 's/(//g' /tmp/netfile",
	"sed -i 's/)//g' /tmp/netfile"
	); 
	foreach ($nmapcmd as $cmd) {
		$this->helper->request_syscmd ($cmd);
	} 
	$file = file("/tmp/netfile") or die("Could not read file /tmp/netfile!");	

/*
 * output results
 */ 
		
	$tabname = 'discovertable';
	
	echo '<div class="datadivwide">';
	
	echo '<h2>Search block ' . $cidr . '</h2>' . PHP_EOL;
	
	echo '<br/>' . PHP_EOL;
	
	echo '<table class="display" id="' . $tabname . '"  >' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('ipaddr');
	$this->myPanel->aHeaderFor('macaddr'); 
	$this->myPanel->aHeaderFor('description'); 	
	$this->myPanel->aHeaderFor('extension'); 
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	foreach ($file as $row ) {
		$columns = explode(" ",$row,3); 
		echo '<td class="read_only">' . $columns[0] . '</td>' . PHP_EOL;			
		echo '<td class="read_only">' . $columns[1] . '</td>' . PHP_EOL;		
		echo '<td class="read_only">' . $columns[2] . '</td>' . PHP_EOL;
		if ($this->astrunning) {
			$target = "Unknown Peer";
			foreach ($sip_peers as $key=>$ext) {
				if ($ext['IPaddress'] == $columns[0]) {
					$target = $key;	
				}		
			}
		}
		else {
			$target = "N/A";
		}			
		echo '<td class="read_only">' . $target . '</td>' . PHP_EOL;			
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
		
}

private function build_peer_array($amirets) {
/*
 * build an array of peers by cleaning up the AMI output
 * (which contains stuff we don't want).
 */ 
	$peer_array=array();
	$lines = explode("\r\n",$amirets);	
	$peer = 0;
	foreach ($lines as $line) {
		// ignore lines that aren't couplets
		if (!preg_match(' /:/ ',$line)) { 
				continue;
		}
		
		// parse the couplet	
		$couplet = explode(': ', $line);
		
		// ignore events and ListItems
		if ($couplet[0] == 'Event' || $couplet[0] == 'ListItems') {
			continue;
		}
		
		//check for a new peer and set a new key if we have one
		if ($couplet[0] == 'ObjectName') {
			preg_match(' /^(.*)\// ',$couplet[1],$matches);
			if (isset($matches[1])) {
				$peer = $matches[1];
			}
			else {
				$peer = $couplet[1];
			}
		}
		else {
			if (!$peer) {
				continue;
			}
			else {
				$peer_array [$peer][$couplet[0]] = $couplet[1];
			}
		}
	}
	return $peer_array;	
}

}
