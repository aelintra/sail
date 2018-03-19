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
// You should have received a copy of the GNU General Public Licenseinterfaces
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//


Class sarkthreat {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $smtpconf = "/etc/ssmtp/ssmtp.conf";
	protected $bindaddr;
		
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
		
	echo '<form id="sarkthreatForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Recent Intrusion Attempts';
				
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {

	$sysloghd = 'No Entries in this Log';
	$sysloghd = file("/var/log/syslog");
	
	foreach ($sysloghd as $rec) {
		$syslog .= $rec;
	}
	echo '<div class="titlebar">' . PHP_EOL;  
	$this->myPanel->Heading();
	echo '</div>' . PHP_EOL; 
	
	
    echo '<div class="datadivwide">';
    
    echo '<table class="display" id="threattable">' ;
    echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('ThisAttemptDate');
	$this->myPanel->aHeaderFor('FirstSeenDate');
	$this->myPanel->aHeaderFor('Actor');
//	$this->myPanel->aHeaderFor('Hits');
	$this->myPanel->aHeaderFor('PROT');
	$this->myPanel->aHeaderFor('SRC');
	$this->myPanel->aHeaderFor('ASN');
	$this->myPanel->aHeaderFor('ISP');
	$this->myPanel->aHeaderFor('CC');
	$this->myPanel->aHeaderFor('Show');
		
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
//	print_r($sysloghd);
	$pkey = 1;
	foreach ($sysloghd as $row ) {
		
		if (!preg_match('/Shorewall/',$row)) {
			continue;
		}
//		print_r($row);
		
		echo '<input type="hidden" id="pkey" name="pkey" value="' . $pkey . '" />' . PHP_EOL;
		$pkey++;
		
		preg_match ('/^(\w+\s*\d+\s*\d{2}:\d{2}:\d{2})/',$row,$matches);
		$timestamp = $matches[1];
		
		
		echo '<td >' . $matches[1] . '</td>' . PHP_EOL;
		$actor = 'unknown';
		if (preg_match ('/SARKMATCH\:(\w+)/',$row,$matches)) {
			$actor = $matches[1];
		}
		
		preg_match ('/SRC=(\d+\.\d+\.\d+\.\d+)/',$row,$matches);
		$ipaddr = $matches[1];	
		$res = $this->dbh->query("SELECT * FROM threat where pkey = '" . $ipaddr . "'")->fetch(PDO::FETCH_ASSOC);
		If (empty($res['pkey'])) {
			continue;
		}
			
		echo '<td >' . $res['firstseen'] . '</td>' . PHP_EOL;
		echo '<td >' . $actor . '</td>' . PHP_EOL;
//		echo '<td >' . $res['hits'] . '</td>' . PHP_EOL;
		preg_match ('/PROTO=(UDP|TCP)/',$row,$matches);
		echo '<td >' . $matches[1] . '</td>' . PHP_EOL;
		echo '<td >' . $ipaddr . '</td>' . PHP_EOL;
		echo '<td >' . $res['asn'] . '</td>' . PHP_EOL;	
		echo '<td >' . $res['isp'] . '</td>' . PHP_EOL;
		echo '<td >' . $res['loc'] . '</td>' . PHP_EOL;
		echo '<td ><a id="inline" href="#provgen' . $pkey . '">DETAIL</a></td>' . PHP_EOL;

		$ngrep = "ngrep -I /var/log/sip.log -q -Wbyline ''  'src host " .  $ipaddr . "'" ;
// chmod for sip.log goes here		
		$expand_prov = `$ngrep`;
		echo '<div style="display:none"><div id="provgen' . $pkey . '"><div class="fancybox"><pre>' . $expand_prov . '</pre></div></div></div>'  . PHP_EOL;			 
		echo '</tr>'. PHP_EOL;
		
	}
		
	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	
/*
 *  end of site DIV
 */ 
    echo '</div>' . PHP_EOL;  
    echo '</div>' . PHP_EOL;  
}
}
