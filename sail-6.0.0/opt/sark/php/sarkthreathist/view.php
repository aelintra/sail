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


Class sarkthreathist {
	
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
		
	echo '<form id="sarkthreathistForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Threat History';
				
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {

	echo '<div class="titlebar">' . PHP_EOL;  
	$this->myPanel->Heading();
	echo '</div>' . PHP_EOL; 
	
	
/*
 * Suspects summmary table	
 */

	echo '<div id="threathist" >'. PHP_EOL;	
	echo '<div class="datadivmax">';

	echo '<table id="threathisttable">' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('SRC');
	$this->myPanel->aHeaderFor('FirstSeenDate');
	$this->myPanel->aHeaderFor('LastSeenDate');
	$this->myPanel->aHeaderFor('Hits');
	$this->myPanel->aHeaderFor('ASN');
	$this->myPanel->aHeaderFor('ISP');
	$this->myPanel->aHeaderFor('CC');
		
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
	$rows = $this->helper->getTable("threat");

	foreach ($rows as $row ) {
		If (empty($rows['pkey'])) {
			continue;
		}	
		echo '<td >' . $row['pkey'] . '</td>' . PHP_EOL;	
		echo '<td >' . $row['firstseen'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['lastseen'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['hits'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['asn'] . '</td>' . PHP_EOL;	
		echo '<td >' . $row['isp'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['loc'] . '</td>' . PHP_EOL;
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
