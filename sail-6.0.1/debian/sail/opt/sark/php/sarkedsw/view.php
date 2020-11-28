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

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkNetHelperClass";

Class sarkedsw {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $nethelper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $head = "IPV4 Firewall Rules";

public function showForm() {
//print_r($_REQUEST);		
	$this->myPanel = new page;
	$this->helper = new helper;
	$this->nethelper = new netHelper;
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
/* 
 * start page output
 */
	
	$buttonArray['new'] = true;
	$buttonArray['restfw'] = true;
	$this->myPanel->actionBar($buttonArray,"sarkedswForm",false,false);

	$this->myPanel->Heading($this->head,$this->message);

	$this->myPanel->responsiveSetup(2);

	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

	$this->myPanel->beginResponsiveTable('edswtable',' w3-small');
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	
	$this->myPanel->aHeaderFor('action',true,'w3-hide'); 	
	$this->myPanel->aHeaderFor('fwsource');
	$this->myPanel->aHeaderFor('fwdest',false,'w3-hide');
	$this->myPanel->aHeaderFor('fwproto',true,'w3-hide-small');
	$this->myPanel->aHeaderFor('fwdestports');
	$this->myPanel->aHeaderFor('fwsport',false,'w3-hide');
	$this->myPanel->aHeaderFor('fworigdest',false,'w3-hide');
	$this->myPanel->aHeaderFor('connrate',true,'w3-hide-small');
	$this->myPanel->aHeaderFor('description',true,'w3-hide-small');
	$this->myPanel->aHeaderFor('del');
	
	echo '</tr>' . PHP_EOL;
	echo '</thead>' . PHP_EOL;
	echo '<tbody>' . PHP_EOL;
/*** table rows ****/
	$file = '/etc/shorewall/sark_rules';
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
				echo '<td class="w3-hide-small">' . $elements[7]  . '</td>' . PHP_EOL;
			}
			else {
				echo '<td class="w3-hide-small"></td>' . PHP_EOL;
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

	$legend="IPV4 New Rule";
	if (isset($_GET['edit'])) {
		$legend="IPV4 Edit Rule";
	}

	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar($legend);

	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	$fwsource = null;
	$fwproto = 'tcp';
	$fwdestports = null;
	$connrate = null;
	$description = null;


/*	
	$fwsource = strip_tags($_REQUEST['fwsource']);
	$fwproto = strip_tags($_REQUEST['fwproto']);
	$fwdestports = strip_tags($_REQUEST['fwdestports']);
	$connrate = strip_tags($_REQUEST['connrate']);
	$description = strip_tags($_REQUEST['description']);	
*/
	if (isset($_REQUEST['pkey'])) {
		echo '<input type="hidden" name="pkey" id="pkey" value="' . strip_tags($_REQUEST['pkey']) . '" />' . PHP_EOL;
	}
	if ($fwsource) {
		$this->myPanel->displayInputFor('fwsource','text',$fwsource);
	}
	else {
		$this->myPanel->displayInputFor('fwsource','text');
	}
	$this->myPanel->radioSlide('fwproto',$fwproto,array('tcp','udp'));
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

	$tuple = array();
	$this->validator = new FormValidator();
	$this->validator->addValidation("fwsource","req","Please fill in source");

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

	if (preg_match (' /^\$LAN$/ ',$_POST['fwsource'] )) {
		$_POST['fwsource'] = 'net:$LAN';
		$sourcepass=true;
	}
	elseif (preg_match (' /^net$/ ',$_POST['fwsource'] )) {
		$sourcepass=true;
	}
	elseif (preg_match (' /^net:\$LAN$/ ',$_POST['fwsource'] )) {
		$sourcepass=true;
	}
	elseif (preg_match (' /^net:(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/([1-9]|[1-2][0-9]|3[0-2]))$/ ',$_POST['fwsource'] )) {
		$sourcepass=true;
	}
	if (!$sourcepass) {
		$this->invalidForm = True;			
		$this->validator->error_hash['sourceval'] = 'source IP/CIDR looks wrong';
	}
	
	$sport = '-';
	$origdest = '-';
	$pkey=NULL;

    $source = strip_tags($_POST['fwsource']);
	
	if ($this->invalidForm) {
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B> Validation Errors!</B>";
		return;		
    }
    
    $protocol =  strip_tags($_POST['fwproto']);
	$portrange = strip_tags($_POST['fwdestports']);
	$comment 		=  strip_tags($_POST['description']);
    $source = preg_replace( ' /\// ', '\/', $source);
    if (!empty($_POST['portrangeend'])) {
    	$portrange .= ":" . strip_tags($_POST['portrangeend']);
    }   

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
    	$sed = "/bin/sed -i '1s/^/ACCEPT $source \$FW $protocol $portrange $sport $origdest $connrate $comment \\n/' /etc/shorewall/sark_rules";
    }
    else {
    	$sed = "/bin/sed -i '" . $pkey . "s/.*/ACCEPT $source \$FW $protocol $portrange $sport $origdest $connrate $comment \\n/' /etc/shorewall/sark_rules";
    }   
    $rc = $this->helper->request_syscmd ($sed);
    
	$this->message = "Saved  (RESTART Firewall)";
	//    print_r($this->error_hash);
    unset ($this->validator);
    $this->myPanel->prg($this->message);
    exit;
}

