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


Class sarkfqdnwlist {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();

public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
		
	echo '<form id="sarkfqdnwlistForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'Dynamic FQDNs';
	
	if (isset($_POST['new_x'])) { 
		$this->showNew();		
		return;
	}
	
	if (isset($_POST['save_x'])) { 
		$this->saveNew();
		if ($this->invalidForm) {
			$this->showNew();
			return;
		}			
	}	
	
	
	if (isset($_GET['delete'])) { 
		$this->ruleDelete();
	}	

	if (isset($_POST['restfw_x'])) { 
		if (!$this->restartFirewall() ) {
			return;
		}
	}

	$this->showMain();

	
	$this->dbh = NULL;
	return;
	
}

private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	else {
		$this->myPanel->msg = "";
	}
/* 
 * start page output
 */
  
	echo '<div class="buttons">';	
	$this->myPanel->Button("new");
//	$this->myPanel->Button("restfw");
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	echo '<div class="datadivnarrow">';

	echo '<table class="display" id="fqdnwlisttable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('fqdn');
	$this->myPanel->aHeaderFor('fqdnipaddress');
	$this->myPanel->aHeaderFor('description');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
	$rows = $this->helper->getTable("shorewall_whitelist");

	foreach ($rows as $row ) {
		
			echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 	 
			echo '<td class="center">' . $row['fqdn']  . '</td>' . PHP_EOL;		
			$str = "dig +short " . $row['fqdn'];
			$ipaddr = `$str`; 
			if (empty($ipaddr)) {
				$ipaddr = "Unresolved!";
			}
			echo '<td >' . $ipaddr   . '</td>' . PHP_EOL;
			echo '<td >' . $row['comment']   . '</td>' . PHP_EOL;
			$get = '?delete=yes&amp;pkey='. $row['pkey'] . '&amp;ipaddr=' . $ipaddr;
			$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$get);	
		
			echo '</td>' . PHP_EOL;
			echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;	
	echo '</div>';
	
}

private function ruleDelete() {

	$sql = $this->dbh->prepare("DELETE from shorewall_whitelist WHERE pkey=?");
	$sql->execute(array($_GET['pkey']));
	$rc = $this->helper->request_syscmd ("sh /opt/sark/scripts/getfqdnip.sh");
	$ipaddr = $_GET['ipaddr'];
	if ($ipaddr != "Unresolved!") {
//		$rc = $this->helper->request_syscmd ('ipset  -A fqdndrop ' . $ipaddr);
		if ( ! `grep $ipaddr /opt/sark/cache/fqdndrop` ) {
			$rc = $this->helper->request_syscmd ('echo ' . $ipaddr . ' >> /opt/sark/cache/fqdndrop' );
		}
	}
}


private function showNew() {
	
	$this->myPanel->msg .= "Add New Dynamic FQDN " ; 
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  

	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	$this->myPanel->Button("save");
	echo '</div>';
	    
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}	
		
	echo '<div class="editinsert">';
	$this->myPanel->aLabelFor('FQDN');
	echo '<input type="text" name="fqdn" id="fgdn" placeholder="some.domain.name" />' . PHP_EOL;
	$this->myPanel->aLabelFor('description');
	echo '<input type="text" name="comment" id="comment"  />' . PHP_EOL;
	echo '</div>';				
}

private function saveNew() {
// save the data away	
	$tuple = array();
	
	$this->validator = new FormValidator();

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
				
		$tuple['fqdn']		=  strip_tags($_POST['fqdn']);
		$tuple['comment'] 		=  strip_tags($_POST['comment']);
		
		$ret = $this->helper->createTuple("shorewall_whitelist",$tuple,false);
		if ($ret == 'OK') {
			$this->message = "Saved new Rule!";
			$rc = $this->helper->request_syscmd ("sh /opt/sark/scripts/getfqdnip.sh");
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash['shorewall'] = $ret;	
		}
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
}
private function restartFirewall() {

	return;
	$fwrule = null;	
	$rows = $this->helper->getTable("shorewall_whitelist");
	foreach ($rows as $row ) {
		$fwrule .= "DROP\t net:" . $row['source'] . "\tall\n";	
	}

	if ($fwrule) {
		$fh = fopen("/etc/shorewall/sark_blrules", 'w') or die('Could not open blrules file!');
		fwrite($fh, $fwrule) or die('Could not write to blrules file');
		fclose($fh);
	}
	else {
		`/usr/bin/tail /etc/shorewall/sark_blrules > /etc/shorewall/sark_blrules`;
	}
	
	$rc = `sudo /sbin/shorewall check 2>&1`;

    if (! strchr($rc, 'ERROR')) {
    	$rc = `sudo /sbin/shorewall restart`;
		$this->message = "RESTARTED OK";
		return "OK";
    }

	$this->myPanel->msg .= "BAD RULE - NO RESTART!";
/* 
 * start page output
 */
  
	echo '<div class="buttons">';	
	$this->myPanel->Button("cancel");
	echo '</div>';	
	
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}	
	echo '<br/><br/><br/><br/>';		
	echo '<div class="datadivnarrow">';
	if ($error) {
		echo '<strong style="color:Red">ERROR(S) FOUND IN YOUR RULES!<br/>';
		echo 'The firewall has not been restarted. You MUST correct the error(s) and you MUST NOT reboot<br/>';
		echo 'the system until you have fixed the problem or your firewall may be disabled!<br/>';
		echo 'Error(s) are highlighted below...</strong><br/><br/>'; 
	} 
	$lines = explode("...", $rc);
	foreach ($lines as $line) {
		if (strchr($line, 'ERROR')) {
			echo '<strong>' . $line . '</strong><br/>';
		}
		else {
			echo $line . '<br/>';
		}
	}
	echo '</div>';
	echo '</div>';
	return;			
}
}
