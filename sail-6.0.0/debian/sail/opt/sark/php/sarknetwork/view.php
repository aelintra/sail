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
	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->nethelper = new netHelper;
	
		
	echo '<form id="sarknetworkForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Network Settings';
	$res = $this->dbh->query("SELECT BINDADDR FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$this->bindaddr = $res['BINDADDR'];
	
	if (isset($_POST['save_x'])) { 
		$this->saveEdit();					
	}
	if (isset($_POST['reboot_x'])) { 
		$this->saveEdit();
		if ( ! $this->invalidForm) {
			unset($_SESSION['user']); 
			$this->helper->request_syscmd ("reboot");
			if (isset($_POST['ipaddr'])) {
				$this->message = "Rebooting Now, Back shortly at " . $_POST['ipaddr'];
			}
			else {
				$this->message = "Rebooting Now, Back shortly";
			}
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
	
	$file = file("/etc/ntp.conf") or die("Could not read file ntp.conf!");	
	$astfile = null;
	foreach ($file as $rec) {
		if ( preg_match (" /^server\s*(.*)$/ ",$rec,$matches)) {
			$astfile .=  $matches[1]."\n";
		}
	}	
	
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
/*		
		if (preg_match (" /^domain\s*(.*)$/ ",$buff,$matches)) {	
			$domain = $matches[1];
		}
		if (preg_match (" /^search\s*(.*)$/ ",$buff,$matches)) {	
			$searchdomain = $matches[1];
		}
*/			
		if (preg_match (" /^nameserver\s*(.*)$/ ",$buff,$matches)) {
/*			
			if ($matches[1] == '127.0.0.1') {
				continue;
			}
*/
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
  
	echo '<div class="buttons">' . PHP_EOL;
	$this->myPanel->Button("save");
	$this->myPanel->Button("reboot");
	echo '</div>' . PHP_EOL;	
	
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}

    echo '<div class="datadivtabedit">';
    
	echo '<div id="pagetabs" class="mytabs">' . PHP_EOL;
    echo '<ul>'.  PHP_EOL;
	
//    if ( substr($arch, 0, 3) == 'arm' || $arch == 'ppc' || file_exists('/etc/debian_version') ) { 
	echo '<li><a href="#network">NetworkIPV4</a></li>'. PHP_EOL;
	echo '<li><a href="#network6">NetworkIPV6</a></li>'. PHP_EOL;
	if ($dhcpsrvAvail) {	
		echo '<li id="dhcp-srv"><a href="#dhcpd">DHCP Server</a></li>'. PHP_EOL;
	}
//	}
	
	if (file_exists ("/etc/ssmtp/ssmtp.conf")) {
		echo '<li><a href="#smtp">SMTP</a></li>'. PHP_EOL;
	}
    echo '<li><a href="#ntp">Timezone/NTP</a></li>'. PHP_EOL;
    
    echo '</ul>'. PHP_EOL;
    


//	if ( substr($arch, 0, 3) == 'arm' || $arch == 'ppc' || file_exists('/etc/debian_version') ) { 
		echo '<div id="network" >'. PHP_EOL;
	
		if ( !$dhcpsrvUp ) {
			if ( $dhcp ) {
				echo '<h2>'. PHP_EOL;
				echo 'Use DHCP to obtain an IP address?:&nbsp;';
				echo '<input id="toggleDhcpElement" type="checkbox" name="toggle" checked="checked" onchange="toggleDhcpStatus()">'. PHP_EOL;
				echo '</h2>'. PHP_EOL;
			}
			else {
				echo '<h2>'. PHP_EOL;
				echo 'Use DHCP to obtain an IP address?:&nbsp;';
				echo '<input id="toggleDhcpElement" type="checkbox" name="toggle" onchange="toggleDhcpStatus()">'. PHP_EOL;
				echo '</h2>'. PHP_EOL;
			}		
		}
		else {
			echo '<h2></h2>';
		}
		echo '<br/>';
    
		echo '<div id="elementsToOperateOnDhcp">';
    
		$this->myPanel->aLabelFor('ipaddr');
		echo '<input type="text" name="ipaddr" id="ipaddr" size="20"  value="' . $ipaddr . '"  />' . PHP_EOL;
		$this->myPanel->aLabelFor('netmask');
		echo '<input type="text" name="netmask" id="netmask" size="20"  value="' . $netmask . '"  />' . PHP_EOL;
		$this->myPanel->aLabelFor('gatewayip');
		echo '<input type="text" name="gatewayip" id="gatewayip" size="20"  value="' . $gatewayip. '"  />' . PHP_EOL;
		$this->myPanel->aLabelFor('domain');
		echo '<input type="text" name="domain" id="domain" size="30"  value="' . $domain . '"  />' . PHP_EOL;		
		$this->myPanel->aLabelFor('dns');	
		echo '<input type="text" name="dns1" id="dns1" size="20"  value="' . $dns[0] . '"  />' . PHP_EOL;
		$this->myPanel->aLabelFor('dns');
		echo '<input type="text" name="dns2" id="dns2" size="20"  value="' . $dns[1] . '"  />' . PHP_EOL;    
//		echo '<br/><br/>' . PHP_EOL; 
			
		echo "</div>". PHP_EOL;
				
		if ( $this->bindaddr != 'ON' ) {
			$this->myPanel->aLabelFor('localhost');
			echo '<input type="text" name="hostname" id="hostname" size="20"  value="' . $hostname . '"  />' . PHP_EOL;
		}

		$this->myPanel->aLabelFor('sshport');    
		echo '<input type="text" name="sshport" id="sshport" size="5"  value="' . $sshport . '"  />' . PHP_EOL; 
		echo '<br/>' . PHP_EOL; 
		$this->myPanel->aLabelFor('icmp'); 
		$this->myPanel->selected = $icmp;
		$this->myPanel->popUp('icmp', array('YES','NO'));    
		echo '<br/><br/>' . PHP_EOL; 
	
/*
 *      TAB DIVEND
 */
 		echo "</div>". PHP_EOL;

/*
 *		TAB Network IPV6
 */

		echo '<div id="network6" >'. PHP_EOL;
	
		
		echo '<h2>'. PHP_EOL;
		echo 'Primary Interface Name:&nbsp ' . $this->nethelper->get_interfaceName() . PHP_EOL;		
		echo '</h2>'. PHP_EOL;
    	echo '<br/>';

    	echo '<div id="ipv6elements">';
    	$ipv6array = $this->nethelper->get_IPV6ALL();
    	$type = 'IPV6GUA';
    	foreach($ipv6array as $row) {
    		$row = trim($row);
    		$elements = explode(' ',$row);
    		if (preg_match(' /^fe80/ ', $elements[1])) {
    			$type = 'IPV6LLA'; 
    		} 
    		if (preg_match(' /^fd84/ ', $elements[1])) {
    			$type = 'IPV6ULA'; 
    		} 
    		$this->myPanel->aLabelFor($type);
    		echo '<input type="text" size="40"  value="' . $elements[1] . '"  />' . PHP_EOL;
    		$type = 'IPV6GUA';
    	}
    	echo "</div>". PHP_EOL;

/*
 *      TAB DIVEND
 */
 		echo "</div>". PHP_EOL;


/*
 * div dhcpd
 */ 
 
		if ($dhcpsrvAvail) {
			echo '<div id="dhcpd" >'. PHP_EOL;	
		
		if ( $dhcpsrvUp ) {
				echo '<h2>'. PHP_EOL;
				echo 'Run DHCP Server?:&nbsp;';
				echo '<input id="toggleDhcpd" type="checkbox" name="dhcpdtoggle" checked="checked" onchange="toggleDhcpdStatus()">'. PHP_EOL;
				echo '</h2>'. PHP_EOL;
			}
			else {
				echo '<h2>'. PHP_EOL;
				echo 'Run DHCP Server?:&nbsp;';
				echo '<input id="toggleDhcpd" type="checkbox" name="dhcpdtoggle" onchange="toggleDhcpdStatus()">'. PHP_EOL;
				echo '</h2>'. PHP_EOL;
			}
    
			echo '<div id="elementsToOperateOnDhcpD">';
				
			$this->myPanel->aLabelFor('dhcpstart');
			echo '<input type="text" name="dhcpstart" id="dhcpstart" size="20"  value="' . $dhcpstart . '"  />' . PHP_EOL;
			$this->myPanel->aLabelFor('dhcpend');
			echo '<input type="text" name="dhcpend" id="dhcpend" size="20"  value="' . $dhcpend . '"  />' . PHP_EOL;
		
			echo '</div>';
		
/*
 *      TAB DIVEND
 */
			echo "</div>". PHP_EOL;		
	}
/*
 *  SMTP
 */  
	if (file_exists("/etc/ssmtp/ssmtp.conf")) {
		echo '<div id="smtp" >'. PHP_EOL;
		$this->myPanel->aLabelFor('smtphost');    
		echo '<input type="text" name="smtphost" id="smtpip" size="20"  value="' . $smtphost . '"  />' . PHP_EOL; 
		$this->myPanel->aLabelFor('user');    
		echo '<input type="text" name="smtpuser" id="smtpuser" size="20"  value="' . $smtpuser . '"  />' . PHP_EOL;    
		$this->myPanel->aLabelFor('password');    
		echo '<input type="password" name="smtppwd" id="smtppwd" size="20"  value="' . $smtppwd . '"  />' . PHP_EOL; 
		$this->myPanel->aLabelFor('UseTLS');
		$this->myPanel->selected = $smtpusetls;
		$this->myPanel->popUp('smtpusetls', array('NO','YES'));
		$this->myPanel->aLabelFor('UseSTARTTLS');
		$this->myPanel->selected = $smtpusestrttls;
		$this->myPanel->popUp('smtpusestrttls', array('NO','YES'));    
/*
 *      TAB DIVEND
 */
		echo "</div>". PHP_EOL;
	}
/*
 *  NTP
 */   
	$timezone_identifiers = DateTimeZone::listIdentifiers();
	
	echo '<div id="ntp" >'. PHP_EOL;
	
	$timezone = trim(`/bin/cat /etc/timezone`);
	$this->myPanel->aLabelFor('Timezone');
	$this->myPanel->selected = $timezone;
	$this->myPanel->popUp('timez',$timezone_identifiers); 
	echo '<input type="hidden" name="oldtz" id="oldtz" size="20"  value="' . $timezone . '"  />' . PHP_EOL; 
	echo '<br/><br/><br/>';

	$this->myPanel->aLabelFor('ntp-servers');
//	echo '<strong>NTP Servers</strong><br/><br/>';
	echo '<textarea rows="7" cols="50" name="astfile" id="astfile">' . $astfile . '</textarea>' . PHP_EOL;  
	
	
/*
 *      TAB DIVEND
 */
    echo "</div>". PHP_EOL;    
/*
 *  end of TABS DIV
 */ 
    echo '</div>' . PHP_EOL;
/*
 *  end of site DIV
 */ 
    echo '</div>' . PHP_EOL;    
}


private function saveEdit() {
// save the data away
//print_r ($_POST);

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
		$this->validator->addValidation("ipaddr","req","You MUST provide an ip address");
		$this->validator->addValidation("netmask","req","You MUST provide a network mask");
		$this->validator->addValidation("gatewayip","req","You MUST provide a gateway address");
		$this->validator->addValidation("dns1","req","You MUST provide at least one nameserver for correct execution of the PBX");
	}
	 	
    $this->validator->addValidation("ipaddr",
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
		"Domain is invalid");
	$this->validator->addValidation("dhcpstart",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"IP address is invalid");
	$this->validator->addValidation("dhcpend",
		"regexp=/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",
		"IP address is invalid");				
	
	//Now, validate the form
    if ($this->validator->ValidateForm()) {
		
		$ipaddr			= strip_tags($_POST['ipaddr']);	
		$netmask		= strip_tags($_POST['netmask']);
		$gatewayip		= strip_tags($_POST['gatewayip']);
		$hostname 		= strip_tags($_POST['hostname']);	
		$domain 		= strip_tags($_POST['domain']);			
		$sshport		= strip_tags($_POST['sshport']);
		$smtpuser		= strip_tags($_POST['smtpuser']);
		$smtppwd		= strip_tags($_POST['smtppwd']);
		$smtphost		= strip_tags($_POST['smtphost']);
		$smtpusetls		= strip_tags($_POST['smtpusetls']);
		$smtpusestrttls = strip_tags($_POST['smtpusestrttls']);
		$astfile		= strip_tags($_POST['astfile']);
		$dns1			= strip_tags($_POST['dns1']);
		$dns2			= strip_tags($_POST['dns2']);
		$icmp			= strip_tags($_POST['icmp']);
		$timez 			= strip_tags($_POST['timez']);
		$oldtz 			= strip_tags($_POST['oldtz']);
		if (isset($_POST['dhcpstart'])) {
			$dhcpstart		= strip_tags($_POST['dhcpstart']);
		}
		if (isset($_POST['dhcpend'])) {		
			$dhcpend		= strip_tags($_POST['dhcpend']);
		}
			
		$cur_ipaddr = $this->nethelper->get_localIPV4();
		$cur_netmask = $this->nethelper->get_netMask();
		$cur_gatewayip = $this->nethelper->get_networkGw();
			
		
		$file = file("/etc/ntp.conf") or die("Could not read file $pkey !");	
		$cur_astfile = null;
		foreach ($file as $rec) {
			if ( preg_match (" /^server\s*(.*)$/ ",$rec,$matches)) {
				$cur_astfile .=  $matches[1]."\n";
			}
		}		
	
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
		$restartShorewall=false;
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
 		
		if ( $this->bindaddr != 'ON' ) {
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
		
		if ($timez !=  $oldtz ) {
			$myret=$this->helper->request_syscmd ("echo $timez > /etc/timezone");
			$myret=$this->helper->request_syscmd ("dpkg-reconfigure -f noninteractive tzdata");	
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
			$this->message = "Updated settings!";
			
			if ($reboot) {
				$this->message .= " - A reboot is required...";
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


}
