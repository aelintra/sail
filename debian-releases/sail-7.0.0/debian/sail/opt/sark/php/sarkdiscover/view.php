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

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/AsteriskManager.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkNetHelperClass";

Class sarkdiscover {
	
	protected $message; 
	protected $head = "Discover";
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
		

	$this->myPanel->pagename = 'Discover';
	
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
 *  Do network discovery malarky
 */	
	$file=null;
	$nethelper = new netHelper;	
	$myip = $nethelper->get_localIPV4();
	$fvar = filter_var($myip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 | FILTER_FLAG_NO_PRIV_RANGE |  FILTER_FLAG_NO_RES_RANGE);
	if ($fvar) {
		$this->error_hash['PUBLIC'] = 'Cannot perform search from a public IP address - ' . $fvar;
		$this->invalidForm = True;
	}
	else {
		$cidr=$nethelper->get_networkIPV4();
/*
 *  Limit search to 8 bits, otherwise it will take forever on Class B networks
 */ 
		$cidr .= '/24';
 		$rets=`sudo nmap -T5  -sP -n $cidr | grep -v Host | grep -v '$myip' | awk '/Nmap scan/ {printf $0 " "; getline; print $0}' > /tmp/netfile`;
 		`sed -i 's/Nmap scan report for //' /tmp/netfile`;
 		`sed -i 's/MAC Address: //' /tmp/netfile`;
 		`sed -i 's/(//g' /tmp/netfile`;
 		`sed -i 's/)//g' /tmp/netfile`;
 	
		if (file_exists("/tmp/netfile")) {
			if (filesize( "/tmp/netfile" )) {
				$file = file("/tmp/netfile") or die("Could not read file /tmp/netfile!");
			}
			else {
				$this->error_hash['SCAN'] = "  Could not read network scan output; no data.";
				$this->invalidForm = True;
			}	
		}
	}

/* 
 * start page output
 */

	$buttonArray=array();
	$this->myPanel->actionBar($buttonArray,"sarkdiscoverForm",false,false);
	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	$bigTable=true;
	$this->myPanel->responsiveSetup(2);
	echo '<form id="sarkdiscoverForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

/*
 * output results
 */ 
	
	$this->myPanel->beginResponsiveTable('discovertable',' w3-small');

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('ipaddr');
	$this->myPanel->aHeaderFor('macaddr'); 
	$this->myPanel->aHeaderFor('vendor',false,'w3-hide-small'); 
	$this->myPanel->aHeaderFor('extension'); 
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;

	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/

	foreach ($file as $row ) {

		$columns = explode(" ",$row,3);
		$short_vendor = NULL;
		$shortmac = substr($columns[1],0,8);
		$vendorline = `grep "$shortmac" /opt/sark/www/sark-common/manuf.txt`;
		$delim="\t";
		$short_vendor_cols = explode($delim,$vendorline,3);
		$short_vendor = NULL;
		if ( ! empty($short_vendor_cols[1]) ) {
			$short_vendor = $short_vendor_cols[1];
		}
		if (preg_match('/(Snom|Panasonic|Yealink|Polycom|Cisco|Gigaset|Aastra|Grandstream|2N|Vtech)/i',$short_vendor_cols[2],$matches)) {
				$short_vendor = $matches[1];
		}
		else {
			if (preg_match('/(Snom|Panasonic|Yealink|Polycom|Cisco|Gigaset|Aastra|Grandstream|2N|Vtech)/i',$short_vendor,$matches)) {
				$short_vendor = $matches[1];
			}
			else {
				continue;
			}
		}
			
		$model = '';
		$cleanmac = preg_replace('/:/','' ,$columns[1]);	
		echo '<tr>' . PHP_EOL;	
		$ip = '<a href="http://' . $columns[0] . '" target="_blank">' . $columns[0] . '</a>';				
		echo '<td class="icons">' . $ip . '</td>' . PHP_EOL;			
		echo '<td  >' . $cleanmac . '</td>' . PHP_EOL;
		echo '<td class="w3-hide-small">' . $short_vendor . '</td>' . PHP_EOL;				
		
		$sql = $this->dbh->prepare("SELECT pkey FROM ipphone where macaddr = ? COLLATE NOCASE");
		$sql->execute(array($cleanmac));
		$res = $sql->fetch();
		if (isset($res['pkey'])) {
			$target = '<a href="/php/sarkextension/main.php?edit=yes&pkey=' . $res['pkey'] . '">' . $res['pkey'] . '</a>';	
		}
		else {
			$target = '<a href="/php/sarkextension/main.php?new=yes&mac=' . $cleanmac . '&vendor=' . $short_vendor . '" >Adopt</a>';
		}					
		echo '<td class="icons">' . $target . '</td>' . PHP_EOL;			
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>';
	$this->myPanel->responsiveClose();

		
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
