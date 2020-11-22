<?php
//
// Developed by CoCo
// Copyright (C) 2018 CoCoSoft
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
// You should have received a copy of the GNU General Public Licenses
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkNetHelperClass";

Class sarknetwork {
	
	protected $message;

	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $smtpconf = "/etc/ssmtp/ssmtp.conf";
	protected $bindaddr;
	protected $nethelper;
	protected $head = "Network";
	protected $myBooleans = array(
		'toggleDhcpElement',
		'edomainsend',
		'fqdnhttp',		
		'fqdninspect',
		'fqdnprov',
		'icmp',
		'smtpusetls',
		'smtpusestrttls'	
	);
	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->nethelper = new netHelper;
	

	$this->myPanel->pagename = 'Network Settings';
	
	if (isset($_POST['save']) || isset($_POST['endsave'])) { 
		$this->saveEdit();					
	}
	if (isset($_POST['reboot'])) { 
		$this->saveEdit();
		if ( ! $this->invalidForm) {
			unset($_SESSION['user']); 
			$this->helper->request_syscmd ("reboot");
			if (isset($_POST['ipaddr'])) {
				$this->message = "Rebooting Now, Back shortly at " . $_POST['lanipaddr'];
			}
			else {
				$this->message = "Rebooting Now, Back shortly";
			}
		}
	}

	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
		$this->helper->sysCommit();
		$this->message = "Updates Committed";	
	}	
				
	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {
	
	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	$sql = $this->dbh->prepare("SELECT fqdn,fqdnhttp,fqdninspect,fqdnprov,bindaddr,bindport,edomain,sendedomain,vcl FROM globals where pkey = ?");
	$sql->execute(array('global'));
	$global = $sql->fetchObject();
		
	$dhcp=false;
	$interface = $this->nethelper->get_interfaceName();
	if (`grep $interface /etc/network/interfaces | grep -i dhcp` ) {
		$dhcp=true;
	}
	$dhcpsrvAvail=false;
	$dhcpsrvUp=false;
	$dhcpstart = '';
	$dhcpend = '';
	if (file_exists('/etc/dnsmasq.conf')) {
		$dhcpsrvAvail=true;
		if (file_exists('/etc/dnsmasq.d/sarkdhcp-range')) {
			$dhcpsrvUp=true;
			$this->helper->request_syscmd ("chmod +r /etc/dnsmasq.d/sarkdhcp-range");
			$file = file("/etc/dnsmasq.d/sarkdhcp-range") or die("Could not read file sarkdhcp-range!");
			foreach ($file as $rec) {
				preg_match(' /^dhcp-range=(.*),(.*),.*$/ ', $rec, $matches);
				if (isset($matches[1])) {
					$dhcpstart=$matches[1];
				} 
				if (isset($matches[2])) {
					$dhcpend=$matches[2];
				}
			}
		}			
	}
	 
	$arch = trim(`/bin/uname -m`);
	
	$icmp='NO';
	$ret = $this->helper->request_syscmd ("grep '^Ping/ACCEPT' /etc/shorewall/rules");
	if (trim($ret)) {
		$icmp='YES';
	}
	
	$ipaddr = $this->nethelper->get_localIPV4();
	$netmask = $this->nethelper->get_netMask();
	$gatewayip = $this->nethelper->get_networkGw();	
	
	$hostname = `cat /etc/hostname`;

/*	
	$file = file("/etc/ntp.conf") or die("Could not read file ntp.conf!");	
	$astfile = null;
	foreach ($file as $rec) {
		if ( preg_match (" /^(server|pool)\s*(.*)$/ ",$rec,$matches)) {
			$astfile .=  $matches[2]."\n";
		}
	}	
*/	
	$ret = $this->helper->request_syscmd ("grep 'Port ' /etc/ssh/sshd_config");
	$ret = preg_replace('/<<EOT>>$/', '', $ret);
	if (preg_match (" /(\d{2,5})/ ",$ret,$matches)) {
			$sshport=$matches[1];
	}
	else {
			$sshport = 22;
	} 
	$smtpuser='';
	$smtppwd='';
	$smtphost =  `hostname`;
	$smtpusetls =  'NO';
	$smtpusestrttls =  'NO';
	$ntpservers = array();
	
	if (file_exists("/etc/ssmtp/ssmtp.conf")) {
		$file = file("/etc/ssmtp/ssmtp.conf") or die("Could not read file ssmtp.conf!");	
	
		foreach ($file as $rec) {
			if ( preg_match (" /^AuthUser=\s*(.*)$/ ",$rec,$matches)) {
				$smtpuser =  $matches[1];
			}
			if ( preg_match (" /^AuthPass=\s*(.*)$/ ",$rec,$matches)) {
				$smtppwd =  $matches[1];
			}
			if ( preg_match (" /^mailhub=\s*(.*)$/ ",$rec,$matches)) {
				$smtphost =  $matches[1];
			}
			if ( preg_match (" /^UseTLS=\s*(.*)$/ ",$rec,$matches)) {
				$smtpusetls =  $matches[1];
			}		
			if ( preg_match (" /^UseSTARTTLS=\s*(.*)$/ ",$rec,$matches)) {
				$smtpusestrttls =  $matches[1];
			}		
		}
	}
	
	$dns = array();
	$this->helper->request_syscmd ("touch /etc/resolv.dnsmasq");
	$handle = fopen("/etc/resolv.dnsmasq", "r") or die('Could not read resolv.dnsmasq!');
	while (!feof($handle)) {
		$buff = fgets($handle);		
		if (preg_match (" /^nameserver\s*(.*)$/ ",$buff,$matches)) {
			$dns[] = $matches[1];
		}
		if (count($dns) == 2) {
			break;
		}
	}
	fclose($handle);
	if (empty($dns)) {
		$dns[] = "8.8.8.8";
		$dns[] = "8.8.4.4";
	}
	$domain = `hostname -d`;
/* 
 * start page output
 */
  
	$buttonArray['reboot'] = true;
	$this->myPanel->actionBar($buttonArray,"sarknetworkForm");

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);
	
	$this->myPanel->responsiveTwoCol();


	echo '<form id="sarknetworkForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;

