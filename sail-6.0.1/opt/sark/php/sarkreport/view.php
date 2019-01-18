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


Class sarkreport {
	
	protected $message; 
	protected $head = "System Reports";
	protected $myPanel;
	protected $helper;
	protected $logsize=100;

/*
	List of logfiles we'll show 
 */
	protected $pdfFiles = array(
		"Directory"			=> 'ldap',
        "Extensions"		=> 'ipphone',
        "Inbound Routes"	=> 'ddi',
        "IPV4 Firewall"		=> 'shorewall',
        "IPV6 Firewall"		=> 'shorewall6',
        "Outbound Routes"	=> 'routes',
        "Ring Groups"		=> 'groups',       
        "Tenants"			=> 'cluster',
		"Trunks"			=> 'trunks'
  	);

public function showForm() {
	
	$this->myPanel = new page;
	$this->helper = new helper;

		
	
	$this->myPanel->pagename = 'System Reports';
	$this->showMain();
	return;	
}
	
private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 	
/* 
 * start page output
 */
	$buttonArray=array();
 	$this->myPanel->actionBar($buttonArray,"sarklogForm",false,false);
	
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

//	$this->myPanel->subjectBar("System Logs");
	echo '<form id="sarklogForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	$this->myPanel->beginResponsiveTable('logtable',' w3-medium');	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
//	$this->myPanel->aHeaderFor('astfilename');
	$this->myPanel->aHeaderFor('Report Type',false);
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;	
	echo '<tbody>' . PHP_EOL;
	
/**** table rows ****/	
	foreach ($this->pdfFiles as $key => $table ) {
			echo '<tr title = "Click to View">' . PHP_EOL;		
//			echo '<td style="text-align:left; "><a href="' . $_SERVER['PHP_SELF'] . '?edit=yes&amp;pkey=' . $key . '">' . $key . '</a></td>';
			echo '<td><a href="/php/downloadpdf.php?pdf=' . $table . '" style="text-decoration:none" target="_blank">' . $key . '</a></td>' . PHP_EOL;	
			echo '</tr>'. PHP_EOL;	
	}
	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>' . PHP_EOL;
	$this->myPanel->responsiveClose();	
}



}
