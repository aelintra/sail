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


Class sarkedsw6 {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $head = "IPV6 Firewall Rules";

public function showForm() {
//print_r($_REQUEST);		
	$this->myPanel = new page;
	$this->helper = new helper;
	$this->dbh = DB::getInstance();
	
	if (isset($_POST['new'])) { 
		$this->show();		
		return;
	}
	
	if (isset($_POST['save']) || isset($_POST['endsave'])) { 
		$this->save();
		if ($this->invalidForm) {
			$this->show();
			return;
		}			
	}	
	
	if (isset($_POST['restfw'])) { 		
		$this->restartFirewall();
		return;
	}
	
	if (isset($_GET['delete'])) { 
		$this->ruleDelete();
	}

	if (isset($_GET['msg'])) { 
		$this->message = strip_tags($_GET['msg']);
	}		

		
	$this->showMain();
	
	return;
	
}

private function showMain() {
//	print_r($_POST);

/* 
 * start page output
 */
	$buttonArray['new'] = true;
	$buttonArray['restfw'] = true;
	
	$this->myPanel->actionBar($buttonArray,"sarkedswForm",false,false);
	echo '</form>';
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup();

	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->beginResponsiveTable('edswtable',' w3-small');
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('action',true,'w3-hide'); 	
	$this->myPanel->aHeaderFor('fwsource6');
	$this->myPanel->aHeaderFor('fwdest',false,'w3-hide');
	$this->myPanel->aHeaderFor('fwproto',true,'w3-hide-small');
	$this->myPanel->aHeaderFor('fwdestports');
	$this->myPanel->aHeaderFor('fwsport',false,'w3-hide');
	$this->myPanel->aHeaderFor('fworigdest',false,'w3-hide');
	$this->myPanel->aHeaderFor('connrate',true,'w3-hide-medium w3-hide-small');
	$this->myPanel->aHeaderFor('description',true,'w3-hide-small');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
/*** table rows ****/
	$file = '/etc/shorewall6/sark_rules6';
	if (file_exists($file)) {
		$rec = file($file) or die('Could not read file!');
	}
	$pkey=1;
	foreach ($rec as $row ) {
		if (preg_match(" /^#|^\s/ ", $row)) {
			$pkey++;
			continue;			
		}
		else {
			$fwdesc = Null;
			if (preg_match(" /#/ ", $row)) {
				$splitComments = explode("#",$row);
				$fwdesc = trim($splitComments [1]);
				$elements = explode(" ",$splitComments[0]);
			}
			else {
				$elements = explode(" ",$row);
			}

			echo '<tr id="' . $pkey . '">'. PHP_EOL; 

			echo '<td class="read_only w3-hide">' . $elements[0] . '</td>' . PHP_EOL;				 
			echo '<td >' . $elements[1]  . '</td>' . PHP_EOL;		
			echo '<td class="w3-hide">' . $elements[2]  . '</td>' . PHP_EOL;
			echo '<td class="w3-hide-small">' . $elements[3]  . '</td>' . PHP_EOL;
			echo '<td >' . $elements[4]  . '</td>' . PHP_EOL;
			echo '<td class="w3-hide">' . $elements[5]  . '</td>' . PHP_EOL;
			echo '<td class="w3-hide">' . $elements[6]  . '</td>' . PHP_EOL;
			if (!empty($elements[7])) {
				echo '<td class="w3-hide-medium w3-hide-small">' . $elements[7]  . '</td>' . PHP_EOL;
			}
			else {
				echo '<td class="w3-hide-medium w3-hide-small"></td>' . PHP_EOL;
			}
			echo '<td class="w3-hide-small">' . $fwdesc  . '</td>' . PHP_EOL;
			$get = '?delete=yes&amp;pkey='. $pkey;
			$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$get);		
			echo '</tr>'. PHP_EOL;
			$pkey++;
		}
	}
	echo '</tbody>' . PHP_EOL;
	$this->myPanel->endResponsiveTable();
	echo '</form>' . PHP_EOL;
	$this->myPanel->responsiveClose();	
}