//ToDo
// 	if (file_exists ("/etc/ssmtp/ssmtp.conf")) {
//	}   
	
	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("IPV4 " . $this->nethelper->get_interfaceName() );
	
	if ( ! $global->VCL) {
		if ( !$dhcpsrvUp ) {
			if ( $dhcp ) {			
				$this->myPanel->displayBooleanFor('toggleDhcpElement','YES');
			}
			else {
				$this->myPanel->displayBooleanFor('toggleDhcpElement','NO');
			}
			$this->myPanel->aHelpBoxFor('dhcpaddr');		
		}
	}
	
		echo '<input type="hidden" name="bindaddr" id="bindaddr" size="20"  value="' . $global->BINDADDR . '"  />' . PHP_EOL; 
 
		echo '<div id="elementsToOperateOnDhcp">';
    
		$this->myPanel->displayInputFor('lanipaddr','text',$ipaddr);
		$this->myPanel->displayInputFor('netmask','text',$netmask);
		$this->myPanel->displayInputFor('gatewayip','text',$gatewayip);
		$this->myPanel->displayInputFor('domain','text',$domain);		
		$this->myPanel->displayInputFor('dns','text',$dns[0],"dns1");
		$this->myPanel->displayInputFor('dns','text',$dns[1],"dns2");

		echo '</div>' . PHP_EOL;

		echo '</div>';

/*
 *  identity
 */   
	
	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Identity");
//	echo '<h2 class="w3-red">Identity</h2>';

    if ( $global->BINDADDR != 'ON' ) {
		$this->myPanel->displayInputFor("hostname",'text',$hostname); 
	}

    $edomaindig = $this->nethelper->get_externip();
    if ($edomaindig) { 
	   	echo '<div id="publicip">';
    	$this->myPanel->displayInputFor("edomaindig",'text',$edomaindig); 
    	echo '</div>';
    }

    $this->myPanel->displayInputFor('bindport','number',$global['BINDPORT']);
   
    $this->myPanel->displayInputFor("edomain",'text',$global->EDOMAIN); 
    $this->myPanel->displayBooleanFor('edomainsend',$global->SENDEDOMAIN);
    $this->myPanel->displayInputFor("fqdn",'text',$global->FQDN); 
    if (!empty($global->FQDN)) {
    	$this->myPanel->displayBooleanFor('fqdnprov',$global->FQDNPROV);
    	$this->myPanel->displayBooleanFor('fqdninspect',$global->FQDNINSPECT);
    	$this->myPanel->displayBooleanFor('fqdnhttp',$global->FQDNHTTP);
    }

	echo '</div>';


	$this->myPanel->responsiveTwoColRight();

