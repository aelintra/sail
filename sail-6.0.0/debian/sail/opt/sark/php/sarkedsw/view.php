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


Class sarkedsw {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();

public function showForm() {
	
	$this->myPanel = new page;
	$this->helper = new helper;
	$this->dbh = DB::getInstance();
	
	echo '<form id="sarkedswForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">';
	
	$this->myPanel->pagename = 'IPV4 Firewall';
	
	if (isset($_POST['new_x']) || isset($_GET['new'])) { 
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
	
	if (isset($_POST['restfw_x'])) { 
//		$this->message = $this->helper->restartFirewall();			
		$this->restartFirewall();
		return;
	}
	
	if (isset($_GET['delete'])) { 
		$this->ruleDelete();
	}	

		
	$this->showMain();
	
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
	if ( $_SESSION['user']['pkey'] == 'admin' ) {
		echo '<a  href="/php/downloadpdf.php?pdf=shorewall"><img id="pdfprint" src="/sark-common/buttons/print.png" border=0 title = "Click to Download PDF" ></a>' . PHP_EOL;									
	}
	echo '</div>';	
	
	$this->myPanel->Heading();
	
	echo '<div class="datadivnarrow">';

	echo '<div id="pagetabs" class="mytabs"  >' . PHP_EOL;
	
    echo '<ul>'.  PHP_EOL;
    echo '<li><a href="#firewall">Firewall</a></li>'. PHP_EOL;
    echo '<li><a href="#advanced">Advanced</a></li>'.  PHP_EOL;
    echo '</ul>'. PHP_EOL;

	echo '<div id="firewall" >'. PHP_EOL;
	
		
	echo '<table class="display" id="edswtable" >' ;
	echo '<thead>' . PHP_EOL;	
	echo '<tr>' . PHP_EOL;
	$this->myPanel->aHeaderFor('action'); 	
	$this->myPanel->aHeaderFor('fwsource');
	$this->myPanel->aHeaderFor('fwdest');
	$this->myPanel->aHeaderFor('fwproto');
	$this->myPanel->aHeaderFor('fwdestports');
	$this->myPanel->aHeaderFor('fwsport');
	$this->myPanel->aHeaderFor('fworigdest');
	$this->myPanel->aHeaderFor('connrate');
	$this->myPanel->aHeaderFor('description');
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
				$fwdesc = $splitComments [1];
				$elements = explode(" ",$splitComments[0]);
			}
			else {
				$elements = explode(" ",$row);
			}

			echo '<tr id="' . $pkey . '">'. PHP_EOL; 
			echo '<td class="read_only">' . $elements[0] . '</td>' . PHP_EOL;				 
			echo '<td >' . $elements[1]  . '</td>' . PHP_EOL;		
			echo '<td >' . $elements[2]  . '</td>' . PHP_EOL;
			echo '<td >' . $elements[3]  . '</td>' . PHP_EOL;
			echo '<td >' . $elements[4]  . '</td>' . PHP_EOL;
			echo '<td >' . $elements[5]  . '</td>' . PHP_EOL;
			echo '<td >' . $elements[6]  . '</td>' . PHP_EOL;
			if (!empty($elements[7])) {
				echo '<td >' . $elements[7]  . '</td>' . PHP_EOL;
			}
			else {
				echo '<td ></td>' . PHP_EOL;
			}
			echo '<td >' . $fwdesc  . '</td>' . PHP_EOL;
			$get = '?delete=yes&amp;pkey='. $pkey;
			$this->myPanel->deleteClick($_SERVER['PHP_SELF'],$get);				
			echo '</td>' . PHP_EOL;
			echo '</tr>'. PHP_EOL;
			$pkey++;
		}
	}
	echo '</tbody>' . PHP_EOL;
	echo '</table>' . PHP_EOL;	

/*
 *      TAB DIVEND
 */
    echo "</div>". PHP_EOL;

#
#  firewall tab
#
	echo '<div id="advanced" >'. PHP_EOL;
	
	$global = $this->dbh->query("SELECT SIPFLOOD,EXTBLKLST,FQDN,FQDNTRUST,FQDNINSPECT FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
    
    $this->myPanel->aLabelFor('sipflood');
    $this->myPanel->selected = $global['SIPFLOOD'];
    $this->myPanel->popUp('SIPFLOOD', array('NO','YES'));  
    
    $this->myPanel->aLabelFor('extblklst');
    $this->myPanel->selected = $global['EXTBLKLST'];
    $this->myPanel->popUp('EXTBLKLST', array('NO','YES'));      

	if (isset($global['FQDN'])) {
    	$this->myPanel->aLabelFor('fqdninspect');
    	$this->myPanel->selected = $global['FQDNINSPECT'];
    	$this->myPanel->popUp('FQDNINSPECT', array('NO','YES'));
    }
    else {
    	$global['FQDNINSPECT'] = 'NO';
    }
    
    $this->myPanel->aLabelFor('fqdntrust');
    $this->myPanel->selected = $global['FQDNTRUST'];
    $this->myPanel->popUp('FQDNTRUST', array('NO','YES'));    




/*
 *      TAB DIVEND
 */
    echo "</div>". PHP_EOL;
    
#
#  end of TABS DIV
#
    echo '</div>' . PHP_EOL;
    echo '</div>' . PHP_EOL;    
	
}