private function show() {
	    
	$buttonArray['cancel'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkedswForm",true,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	
	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

	$legend="IPV6 New Rule";
	if (isset($_GET['edit'])) {
		$legend="IPV6 Edit Rule";
	}

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar($legend);

	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	$fwsource6 = null;
	$fwproto = 'tcp';
	$fwdestports = null;
	$connrate = null;
	$description = null;


	
	$fwsource6 = strip_tags($_REQUEST['fwsource6']);
	$fwproto = strip_tags($_REQUEST['fwproto']);
	$fwdestports = strip_tags($_REQUEST['fwdestports']);
	$connrate = strip_tags($_REQUEST['connrate']);
	$description = strip_tags($_REQUEST['description']);	

	if (isset($_REQUEST['pkey'])) {
		echo '<input type="hidden" name="pkey" id="pkey" value="' . strip_tags($_REQUEST['pkey']) . '" />' . PHP_EOL;
	}
	if ($fwsource6) {
		$this->myPanel->displayInputFor('fwsource6','text',$fwsource6);
	}
	else {
		$this->myPanel->displayInputFor('fwsource6','text');
	}
	$this->myPanel->radioSlide('fwproto',$fwproto,array('tcp','udp','ALL'));
	$this->myPanel->displayInputFor('fwdestports','text',$fwdestports);
//	$this->myPanel->displayInputFor('portrangeend','number');
	$this->myPanel->displayInputFor('connrate','text',$connrate);
	$this->myPanel->displayInputFor('description','text',$description);

	echo '</div>';

	$endButtonArray['cancel'] = true;
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);
	echo '</form>' . PHP_EOL; // close the form
	$this->myPanel->responsiveClose();
				
}

private function save() {
// save the data away
	print_r($_POST);
	$tuple = array();

	$this->validator = new FormValidator();
	$this->validator->addValidation("fwsource6","req","Please fill in source");

	$this->validator->addValidation("description",
		"regexp=/^[a-zA-Z0-9\(\)\.\-_\s]{2,30}$/",
		"description maxlen=30 and can only contain characters a-zA-Z0-9().-_ and spaces");	
    $this->validator->addValidation("fwdestports","req","Please fill in Portrange");

    $this->validator->addValidation("connrate","regexp=/^\d{1,3}\/\w{3,7}:\d{1,3}$/","Connection Rate looks wrong (^\d{1,3}\/\w{3,7}:\d{1,3}$) ");
    $this->validator->addValidation("fwdestports","regexp=/^\d[\d,:]*$/","Ports look wrong - only digit strings sepatated by commas(,) or colons(:) are allowed ");
    
    //Now, validate the form
    $this->invalidForm = False;
    if (!$this->validator->ValidateForm()) {	
		$this->invalidForm = True;
	}

	$sourcepass=false;

	if (preg_match (' /^net:\[(.*)\]$/ ',$_POST['fwsource6'],$matches )) {
//		print_r($matches[1]);
//		if ( filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6 ) ) {
			$sourcepass=true;
//		}		
	}
	if (!$sourcepass) {
		$this->invalidForm = True;			
		$this->validator->error_hash['sourceval'] = 'source IP/CIDR looks wrong';
	}
	
	$sport = '-';
	$origdest = '-';
	$pkey=NULL;

    $source = strip_tags($_POST['fwsource6']);
	
	if ($this->invalidForm) {
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B> Validation Errors!</B>";
		return;		
    }
    
    if (! isset($_POST['fwproto'])) {
    	$protocol = 'tcp';
    }
    else {
    	$protocol =  strip_tags($_POST['fwproto']);
    }
	$portrange = strip_tags($_POST['fwdestports']);
	$comment 		=  strip_tags($_POST['description']);
    $source = preg_replace( ' /\// ', '\/', $source); 

	$connrate = null;
    if (!empty($_POST['connrate'])) {
    	$connrate = strip_tags($_POST['connrate']);
    	$connrate = preg_replace( ' /\// ', '\/', $connrate);
    	$sed .= " " . $connrate;
    } 
           
    $comment = null;
    if (!empty($_POST['description'])) {
    	$comment = '#' . $_POST['description'];
    }

    if (isset($_REQUEST['pkey'])) {
		$pkey = strip_tags($_POST['pkey']);
	}
    
    if (! $pkey) {
    	$sed = "/bin/sed -i '1s/^/ACCEPT $source \$FW $protocol $portrange $sport $origdest $connrate $comment \\n/' /etc/shorewall6/sark_rules6";
    }
    else {
    	$sed = "/bin/sed -i '" . $pkey . "s/.*/ACCEPT $source \$FW $protocol $portrange $sport $origdest $connrate $comment \\n/' /etc/shorewall6/sark_rules6";
    }   
    $rc = $this->helper->request_syscmd ($sed);
    
	$this->message = "- Saved (RESTART Firewall)";
	//    print_r($this->error_hash);
    unset ($this->validator);
    $this->myPanel->prg($this->message);
    exit;
}

private function ruleDelete() {
	$rc = $this->helper->request_syscmd ("/bin/sed -i '".$_GET['pkey']."d' /etc/shorewall6/sark_rules6");
}