/*
 * div dhcpd
 */ 
 	
//	if ( ! $global->VCL) {
		echo '<div id="dhcp-srv">';
 		$this->myPanel->internalEditBoxStart();
 		$this->myPanel->subjectBar("IPV4 DHCP");

		if ($dhcpsrvAvail) {			
			if ( $dhcpsrvUp ) {
				$this->myPanel->displayBooleanFor('toggleDhcpd','ON');
			}
			else {
				$this->myPanel->displayBooleanFor('toggleDhcpd','OFF');
			}
    		$this->myPanel->aHelpBoxFor('dhcpserver');
			echo '<div id="elementsToOperateOnDhcpD">';
			$this->myPanel->displayInputFor("dhcpstart",'text',$dhcpstart);
			$this->myPanel->displayInputFor("dhcpend",'text',$dhcpend);		
			echo '</div>';	
		}
		echo '</div>';
		echo '</div>';
//	}

	
/*
 *		TAB Network IPV6
 */

		$ipv6array = $this->nethelper->get_IPV6ALL();

		if (!empty($ipv6array)) {
			$this->myPanel->internalEditBoxStart();
			$this->myPanel->subjectBar("IPV6 " . $this->nethelper->get_interfaceName() );

			echo '<div id="ipv6elements">';
    	
    		$type = 'IPV6GUA';
    		foreach($ipv6array as $row) {
    			$row = trim($row);
    			$elements = explode(' ',$row);
    			if (preg_match(' /^fe80/ ', $elements[1])) {
    				$type = 'IPV6LLA'; 
    			} 
    			if (preg_match(' /^fd/ ', $elements[1])) {
    				$type = 'IPV6ULA'; 
    			} 
    			$this->myPanel->displayInputFor($type,'text',$elements[1]);
    		}
    		echo '</div>';
    		echo '</div>';
    	}
/*
 *  SMTP
 */  
	
	if (file_exists("/etc/ssmtp/ssmtp.conf")) {
		
		$this->myPanel->internalEditBoxStart();
		$this->myPanel->subjectBar("SMTP");
		$this->myPanel->displayInputFor("smtphost",'text',$smtphost);
		$this->myPanel->displayInputFor("smtpuser",'text',$smtpuser);    
		$this->myPanel->displayInputFor("smtppwd",'text',$smtppwd); 
		$this->myPanel->displayBooleanFor('smtpusetls',$smtpusetls);
		$this->myPanel->displayBooleanFor('smtpusestrttls',$smtpusestrttls);  
		echo '</div>';
	}
/*
 *  NTP
 */   
	$timezone_identifiers = DateTimeZone::listIdentifiers();
	
	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("NTP");

	$timezone = trim(`/bin/cat /etc/timezone`);
	$this->myPanel->displayPopupFor('timez',$timezone,$timezone_identifiers);

	echo '<input type="hidden" name="oldtz" id="oldtz" size="20"  value="' . $timezone . '"  />' . PHP_EOL; 

// Removed jan 2019
/*
	$this->myPanel->aLabelFor('ntp-servers');
 	$this->myPanel->displayFile($astfile,"astfile"); 
	$this->myPanel->aHelpBoxFor('ntp-servers'); 
*/
	echo '</div>';

/*
 *  Access
 */   
	
	$this->myPanel->internalEditBoxStart();
	$this->myPanel->subjectBar("Access");
	$this->myPanel->displayBooleanFor('icmp',$icmp);  
	$this->myPanel->displayInputFor("sshport",'number',$sshport);
	echo '</div>';	

//	echo '<br/>' . PHP_EOL; 
	
	$endButtonArray['save'] = "endsave";
	$this->myPanel->endBar($endButtonArray);

	echo '</form>';
	$this->myPanel->responsiveClose();   
}


