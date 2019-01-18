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


Class sarkipblacklist {
	
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
		
	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'IP Blacklist';
	
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
	$this->myPanel->Button("restfw");
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	echo '<div class="datadivtiny">';

	echo '<table class="display" id="edswtable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('fwsource');
	$this->myPanel->aHeaderFor('description');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
		
/*** table rows ****/
	$rows = $this->helper->getTable("shorewall_blacklist");

	foreach ($rows as $row ) {
		
			echo '<tr id="' . $row['pkey'] . '">'. PHP_EOL; 	 
			echo '<td class="center">' . $row['source']  . '</td>' . PHP_EOL;		
			echo '<td >' . $row['comment']   . '</td>' . PHP_EOL;
			$get = '?delete=yes&amp;pkey='. $row['pkey'];
			$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$get);	
		
			echo '</td>' . PHP_EOL;
			echo '</tr>'. PHP_EOL;
	}

	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;	
	echo '</div>';
	
}

private function ruleDelete() {

	$sql = $this->dbh->prepare("DELETE from shorewall_blacklist WHERE pkey=?");
	$sql->execute(array($_GET['pkey']));
}


private function showNew() {
	
	$this->myPanel->msg .= "Add New blacklist Rule " ; 
	
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
	$this->myPanel->aLabelFor('source');
	echo '<input type="text" name="source" id="source" placeholder="0.0.0.0/0" />' . PHP_EOL;
	$this->myPanel->aLabelFor('comment');
	echo '<input type="text" name="comment" id="comment"  />' . PHP_EOL;
	echo '</div>';				
}

private function saveNew() {
// save the data away	
	$tuple = array();
	
	$this->validator = new FormValidator();
    $this->validator->addValidation("source",
		"regexp=/^(LAN$)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))?$/",
		"Source address looks wrong ");
	$this->validator->addValidation("comment",
		"regexp=/^[a-zA-Z0-9\(\)\.\-_\s]{2,30}$/",
		"comment maxlen=30 and can only contain characters a-zA-Z0-9().-_ and spaces");	

    //Now, validate the form
    if ($this->validator->ValidateForm()) {
				
		$tuple['source']		=  strip_tags($_POST['source']);
		$tuple['comment'] 		=  strip_tags($_POST['comment']);
		
		$ret = $this->helper->createTuple("shorewall_blacklist",$tuple,false);
		if ($ret == 'OK') {
			$this->message = "Saved new Rule!";
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

	$fwrule = null;	
	$rows = $this->helper->getTable("shorewall_blacklist");
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
