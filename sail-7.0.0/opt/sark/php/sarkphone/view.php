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

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkAmiHelperClass";


Class sarkphone {
	
	protected $message; 
	protected $head = "User";
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $cdr;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $params = array('server' => '127.0.0.1', 'port' => '5038');
	protected $astrunning=false;
	protected $selection;
	protected $myBooleans = array(
		'celltwin'
	);
	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;


	if ( $this->helper->check_pid() ) {	
		$this->astrunning = true;
	}


	$this->myPanel->pagename = 'Phone';
	$res = $this->dbh->query("SELECT extension,selection FROM user WHERE pkey = '" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
	
	if (isset($res['extension']) && $res['extension'] != 'None') {
		$this->selection = $res['selection'];
	}
	else {		
		$this->myPanel->msg .= "No phone extension associated with user " . $_SESSION['user']['pkey'] . " - Contact your Administrator" . PHP_EOL;
		$this->myPanel->Heading("",$this->msg);
		exit;
	}
	
	
		
	if (isset($_GET['delete'])) {
		$id = $_SESSION['user']['pkey'];
		$this->helper->request_syscmd ( "rm $id.*" );			
		$this->helper->logit("I'm deleting file $id ",3 );
		$this->message = " - Voicemail successfully deleted!";
		if (count(glob("/var/spool/asterisk/voicemail/default/" . $_SESSION['user']['pkey'] . "/INBOX/*")) === 0) {
			$this->helper->request_syscmd ( "asterisk -rx 'sip notify clear-mwi " . $_SESSION['user']['pkey'] . "'" );
		}
	}
	
	if (isset($_POST['update'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showEdit();
			return;
		}					
	}

	$this->showEdit();
	
	$this->dbh = NULL;

	return;
	
}	

private function showEdit() {
	
	$extension = $this->dbh->query("SELECT * FROM IPphone WHERE pkey = '" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}	
		
	if (!$this->astrunning) {
		$this->myPanel->msg .= "  (No Asterisk running)";
	}

	$buttonArray=array();
	$buttonArray['dialbutton'] = True;
	$this->myPanel->actionBar($buttonArray,"sarkphoneForm",false,false,true);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head . " " . $extension['pkey'], $this->message);

	$this->myPanel->responsiveSetup(2);

	$subject = $extension['technology'] . "/" . $extension['pkey'];
	if (isset($extension['macaddr'])) {
		$subject .= " (" . $extension['macaddr'] . ")";
	}
	echo '<form id="sarkphoneForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';	
	echo '<div id="dial01" style="display:none">';
	$this->myPanel->internalEditBoxStart();

	echo '<span onclick="document.getElementById(\'dial01\').style.display=\'none\'" class="w3-button w3-right w3-tiny ">&times&nbsp;close</span>';  
	$this->myPanel->displayInputFor('keyboardDial','tel');
	echo '<div class="w3-container w3-bar w3-padding ' . $this->myPanel->bgColorClass . '" >' . PHP_EOL;
	     
    echo '<button class="w3-button w3-right w3-margin-left w3-blue w3-round-xxlarge" ';
    echo ' id="dial" name ="dial" value="Dial" type="button">Dial <i class="fas fa-phone"></i></button>'; 
	echo '</div>';
	echo '</div>';
	echo '</div>';
	
	$this->myPanel->internalEditBoxStart();
/*	
	echo '<div id="clustershow">';
	$this->myPanel->displayInputFor('cluster','text',$extension['cluster'],'cluster');
	echo '</div>'; 
*/
	$this->myPanel->displayInputFor('cellphone','tel',$extension['cellphone']);
	if ( $this->astrunning ) {
		$amiHelper = new amiHelper();
		$amiHelper->get_database($_SESSION['user']['pkey'],$cfim,$cfbs,$ringdelay,$celltwin);
		if ( $extension['cellphone'] ) {
			if ($celltwin) {
    			$this->myPanel->displayBooleanFor('celltwin','ON');
    		}
    		else {
    			$this->myPanel->displayBooleanFor('celltwin','OFF');
    		}
		}
		$this->myPanel->displayInputFor('cfim','tel',$cfim);
		$this->myPanel->displayInputFor('cfbs','tel',$cfbs);
		$this->myPanel->displayInputFor('ringdelay','number',$ringdelay);

	}
	echo '</div>';

	if ( $extension['cellphone'] ) {
		$this->myPanel->internalEditBoxStart();
		$this->myPanel->radioSlide('callbackto',$extension['callbackto'], array('desk','cell'));
		echo '</div>';
	}


	echo '<div class="w3-margin-top w3-margin-bottom">';
	$this->myPanel->aLabelFor("Voicemail");
	echo '</div>';
   	
 	//echo '<table class=display id="mailboxtable"  >' ;
 	echo '<table class="' . $this->myPanel->tableClass . ' w3-tiny" id="mailboxtable">';	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>'. PHP_EOL; 
	$this->myPanel->aHeaderFor('clidstart');
	$this->myPanel->aHeaderFor('Duration');		
	$this->myPanel->aHeaderFor('Time');	
	$this->myPanel->aHeaderFor('dl',false,'w3-hide-small w3-hide-medium');
	$this->myPanel->aHeaderFor('play');    		
	$this->myPanel->aHeaderFor('del');	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	
	$this->genMail('INBOX');
	$this->genMail('Old');
	
	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL; 

 	$servername="localhost";
	try {
		$this->cdr = new PDO("mysql:host=$servername;dbname=asterisk", 'asterisk', 'aster1sk');
    // set the PDO error mode to exception
        $this->cdr->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        syslog(LOG_WARNING, "MySQL Connected successfully"); 
    }
        catch(PDOException $e)
    {
        syslog(LOG_WARNING, "MySQL Connection failed: " . $e->getMessage() );
    }
    


   	$rets=$this->cdr->prepare("select calldate,src,dst,billsec from cdr where dst <> 's' AND src = ? ORDER BY calldate DESC LIMIT 40");
    $rets->execute(array($_SESSION['user']['pkey']));
    $outbound = $rets->fetchAll();

    $rets=$this->cdr->prepare("select calldate,src,dst,billsec from cdr where dst <> 's' AND dst = ? ORDER BY calldate DESC LIMIT 40");
    $rets->execute(array($_SESSION['user']['pkey']));
    $inbound = $rets->fetchAll();

   	echo '<div class="w3-margin-top w3-margin-bottom">';
	$this->myPanel->aLabelFor("Calls Received");
	echo '</div>';    
 	
 	//echo '<table class=display id="mailboxtable"  >' ;
 	echo '<table class="' . $this->myPanel->tableClass . ' w3-small" id="incalltable">';	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>'. PHP_EOL; 
	$this->myPanel->aHeaderFor('callfromto');	
	$this->myPanel->aHeaderFor('calltime');
	$this->myPanel->aHeaderFor('callduration');	
	$this->myPanel->aHeaderFor('callback');	
		
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	foreach($inbound as $row) {
		preg_match("/^[\d-]+\s(.*)$/", $row['calldate'],$matches);
		$rdate = $matches[1];
		if (! $row['src']) {
			$row['src'] = 'Unknown';
		}
		echo '<tr>'. PHP_EOL;
		echo '<td>' . $row['src'] . '</td>' . PHP_EOL; 
		echo '<td>' . $row['calldate'] . '</td>' . PHP_EOL;
		echo '<td>' . $row['billsec'] . '</td>' . PHP_EOL;
		echo '<td ';
		if ($row['src'] == 'Unknown') {
			echo '>X</td>';
		}
		else {
			echo 'onclick="dialBack(' . "'" . $row['src'] . "')" . '"><span class="fas fa-phone"></span></td>';
		} 		
		echo '</tr>'. PHP_EOL;
	}
	echo '</div>';	
	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL; 

	echo '<div class="w3-margin-top w3-margin-bottom">';
	$this->myPanel->aLabelFor("Calls Made");
	echo '</div>';    
 	
 	//echo '<table class=display id="mailboxtable"  >' ;
 	echo '<table class="' . $this->myPanel->tableClass . ' w3-small" id="outcalltable">';	
	echo '<thead>' . PHP_EOL;	
	echo '<tr>'. PHP_EOL; 
	$this->myPanel->aHeaderFor('callfromto');	
	$this->myPanel->aHeaderFor('calltime');
	$this->myPanel->aHeaderFor('callduration');
	$this->myPanel->aHeaderFor('callback');
			
		
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
	foreach($outbound as $row) {
		preg_match("/^[\d-]+\s(.*)$/", $row['calldate'],$matches);
		$rdate = $matches[1];
		echo '<tr>'. PHP_EOL;
		echo '<td>' . $row['dst'] . '</td>' . PHP_EOL; 
		echo '<td>' . $row['calldate'] . '</td>' . PHP_EOL;
		echo '<td>' . $row['billsec'] . '</td>' . PHP_EOL;
		echo '<td onclick="dialBack(' . "'" . $row['dst'] . "')" . '"><span class="fas fa-phone"></span></td>';

		echo '</tr>'. PHP_EOL;
	}
	echo '</div>';	
	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;   	
 
	$this->myPanel->aHelpBoxFor('callshelp');


	echo '</div>' . PHP_EOL;

    
/*
 *      TAB TABEND DIV
 */ 
   
    echo '</div>' . PHP_EOL;			
	echo '<input type="hidden" id="pkey" name="pkey" value="' . $_SESSION['user']['pkey'] . '" />' . PHP_EOL;	
	echo '</div>';
 	echo '</form>' . PHP_EOL; // close the form 
    $this->myPanel->responsiveClose();

}

private function saveEdit() {
// save the data away

	$tuple = array();

	$this->myPanel->xlateBooleans($this->myBooleans);

	$extension = $this->dbh->query("SELECT * FROM IPphone WHERE pkey = '" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
		
	$this->validator = new FormValidator();
	$this->validator->addValidation("cellphone","num","cellphone number must be numeric"); 
    $this->validator->addValidation("vmailfwd","email","Invalid email address format");  
    $this->validator->addValidation("desc","regexp=/^[0-9a-zA-Z_-]+$/","Caller name is invalid - must be [0-9a-zA-Z_-]"); 
    $this->validator->addValidation("cfim","num","Call forward must be numeric"); 
    $this->validator->addValidation("cfbs","num","Call forward must be numeric"); 
    $this->validator->addValidation("ringdelay","num","Ring Time must be numeric"); 
		
    //Now, validate the form
    if ($this->validator->ValidateForm()) {
/*
 * 	call the tuple builder to create a table row array 
 */ 
		$custom = array (
						'cfim' => True,
						'cfbs' => True,
						'ringdelay' => True,
						'keyboardDial' => True,
						'opencos' => True,
						'closedcos' => True,
						'newkey' => True,
						'vdelete' => True,
						'vreset' => True,
						'celltwin' => True
						
//						'sipiaxfriend' => True
		);
		
		$this->helper->buildTupleArray($_POST,$tuple,$custom);
		
		if ( isset($_POST['celltwin']) ) {
			$tuple['celltwin'] = True;
		}
		else {
			$tuple['celltwin'] = False;
		}
	
		$tuple['pkey'] = $_SESSION['user']['pkey'];
/*	
 * update the asterisk internal database (callforwards and ringdelay)
 */ 
		
		if ($this->astrunning) {
			$amiHelper = new amiHelper();
			$amiHelper->put_database($_SESSION['user']['pkey']);                      
        }
 		
		$ret = $this->helper->setTuple("ipphone",$tuple,$_SESSION['user']['pkey']);
		if ($ret == 'OK') {
			$this->message = "Updated!";
		}
		else {
			$this->invalidForm = True;
			$this->message = "Validation Errors!";	
			$this->error_hash['extensave'] = $ret;	
		}
		
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "Validation Errors!";		
    }
    unset ($this->validator);
}


private function genMail($mailbox) {
/*
 * gen a mailbox table
 */ 	
	$maildir = array();
	$path = "/var/spool/asterisk/voicemail/default/". $_SESSION['user']['pkey'] . "/$mailbox/";
	
	if (file_exists($path)) {
		if ($handle = opendir($path)) {
			while (false !== ($entry = readdir($handle))) {
				if ($entry != '.' && $entry != '..') {
						array_push($maildir, $entry);
				}
			}	
			closedir($handle);
		}
	}
		

	
/**** table rows ****/	
	foreach ($maildir as $file ) {
		$infoarray = array();
		$type = explode( '.', $file);
		if ($type[1] == 'txt') {
			continue;
		}
		$fname = $type[0];
		$finfofile = $path . $fname . '.txt';
		$this->getMailinfo($finfofile,$infoarray);
		$fullpath = $path . $file;
		$deletepath = $path . $fname; 		
		$rdate = date("F d Y H:i:s.", filectime($fullpath));
		$fsize = filesize($fullpath);
		echo '<input type="hidden" id="fname" name="fname" value="' . $file . '" />' . PHP_EOL;
		echo '<tr>' . PHP_EOL; 
		preg_match(' /<(\d+)>/ ',  $infoarray['callerid'] , $matches);
		echo '<td>' . $matches[1] . '</td>' . PHP_EOL;
		echo '<td>' . $infoarray['duration'] .' (sec)</td>' . PHP_EOL;
		$epoch = $infoarray['origtime'];
		$dt = new DateTime("@$epoch");
		echo '<td  class="icons">' . $dt->format('d/m/y  H:i:s') . '</td>' . PHP_EOL;
		echo '<td  class="w3-hide-medium w3-hide-small"><a href="/php/downloadg.php?dfile=' . $fullpath . '"><i class="fas fa-download"></i></a></td>' . PHP_EOL;									
		echo '<td  class="icons"><a href="/server-vmail/default/' . $_SESSION['user']['pkey'] . "/$mailbox/" . $file . '"><i class="fas fa-play"></i></a></td>' . PHP_EOL; 
		$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$deletepath);
		echo '</td>' . PHP_EOL;
		echo '</tr>'. PHP_EOL;
	}


}

private function getMailinfo($infoFile,&$infoarray) {
	$finfo = file($infoFile) or die("Could not read file $infoFile !");
	foreach ($finfo as $row) {
		// only interested in rows with = signs
		if (preg_match(' /=/ ',$row)) {
			$components = explode('=',$row);
			$infoarray[ trim($components[0]) ] = trim($components[1]);
		}		
	}
}

}