private function saveEdit() {
// save the data away
//print_r ($_POST);
	$this->myPanel->xlateBooleans($this->myBooleans);
	$interface = $this->nethelper->get_interfaceName();
	$network_string = "auto lo " . $interface . "\niface lo inet loopback\n";	
	$dhcp_on_string = 
		"iface $interface inet dhcp\n".
/*
		"allow-hotplug wlan0\n". 
		"iface wlan0 inet manual\n". 
		"wpa-roam /etc/wpa_supplicant.conf\n".
*/ 
		"iface default inet dhcp\n" .
		"source /etc/network/interfaces.d/*\n";

	$cur_dhcp=false;
	if (`grep $interface /etc/network/interfaces | grep -i dhcp` ) {
		$cur_dhcp=true;
	}	

	$this->validator = new FormValidator();

	$this->validator->addValidation("sshpport","num","sshport must be numeric"); 
	$this->validator->addValidation("sshport","maxlen=5","sshport max length is 5"); 
    $this->validator->addValidation("smtpuser","alphanum","smtpuser must be alphanumeric");    
	$this->validator->addValidation("hostname","alphanum","hostname must be alphanumeric");
	
	if ( !isset($_POST['toggle'] ) ) {
		$this->validator->addValidation("lanipaddr","req","You MUST provide an ip address");
		$this->validator->addValidation("netmask","req","You MUST provide a network mask");
		$this->validator->addValidation("gatewayip","req","You MUST provide a gateway address");
		$this->validator->addValidation("dns1","req","You MUST provide at least one nameserver for correct execution of the PBX");
	}
	 	
    $this->validator->addValidation("lanipaddr",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"IP address is invalid");
    $this->validator->addValidation("netmask",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"Netmask is invalid");
	$this->validator->addValidation("gatewayip",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"Gateway IP address is invalid");	
	$this->validator->addValidation("domain",
		"regexp=/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/",
		"Domain format is invalid");
	$this->validator->addValidation("fqdn",
		"regexp=/^(([a-zA-Z0-9]|[a-zA-Z0-9][a-zA-Z0-9\-]*[a-zA-Z0-9])\.)*([A-Za-z0-9]|[A-Za-z0-9][A-Za-z0-9\-]*[A-Za-z0-9])$/",
		"FQDN format is invalid");
	$this->validator->addValidation("dhcpstart",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"IP address is invalid");
	$this->validator->addValidation("dhcpend",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"IP address is invalid");				
	
	//Now, validate the form
    if ($this->validator->ValidateForm()) {
		
		$ipaddr			= strip_tags($_POST['lanipaddr']);	
		$netmask		= strip_tags($_POST['netmask']);
		$gatewayip		= strip_tags($_POST['gatewayip']);
		$hostname 		= strip_tags($_POST['hostname']);	
		$domain 		= strip_tags($_POST['domain']);	
		$edomain 		= strip_tags($_POST['edomain']);
		$sendedomain	= strip_tags($_POST['edomainsend']);
		$bindaddr 		= strip_tags($_POST['bindaddr']);
		$fqdn 			= strip_tags($_POST['fqdn']);
		$fqdnhttp 		= strip_tags($_POST['fqdnhttp']);
		$fqdninspect 	= strip_tags($_POST['fqdninspect']);
		$fqdnprov 		= strip_tags($_POST['fqdnprov']);
		$bindport 		= strip_tags($_POST['bindport']);
		$sshport		= strip_tags($_POST['sshport']);
		$smtpuser		= strip_tags($_POST['smtpuser']);
		$smtppwd		= strip_tags($_POST['smtppwd']);
		$smtphost		= strip_tags($_POST['smtphost']);
		$smtpusetls		= strip_tags($_POST['smtpusetls']);
		$smtpusestrttls = strip_tags($_POST['smtpusestrttls']);
//		$astfile		= strip_tags($_POST['astfile']);
		$dns1			= strip_tags($_POST['dns1']);
		$dns2			= strip_tags($_POST['dns2']);
		$timez 			= strip_tags($_POST['timez']);
		$oldtz 			= strip_tags($_POST['oldtz']);
		$icmp 			= strip_tags($_POST['icmp']);

		$restartShorewall=false;

		$tuple = array();
		$tuple['pkey'] = "global";
		$tuple['edomain'] = null;

		if (filter_var($edomain, FILTER_VALIDATE_IP)) {
			$tuple['edomain'] = $edomain;
		}
		$tuple['fqdnprov'] = 'NO';
		$tuple['fqdninspect'] = 'NO';
		$tuple['fqdnhttp'] = 'NO';

		if (!empty($fqdn)) {		
			$tuple['fqdn'] = $fqdn;
			$sname = 'ServerName ' . $fqdn;
			`echo $sname > /opt/sark/etc/apache2/sark_includes/sarkServerName.conf`;
			$tuple['sendedomain'] = $sendedomain;
			if (isset($fqdnprov)) {
				$tuple['fqdnprov'] = $fqdnprov;
			}
			if (isset($fqdninspect)) {
				$tuple['fqdninspect'] = $fqdninspect;
			}
			if (isset($fqdnhttp)) {
				$tuple['fqdnhttp'] = $fqdnhttp;
				$this->doHttpFilter($fqdnhttp,$fqdn);
			}									
		}
		else {
			`echo 'ServerName sark.local' > /opt/sark/etc/apache2/sark_includes/sarkServerName.conf`;
		}
		

		if (isset($bindport)) {
			$tuple['bindport'] = $bindport;
			$restartShorewall=true;
		}		

		$ret = $this->helper->setTuple("globals",$tuple);
		
		if (isset($_POST['dhcpstart'])) {
			$dhcpstart		= strip_tags($_POST['dhcpstart']);
		}
		if (isset($_POST['dhcpend'])) {		
			$dhcpend		= strip_tags($_POST['dhcpend']);
		}
			
		$cur_ipaddr = $this->nethelper->get_localIPV4();
		$cur_netmask = $this->nethelper->get_netMask();
		$cur_gatewayip = $this->nethelper->get_networkGw();

// Removed Jan 2019			
/*		
		$file = file("/etc/ntp.conf") or die("Could not read file $pkey !");	
		$cur_astfile = null;
		foreach ($file as $rec) {
			if ( preg_match (" /^server\s*(.*)$/ ",$rec,$matches)) {
				$cur_astfile .=  $matches[1]."\n";
			}
		}		
*/	
		$ret = $this->helper->request_syscmd ("grep Port /etc/ssh/sshd_config");
		$ret = preg_replace('/<<EOT>>$/', '', $ret);
		if (preg_match (" /(\d{2,5})/ ",$ret,$matches)) {
			$cur_sshport=$matches[1];
		}
		else {
			$cur_sshport = 22;
		} 
	
/*
 * update the files
 */ 

		$ret='OK';
		$reboot=false;
		$restartDnsmasq=false;

/*
 * deal with hosts & domains
 */
		$update_hosts = false;
		$cur_hostname = `hostname`;
		$cur_hostname = trim ($cur_hostname);
		$cur_domain = `hostname -d`;
		$cur_domain = trim ($cur_domain);
								
		if (isset($hostname) && $hostname != $cur_hostname) {
			$cur_hostname = $hostname;
			$update_hosts = true;
		}		
		$hosts_string = $cur_hostname;	
		if ($domain != $cur_domain) {	
			$update_hosts = true;		
/*
 * set forced server for dnsmasq
 */ 
			if ($domain) {			
				$cur_domain = $domain;				
				$hosts_string .= '.' . $cur_domain . ' '  . $hostname;
				$myret = $this->helper->request_syscmd ("sed -i '/address=/c address=/$cur_domain/127.0.0.1' /etc/dnsmasq.d/sarkdns");
				$myret = $this->helper->request_syscmd ("sed -i '/search/c search $cur_domain' /etc/dnsmasq.d/sarkdns");			
			}
			else {
				$myret = $this->helper->request_syscmd ("sed -i '/address=/c address=/nodomain.local/127.0.0.1' /etc/dnsmasq.d/sarkdns");
				$myret = $this->helper->request_syscmd ("sed -i '/search/c search nodomain.local' /etc/resolv.conf");
			}
		}							  
/*
 * repair broken hosts file
 */ 		
		exec( "grep '127.0.0.1' /etc/hosts", $out, $retcode );
		if ($retcode) {
			$myret = $this->helper->request_syscmd ("sed -i '1i127.0.0.1 localhost' /etc/hosts");
			$reboot=true;	
		}
		exec( "grep '127.0.1.1' /etc/hosts", $out, $retcode );
		if ($retcode) {
			$myret = $this->helper->request_syscmd ("sed -i '1a127.0.1.1 $cur_hostname' /etc/hosts");	
			$reboot=true;
		}
/*
 * update hosts
 */ 			
 		
		if ( $bindaddr != 'ON' ) {
			if ($update_hosts) {				
				$myret = $this->helper->request_syscmd ("/bin/echo $cur_hostname > /etc/hostname");
				$this->helper->request_syscmd ("sed -i '/127.0.1.1/c 127.0.1.1 $hosts_string' /etc/hosts");
				$reboot=true;
//				echo "REBOOT 1 hostname is " . $hostname . " and curr_hostname is " . $cur_hostname . "\n";
			}
		}	
/*
 * set ssh port
 */ 		
		if ($cur_sshport != $sshport) {
			$myret=$this->helper->request_syscmd ("/bin/sed -i 's/^Port [0-9][0-9]*/Port $sshport/' /etc/ssh/sshd_config");
//			$myret=$this->helper->request_syscmd ("/usr/sbin/service ssh restart");
			$reboot=true;
		}
/*
 * set ICMP rules
 */
		
		$cur_icmp='NO';
		$myret = $this->helper->request_syscmd ("grep '^Ping/ACCEPT' /etc/shorewall/rules");
		if (trim($myret)) {
			$cur_icmp='YES';
		}
		if ($cur_icmp != $icmp) {
			if ($icmp == 'NO') {
				$myret=$this->helper->request_syscmd ("sed -i 's|^Ping/ACCEPT|Ping/REJECT|' /etc/shorewall/rules");				
			}
			else {
				$myret=$this->helper->request_syscmd ("sed -i 's|^Ping/REJECT|Ping/ACCEPT|' /etc/shorewall/rules");	
			}
			$restartShorewall = true;
		}
		
		$rewrite = false;
		if ( isset($_POST['toggle']) ) {
			if (!$cur_dhcp) {			
				$network_string .= $dhcp_on_string;
				$rewrite = true;
				if (file_exists("/etc/dnsmasq.d/sarkdhcp-range")) {
					$myret=$this->helper->request_syscmd ("rm -rf /etc/dnsmasq.d/sarkdhcp-range");
					$restartDnsmasq = true;
					$this->helper->request_syscmd ("sed -i '/udp 53/d' /etc/shorewall/sark_rules");
					$restartShorewall = true;
				}
			}
		}
		else {
			if ($ipaddr != $cur_ipaddr || $netmask != $cur_netmask || $gatewayip != $cur_gatewayip || $cur_dhcp) {				
				$network_string .= "iface $interface inet static \n";
				$network_string .= "\taddress " . $ipaddr . "\n";
				$network_string .= "\tnetmask " . $netmask . "\n";
				$network_string .= "\tbroadcast " . $this->nethelper->get_networkBrd() . "\n";
				$network_string .= "\tgateway " . $gatewayip . "\n";
// Included for multiple NICs
				$network_string .= "source /etc/network/interfaces.d/*\n";	
				$rewrite = true;
				$dhcpDnssrv = 'dhcp-option=option:dns-server,' . $ipaddr;
				$this->helper->request_syscmd ("echo $dhcpDnssrv > /etc/dnsmasq.d/sarkdhcp-dnssrv");
				$restartDnsmasq = true; 
				$this->helper->request_syscmd ("tail /etc/resolv.conf > /etc/resolv.conf");
			}
			
			$this->helper->request_syscmd ("touch /etc/resolv.dnsmasq");
			$this->helper->request_syscmd ("tail  /etc/resolv.dnsmasq > /etc/resolv.dnsmasq");
			if ($dns1) {
				$this->helper->request_syscmd ("echo nameserver $dns1 >> /etc/resolv.dnsmasq");
			}
			if ($dns2) {
				$this->helper->request_syscmd ("echo nameserver $dns2 >> /etc/resolv.dnsmasq");
			}
			
			$cur_dhcpstart=null;
			$cur_dhcpend=null;
			if ( isset($_POST['dhcpdtoggle']) ) {
				if (file_exists("/etc/dnsmasq.d/sarkdhcp-range")) {
					$file = file("/etc/dnsmasq.d/sarkdhcp-range") or die("Could not read file sarkdhcp-range!");
					foreach ($file as $rec) {
						preg_match(' /^dhcp-range=(.*),(.*),.*$/ ', $rec, $matches);
						if (isset($matches[1])) {
							$cur_dhcpstart=$matches[1];
						} 
						if (isset($matches[2])) {
							$cur_dhcpend=$matches[2];
						}
					}
				}
								
				if ( isset($_POST['dhcpstart']) && isset($_POST['dhcpend']) ) {
					if ($dhcpstart != $cur_dhcpstart || $dhcpend != $cur_dhcpend) {
						preg_match('/\.(\d{1,3})$/', $dhcpend, $matches);
						$endLast3 = $matches[1];
						preg_match('/\.(\d{1,3})$/', $dhcpstart, $matches);
						$startLast3 = $matches[1];
						if ($startLast3 < $endLast3) {
							$dhcpRange='dhcp-range=' . $dhcpstart . ',' . $dhcpend . ',12h';
							$this->helper->request_syscmd ("echo $dhcpRange > /etc/dnsmasq.d/sarkdhcp-range");
							$dhcpRouter = 'dhcp-option=3,' . $gatewayip;
							$this->helper->request_syscmd ("echo $dhcpRouter > /etc/dnsmasq.d/sarkdhcp-router");
							$dhcpDomain = 'dhcp-option=15,' . $domain;
							$this->helper->request_syscmd ("echo $dhcpDomain > /etc/dnsmasq.d/sarkdhcp-domain");
							exec( "grep 'udp 53' /etc/shorewall/sark_rules", $out, $retcode );
							if ($retcode) {
								`/bin/echo 'ACCEPT net:\$LAN \$FW udp 53' >> /etc/shorewall/sark_rules`;
								$restartShorewall=true;
							} 	
							$restartDnsmasq = true;
						}
						else {
							$ret = "DHCP start address higher than end address!";
							echo $startlast3 . " start\n";
							echo $endlast3 . " end\n";
						}						
					}
				}			
			}
			else {
				if (file_exists("/etc/dnsmasq.d/sarkdhcp-range")) {
					$this->helper->request_syscmd ("rm -rf /etc/dnsmasq.d/sarkdhcp-range");
					$restartDnsmasq = true;
					$this->helper->request_syscmd ("sed -i '/udp 53/d' /etc/shorewall/sark_rules");
					$restartShorewall = true;
				}
			}												
		}

		if (file_exists ("/etc/ssmtp/ssmtp.conf")) {
			$this->helper->request_syscmd ("chmod 777 /etc/ssmtp/ssmtp.conf");
			$fh = fopen("/etc/ssmtp/ssmtp.conf", 'w') or die('Could not open ssmtp.conf file!');
			$output = "hostname=" . `hostname`;
			$output .= "FromLineOverride=YES" . PHP_EOL;
			$output .= "mailhub=" . $smtphost . PHP_EOL;
			if ($smtpuser) {
				$output .= "AuthUser=" . $smtpuser . PHP_EOL;
				if ($smtppwd) {
					$output .= "AuthPass=" . $smtppwd . PHP_EOL;
				}
			}
			$output .= "UseTLS=" . $smtpusetls . PHP_EOL;
			$output .= "UseSTARTTLS=" . $smtpusestrttls . PHP_EOL;		
			fwrite($fh,$output) or die('Could not write to ssmtp.conf file');
			fclose($fh);
			$this->helper->request_syscmd ("chmod 664 /etc/ssmtp/ssmtp.conf");
		}				

// Removed Jan 2019.  
/*		
		$ntpservers = explode("\n", $astfile);
		if (!empty($ntpservers)) {
			$this->helper->request_syscmd ("sed -i '/^server/d' /etc/ntp.conf");
			foreach ($ntpservers as $ntp) {
				if ($ntp) {
					$trimntp = trim($ntp);
					$this->helper->request_syscmd ("echo server $trimntp >> /etc/ntp.conf");
				}
			}
		}
*/		
		if ($timez !=  $oldtz ) {
			$distro = trim(`lsb_release -si`);
			$rlse = trim(`lsb_release -sr`);
			if ($distro == 'Ubuntu' || $rlse > 9.0) {
				$this->helper->request_syscmd('rm -rf /etc/localtime');
				$this->helper->request_syscmd('ln -s /usr/share/zoneinfo/' . $timez . ' /etc/localtime');
			}
			else  {
				$myret=$this->helper->request_syscmd ("echo $timez > /etc/timezone");
			}
			$this->helper->request_syscmd ("dpkg-reconfigure -f noninteractive tzdata");	
		}
				
		if ($rewrite) {
			$myret = $this->helper->request_syscmd ("chmod 777 /etc/network/interfaces");
			$fh = fopen("/etc/network/interfaces", 'w') or die('Could not open interface file!');
			fwrite($fh, $network_string) or die('Could not write to file');
			fclose($fh);
			$myret = $this->helper->request_syscmd ("chmod 644 /etc/network/interfaces");
//			echo "<strong> Resetting IP address now...</strong>";
			if ( $this->bindaddr != 'ON' ) {
				$myret = $this->helper->request_syscmd ("ip addr flush dev $interface && ifdown $interface && ifup $interface");
				$myret = $this->helper->request_syscmd ("php /opt/sark/generator/setip.php");
				$myret = $this->helper->request_syscmd ("sv d srk-ua-responder");
				$myret = $this->helper->request_syscmd ("sv u srk-ua-responder");
				$this->doResolv();				
			}
			else {
				$reboot=true;
			}	
				
//			return;
		}	
				
/*
 * flag errors
 */ 	
		if ($ret == 'OK') {
			$this->message = " - Updated OK!";
			
			if ($reboot) {
				$this->message .= " - Reboot required!";
			}
			else if ($restartDnsmasq) {			
				$myret = $this->helper->request_syscmd ("/etc/init.d/dnsmasq restart");
				$this->message .= " - dnsmasq restarted"; 
			}
			if ($restartShorewall) {
				$myret = $this->helper->request_syscmd ("shorewall restart");
				$this->message .= " - shorewall restarted";
			}
		}
		else {
			$this->invalidForm = True;
			$this->message = "<B>  --  Validation Errors!</B>";	
			$this->error_hash[network] = $ret;	
		}			
	}
    else {
		$this->invalidForm = True;
		$this->error_hash = $this->validator->GetErrors();
		$this->message = "<B>  --  Validation Errors!</B>";		
    }
    unset ($this->validator);
    
}
function doResolv() {
/*
 * 
 * resolv.conf
 */ 
/*
 * insert search if none present
 */
		exec( "grep 'search' /etc/resolv.conf", $out, $retcode );
		if ($retcode) {
			$myret = $this->helper->request_syscmd ("echo search nodomain.local >> /etc/resolv.conf");
		}
/* 
 * insert dnsmasq nameserver if none present
 */
		exec( "grep -m 1 'nameserver' /etc/resolv.conf | grep 127.0.0.1", $out, $retcode );
		if ($retcode) {
			$myret = $this->helper->request_syscmd ("sed -i '1inameserver 127.0.0.1' /etc/resolv.conf");
		}
			
/* 
 * set timeout if none present
 */
 		exec( "grep 'options' | grep 'timeout' /etc/resolv.conf", $out, $retcode );
		if ($retcode) {
			$myret = $this->helper->request_syscmd ("echo options timeout:2 >> /etc/resolv.conf");
		}
/* 
 * set retry if none present
 */ 			
 		exec( "grep 'options' | grep 'attempts' /etc/resolv.conf", $out, $retcode );
		if ($retcode) {
			$myret = $this->helper->request_syscmd ("echo options attempts:2 >> /etc/resolv.conf");
		}			
}	