private function showNew() {
	
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	}  
	else {
		$this->myPanel->msg .= "Add New IPV4 Rule " ;
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
	$this->myPanel->aLabelFor('protocol');
	$this->myPanel->popUp('protocol', array('tcp','udp'));
	$this->myPanel->aLabelFor('portrange start');
	echo '<input type="text" name="portrange1" id="portrange1"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('portrange end');
	echo '<input type="text" name="portrange2" id="portrange2"  />' . PHP_EOL;
	$this->myPanel->aLabelFor('connrate');
	echo '<input type="text" name="connrate" id="connrate"  />' . PHP_EOL;	
	$this->myPanel->aLabelFor('comment');
	echo '<input type="text" name="comment" id="comment"  />' . PHP_EOL;
	echo '</div>';				
}

private function saveNew() {
// save the data away	
	$tuple = array();
	$this->validator = new FormValidator();
	$this->validator->addValidation("source","req","Please fill in source");
/*    
	$this->validator->addValidation("source",
		"regexp=/^($LAN)|(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))$/",
		"Source address looks wrong ");	
*/
	
	$this->validator->addValidation("comment",
		"regexp=/^[a-zA-Z0-9\(\)\.\-_\s]{2,30}$/",
		"comment maxlen=30 and can only contain characters a-zA-Z0-9().-_ and spaces");	
    $this->validator->addValidation("portrange1","req","Please fill in Portrange start");
    $this->validator->addValidation("portrange1","num","Ports must be numeric");
    $this->validator->addValidation("portrange1","gt=0","Port number must be > 0");
    $this->validator->addValidation("portrange1","lt=65535","Port number must be < 65535");
    if (isset($_POST['portrange2'])) {
    	$this->validator->addValidation("portrange2","num","Ports must be numeric"); 
    	$this->validator->addValidation("portrange2","gt=0","Port number must be > 0");
    	$this->validator->addValidation("portrange2","lt=65535","Port number must be < 65535");
    }
    $this->validator->addValidation("connrate","regexp=/^\d{1,3}\/\w{3,7}:\d{1,3}$/","Connection Rate looks wrong (^\d{1,3}\/\w{3,7}:\d{1,3}$) ");
    
    //Now, validate the form
    $this->invalidForm = False;
    if (!$this->validator->ValidateForm()) {	
		$this->invalidForm = True;
	}
	
	$sport = '-';
	$origdest = '-';
    $source = strip_tags($_POST['source']);				
	$source = preg_replace( ' /^LAN/ ', '$LAN', $source);			
	if ( !preg_match(' /LAN/ ', $source)) {
    	if (!$this->valid_ip_cidr($source)) {
    		$this->invalidForm = True;			
			$this->validator->error_hash['sourceval'] = 'source IP/CIDR looks wrong';
		}
	}	    		
		
	if ($this->invalidForm) {
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B> Validation Errors!</B>";
		return;		
    }
    
    $protocol =  strip_tags($_POST['protocol']);
	$portrange = strip_tags($_POST['portrange1']);
	$comment 		=  strip_tags($_POST['comment']);
    $source = preg_replace( ' /\// ', '\/', $source);
    if (!empty($_POST['portrange2'])) {
    	$portrange .= ":" . strip_tags($_POST['portrange2']);
    }   

	$connrate = null;
    if (!empty($_POST['connrate'])) {
    	$connrate = strip_tags($_POST['connrate']);
    	$connrate = preg_replace( ' /\// ', '\/', $connrate);
    	$sed .= " " . $connrate;
    } 
           
    $comment = null;
    if (!empty($_POST['comment'])) {
    	$comment = '#' . $_POST['comment'];
    }
    
    $sed = "/bin/sed -i '1s/^/ACCEPT net:$source \$FW $protocol $portrange $sport $origdest $connrate $comment \\n/' /etc/shorewall/sark_rules";    
    $rc = $this->helper->request_syscmd ($sed);
    
	$this->message = "Saved new Rule - RESTART Firewall";
	//    print_r($this->error_hash);
    unset ($this->validator);
}

private function ruleDelete() {
	$rc = $this->helper->request_syscmd ("/bin/sed -i '".$_GET['pkey']."d' /etc/shorewall/sark_rules");
}

private function restartFirewall() {


/*  
 * 	call the tuple builder to create a table row array 
 */  
//		$this->helper->buildTupleArray($_POST,$tuple);
//		$tuple['pkey'] = 'global';
/*
 * call the setter
 */ 
//		$ret = $this->helper->setTuple("globals",$tuple);
//
/*
 *
 */	
//	$this->copyFirewallTemplates();
 
 
	$tuple = array();
	
	$rc = `sudo /sbin/shorewall check 2>&1`;
	$error=False;
    if (! strchr($rc, 'ERROR')) {
    	$rc = `sudo /sbin/shorewall restart`;
		$this->message = "RESTARTED OK";
    }
    else {
    	$this->message = "INVALID RULE FOUND";
    	$error=True;
    }
	
	if (isset($this->message)) {
		$this->myPanel->msg .= $this->message;
	}  

	echo '<div class="buttons">';
	$this->myPanel->Button("cancel");
	echo '</div>';
	    
	$this->myPanel->Heading();
	if (isset($this->message)) {	
		foreach($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}	
	echo '<br/><br/>';		
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
    			
}

private function copyFirewallTemplates() {

// check the rulesets exist 

  	$rc = $this->helper->request_syscmd ("ipset -N voipbl iphash");
	$rc = $this->helper->request_syscmd ("ipset -N fqdntrust iphash");
	$rc = $this->helper->request_syscmd ("ipset -N fqdndrop iphash");

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
 			
}

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
