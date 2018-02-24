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


Class sarklog {
	
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
		
	echo '<form id="sarklogForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'System Logs';
				
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
	$syslog=NULL;
	$astlog=NULL;
	$f2blog=NULL;
	$limit = 1000;
	
	
	$this->helper->request_syscmd ( "chmod +r /var/log/syslog" );
	$this->helper->request_syscmd ( "chmod +r /var/log/asterisk/messages" );
	$this->helper->request_syscmd ( "chmod +r /var/log/fail2ban.log" );	
	
	
	$sysloghd = 'No Entries in this Log';
	$sysloghd = file("/var/log/syslog");
	
	$astloghd = 'No Entries in this Log';	
	$astloghd = file("/var/log/asterisk/messages");
	
	$f2bloghd = 'No Entries in this Log';
	$f2bloghd = file("/var/log/fail2ban.log");
	
	$this->helper->request_syscmd ( "chmod 640 /var/log/syslog" );
	$this->helper->request_syscmd ( "chmod 660 /var/log/asterisk/messages" );
	$this->helper->request_syscmd ( "chmod 660 /var/log/fail2ban.log" );	
	
	foreach ($sysloghd as $rec) {
		$syslog .= $rec;
	}
	foreach ($astloghd as $rec) {
		$astlog .= $rec;
	}
	foreach ($f2bloghd as $rec) {
		$f2blog .= $rec;
	}	
	$sql='select * from master_audit ORDER BY pkey DESC LIMIT 200';
	$rows = $this->helper->getTable("master_audit", $sql);
	
	$this->myPanel->Heading();

    echo '<div class="datadivwide">';
    
	echo '<div id="pagetabs" >' . PHP_EOL;
    echo '<ul>'.  PHP_EOL;
    echo '<li><a href="#auditlog">Activity Log</a></li>'. PHP_EOL; 	
    echo '<li><a href="#syslog">System log</a></li>'. PHP_EOL;
    echo '<li><a href="#f2blog">Fail2Ban log</a></li>'. PHP_EOL;
    echo '<li><a href="#astlog">Asterisk Log</a></li>'. PHP_EOL; 
//    echo '<li><a href="#threatlog">Intrusions</a></li>'. PHP_EOL;  
//    echo '<li><a href="#suspects">Intruders</a></li>'. PHP_EOL;     
    echo '</ul>'. PHP_EOL;
    

	echo '<div id="syslog" >'. PHP_EOL;
	echo '<h2>System Log</h2>' . PHP_EOL;
	echo '<textarea class="longdatabox" readonly="readonly" style = "background-color: #E8E8EE" name="sysfile" id="sysfile">' . $syslog . '</textarea>' . PHP_EOL;
	echo "</div>". PHP_EOL;
	
	echo '<div id="f2blog" >'. PHP_EOL;
	echo '<h2>Fail2ban Log</h2>' . PHP_EOL;
	echo '<textarea class="longdatabox" readonly="readonly" style = "background-color: #E8E8EE" name="f2bfile" id="f2bfile">' . $f2blog . '</textarea>' . PHP_EOL;
	echo "</div>". PHP_EOL;
	
	echo '<div id="astlog" >'. PHP_EOL;
	echo '<h2>Asterisk Log</h2>' . PHP_EOL;
	echo '<textarea class="longdatabox" readonly="readonly" style = "background-color: #E8E8EE" name="astfile" id="astfile">' . $astlog . '</textarea>' . PHP_EOL;
	echo "</div>". PHP_EOL;


/*
 * Audit log table	
 */
	echo '<div id="auditlog" >'. PHP_EOL;	
	echo '<div class="datadivnarrow">';

	echo '<table class="display" id="audittable">' ;	

	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	

	$this->myPanel->aHeaderFor('Date');
	$this->myPanel->aHeaderFor('Action');
	$this->myPanel->aHeaderFor('Key');
	$this->myPanel->aHeaderFor('Relation');
	
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	

	foreach ($rows as $row ) {
		echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 
		echo '<td >' . $row['tstamp'] . '</td>' . PHP_EOL;	
		echo '<td >' . $row['act'] . '</td>' . PHP_EOL;
		echo '<td >' . $row['owner'] . '</td>' . PHP_EOL;	
		echo '<td >' . $row['relation'] . '</td>' . PHP_EOL;			 
		echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;
	echo '</div>';
	echo '</div>';
	

	
/*
 *  end of TABS DIV
 */ 
    echo '</div>' . PHP_EOL;
/*
 *  end of site DIV
 */ 
    echo '</div>' . PHP_EOL;  
    echo '</div>' . PHP_EOL;  
}
}