function doHttpFilter($filterValue,$fqdn) {

	if ($filterValue == 'NO') {
		`echo "#" > /opt/sark/etc/apache2/sark_includes/preventIpAccess.conf`;
		return;
	}

//	$cur_ipaddr = $this->nethelper->get_localIPV4();
//	$quads = explode ('.',$cur_ipaddr);
//	$localcond = 'RewriteCond %{HTTP_HOST} ' . $quads[0] . '\\\.' . $quads[1] . '\\\.' . $quads[2] . '\\\.' . $quads[3];
	$localnet = $this->nethelper->get_networkIPV4();
	$localcidr = $this->nethelper->get_networkCIDR();
	$localcond = 'RewriteCond expr \"-R ' . "\'" . $localnet . '/' . $localcidr . "\'" . '\"';
	$localrule = 'RewriteRule .\* \- [L]';
	$lds = explode ('.',$fqdn);
	$regex = '!';
	foreach ($lds as $ld) {
		$regex .= $ld . '\.';
	}
	$regex = preg_replace (' /\.$/ ' , '$', $regex);
	$fqdncond = 'RewriteCond %{HTTP_HOST} ' . $regex;
	$fqdnrule = 'RewriteRule .\* \- [F]';

	`echo $localcond >  /opt/sark/etc/apache2/sark_includes/preventIpAccess.conf`;
	`echo $localrule >>  /opt/sark/etc/apache2/sark_includes/preventIpAccess.conf`;
	`echo $fqdncond >>  /opt/sark/etc/apache2/sark_includes/preventIpAccess.conf`;
	`echo $fqdnrule >>  /opt/sark/etc/apache2/sark_includes/preventIpAccess.conf`;
	return;
}	


}