private function restartFirewall() {


/*  
 * 	call the tuple builder to create a table row array 
 */  

//	$this->copyFirewallTemplates(); N.B. ToDo!
 
 
	$tuple = array();
	
	$rc = `sudo /sbin/shorewall6 check 2>&1`;
	$error=False;
    if (! strchr($rc, 'ERROR')) {
    	$rc = `sudo /sbin/shorewall6 restart`;
		$this->message = "Restart Success";
    }
    else {
    	$this->message = "Invalid Rule Found";
    	$error=True;
    }

	$buttonArray['return'] = "w3-text-green";
	$this->myPanel->actionBar($buttonArray,"sarkedswForm",false,false);

	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup();

	$this->myPanel->subjectBar("IPV6 Firewall Restart");
	
	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	echo '<p class="w3-container w3-margin w3-white">';
	
	if ($error) {
		echo '<span><strong class="w3-small" style="color:Red">ERROR(S) FOUND IN YOUR RULES!<br/>';
		echo 'The firewall has not been restarted. You MUST correct the error(s) and you MUST NOT reboot<br/>';
		echo 'the system until you have fixed the problem or your firewall may be disabled!<br/>';
		echo 'Error(s) are highlighted below...</strong></span><br/><br/>'; 
	} 
	$lines = explode("...", $rc);
	echo '<p class="w3-container w3-small w3-margin w3-white">';
	foreach ($lines as $line) {
		if (strchr($line, 'ERROR')) {
			echo '<span><strong>' . $line . '</strong></span><br/>';
		}
		else {
			echo $line . '<br/>';
		}
	}
	echo '</p>';
	echo '</form>';
	$this->myPanel->responsiveClose();
    			
}

private function copyFirewallTemplates() {

// check the rulesets exist 
/*
  	$rc = $this->helper->request_syscmd ("ipset -N voipbl iphash");
	$rc = $this->helper->request_syscmd ("ipset -N fqdntrust iphash");
	$rc = $this->helper->request_syscmd ("ipset -N fqdndrop iphash");
*/
	$this->dbh = DB::getInstance();
	$res = $this->dbh->query("SELECT EXTBLKLST,FQDN,FQDNINSPECT,FQDNTRUST,SIPFLOOD FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	
	$file = '/opt/sark/templates/shorewall/sark_inline_fqdn';
	if (file_exists($file) && $res['FQDNINSPECT'] == 'YES') {
		$rc = $this->helper->request_syscmd ("cp $file /etc/shorewall");
		$fqdn = $res['FQDN'];
		$rc = $this->helper->request_syscmd ("/bin/sed -i 's/\$FQDN/" . $res['FQDN'] . "/g' /etc/shorewall/sark_inline_fqdn");
	}
	else {
		$rc = $this->helper->request_syscmd ("echo '#' > /etc/shorewall/sark_inline_fqdn");	
	}

	$file = '/opt/sark/templates/shorewall/sark_inline_limit';
	if (file_exists($file) && $res['SIPFLOOD'] == 'YES') {
		$rc = $this->helper->request_syscmd ("cp $file /etc/shorewall");
	}
	else {
		$rc = $this->helper->request_syscmd ("echo '#' > /etc/shorewall/sark_inline_limit");
	}	
		
/*
 *  Tested and working but never used 
	
	$file = '/opt/sark/templates/shorewall/sark_ipset_blist';
	if (file_exists($file) && $res['EXTBLKLST'] == 'YES') {
		$rc = $this->helper->request_syscmd ("cp $file /etc/shorewall");
	}
	else {
		$rc = $this->helper->request_syscmd ("echo '#' > /etc/shorewall/sark_ipset_blist");
	}
				
	if (file_exists("/opt/sark/templates/shorewall/sark_ipset_fqdn") && $res['FQDNTRUST'] == 'YES') {
		$rc = $this->helper->request_syscmd ("cp /opt/sark/templates/shorewall/sark_ipset_fqdn /etc/shorewall/");
	}
	else {
		$rc = $this->helper->request_syscmd ("echo '#' > /etc/shorewall/sark_ipset_fqdn");
	}
	if (file_exists("/opt/sark/templates/shorewall/sark_ipset_fqdndrop")) {
		$rc = $this->helper->request_syscmd ("cp /opt/sark/templates/shorewall/sark_ipset_fqdndrop /etc/shorewall/");
	}
	else {
		$rc = $this->helper->request_syscmd ("echo '#' > /etc/shorewall/sark_ipset_fqdndrop");
	}	
*/ 			
}


}