private function ruleDelete() {
	$rc = $this->helper->request_syscmd ("/bin/sed -i '".$_GET['pkey']."d' /etc/shorewall/sark_rules");
}

private function restartFirewall() {


/*  
 * 	call the tuple builder to create a table row array 
 */  

	$this->nethelper->copyFirewallTemplates(); 
 
 
	$tuple = array();
	
	$rc = `sudo /sbin/shorewall check 2>&1`;
	$error=False;
    if (! strchr($rc, 'ERROR')) {
    	$rc = `sudo /sbin/shorewall restart`;
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

	$this->myPanel->subjectBar("IPV4 Firewall Restart");

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
/*
private function copyFirewallTemplates() {

	$this->dbh = DB::getInstance();
	$res = $this->dbh->query("SELECT BINDPORT,FQDN,FQDNINSPECT,SIPFLOOD FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$file = '/opt/sark/templates/shorewall/sark_inline_fqdn';
	if ($res['FQDNINSPECT'] == 'YES') {
		$rule = "'INLINE(ACCEPT) net \$FW tcp ";
		$rule .= $res['BINDPORT'];
		$rule .= '; -m string --algo bm --to 100 --string "';
		$rule .= $res['FQDN'];
		$rule .= '"';
		$rule .= "'";
		$rc = $this->helper->request_syscmd ("echo $rule > /etc/shorewall/sark_inline_fqdn");

		$rule = "'INLINE(ACCEPT) net \$FW udp ";
		$rule .= $res['BINDPORT'];
		$rule .= '; -m string --algo bm --to 100 --string "';
		$rule .= $res['FQDN'];
		$rule .= '"';
		$rule .= "'";q
		$rc = $this->helper->request_syscmd ("echo $rule >> /etc/shorewall/sark_inline_fqdn");
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
		
}
*/

private function valid_ip_cidr($cidr, $must_cidr = false) {

    if (!preg_match("/^[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}(\/[0-9]{1,2})?$/", $cidr)) {
        $return = false;
    } 
    else {
        $return = true;
    }
    if ($return == true) {
        $parts = explode("/", $cidr);
        $ip = $parts[0];
        $netmask = $parts[1];
        $octets = explode(".", $ip);
        foreach ($octets as $octet) {
            if ($octet > 255) {
                $return = false;
            }
        }
        if ((($netmask != "") && ($netmask > 32) && !$must_cidr) || (($netmask == ""||$netmask > 32) && $must_cidr)) {
            $return = false;
        }
    }
    return $return;
}

}
