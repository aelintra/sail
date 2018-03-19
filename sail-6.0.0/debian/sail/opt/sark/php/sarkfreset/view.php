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

require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkLDAPHelperClass";
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkNetHelperClass";

Class sarkfreset {
	
	protected $message; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $log = NULL;
	protected $live = false;
	protected $reboot;
	
public function showForm() {
	
	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$login = false;
	
	echo '<form id="sarkfresetForm" action="' . $_SERVER['PHP_SELF'] . '" method="post">' . PHP_EOL;
	
	$this->myPanel->pagename = 'Factory Reset';
	
	if (!empty( $_POST['password'] )) {
		if ($this->helper->checkCreds( "admin",$_POST['password'],$this->message,$login )) {
			$this->reboot = false;
			$this->doReset();
			unset($_SESSION['user']); 
//			if ($this->reboot) {
				$this->message = "Rebooting now (IP may change)";			
				$this->helper->request_syscmd ("reboot");
//			}
		}		
	}
	else {
//		$this->message = "Enter administrator password to execute";
	}


	$this->showMain();
	
	$this->dbh = NULL;
	return;
	
}
	
private function showMain() {

	if (isset($this->message)) {
		$this->myPanel->msg = $this->message;
	} 
	
	$this->myPanel->Heading();
	if (isset($this->message)) {
		foreach ($this->error_hash as $inpname => $inp_err) {
			echo "<p>$inpname : $inp_err</p>\n";
		}       
	}
	
	if ($this->log) {
		echo '<div class="messagebox" >';
		echo '<div class="message" style="font-size: 2em;padding-left:10em;padding-top:2em;">';
		echo $this->log;
		echo '</div>';
		echo '</div>';
		return;
	}		

    echo '<div class="datadivtabedit">';

	echo '<div id="reset" >'. PHP_EOL;
	echo '<input id="selectall" type="checkbox" name="selectall" >'. PHP_EOL;
	echo ' :Select/Deselect all';
	echo '<br/>';
	echo '<br/>';	
	echo '<h2>'. PHP_EOL;
	
	echo '<input class="resetcheck" id="resetdb" type="checkbox" name="resetdb" >'. PHP_EOL;
	echo ' :Reset PBX Database?';
	echo '<br/>';
	echo '<input class="resetcheck" id="backups" type="checkbox" name="backups" >'. PHP_EOL;
	echo ' :Delete backups?';				
	echo '<br/>';
	echo '<input class="resetcheck" id="snaps" type="checkbox" name="snaps" >'. PHP_EOL;
	echo ' :Delete snapshots?';				
	echo '<br/>';
	echo '<input class="resetcheck" id="usergreets" type="checkbox" name="usergreets" >'. PHP_EOL;
	echo ' :Delete greetings?';				
	echo '<br/>';
	echo '<input class="resetcheck" id="vmail" type="checkbox" name="vmail" >'. PHP_EOL;
	echo ' :Delete voicemail?';				
	echo '<br/>';
	echo '<input class="resetcheck" id="vrec" type="checkbox" name="vrec" >'. PHP_EOL;
	echo ' :Delete recordings?';				
	echo '<br/>';		
	echo '<input class="resetcheck" id="cdrs" type="checkbox" name="cdrs" >'. PHP_EOL;
	echo ' :Delete CDRs?';				
	echo '<br/>';
	echo '<input class="resetcheck" id="logs" type="checkbox" name="logs" >'. PHP_EOL;
	echo ' :Delete logs?';				
	echo '<br/>';
	echo '<input class="resetcheck" id="firewall" type="checkbox" name="firewall" >'. PHP_EOL;
	echo ' :Reset firewall rules to default?';				
	echo '<br/>';
	echo '<input class="resetcheck" id="dhcp" type="checkbox" name="dhcp" >'. PHP_EOL;
	echo ' :Reset network to defaults (DHCP)?';				
	echo '<br/>';	
	echo '<input class="resetcheck" id="host" type="checkbox" name="host" >'. PHP_EOL;
	echo ' :Reset hostname and domain to default?';				
	echo '<br/>';		
	echo '<input class="resetcheck" id="sshport" type="checkbox" name="sshport" >'. PHP_EOL;
	echo ' :Reset ssh port to default?';				
	echo '<br/>';	
/*
	echo '<input class="resetcheck" id="recs" type="checkbox" name="recs" >'. PHP_EOL;
	echo ' :Delete voice recordings?';				
	echo '<br/>';
*/
	echo '<input class="resetcheck" id="ldap" type="checkbox" name="ldap" >'. PHP_EOL;
	echo ' :Delete LDAP directory entries?';				
	echo '<br/>';																													
	echo '</h2>'. PHP_EOL;

    echo '</div>' . PHP_EOL;
    
    echo '<br/><br/>';
    echo '<div id="container">' . PHP_EOL;
    echo '<input type="password" id="password" name="password" placeholder="Enter Admin Password"> ' . PHP_EOL;       
    echo '<div id="lower"> ' . PHP_EOL;   	
    echo '</div>' . PHP_EOL; 
    echo '<br/><br/>';
    echo '<input type="submit" value="RESET"> '. PHP_EOL;                     

    echo '</div>' . PHP_EOL;      

    echo '</div>' . PHP_EOL;    
}


private function doReset() {

	$logs = array(
		"apache2/access.log",
		"apache2/error.log",
		"apache2/other_vhosts_access.log",
		"apache2/ssl_access.log",				
		"asterisk/messages",
		"asterisk/queue_log",
		"auth.log",		
		"fail2ban.log",	
		"mail.err",
		"mail.log",
		"mail.info",
		"mail.warn",
		"messages",
		"mysql.log",
		"srkhelper.log",				
		"shorewall-init.log",				
		"syslog",
		"user.log",
		"wtmp"
	);

//	return;
	$nethelper = new netHelper;	
	$interface = $nethelper->get_interfaceName();	
	$dhcp_reset_string = 
		"auto lo " . $interface . "\niface lo inet loopback\n".
		"iface $interface inet dhcp\n".
/*
		"allow-hotplug wlan0\n". 
		"iface wlan0 inet manual\n". 
		"wpa-roam /etc/wpa_supplicant.conf\n".
*/ 
		"iface default inet dhcp\n" .
		"source /etc/network/interfaces.d/*\n";
/*
 * this doesn't get used
 */				
	$hosts_reset_string = 
		"127.0.0.1 localhost\n" .
		"127.0.1.1 s200" .
		"127.0.0.1 debian";
	
	if ( isset($_POST['resetdb'] ) ) {
			$this->helper->request_syscmd ("mv /opt/sark/db/sark.db /opt/sark/db/sark.db.insurance");
			$this->helper->request_syscmd ("rm -rf /opt/sark/oncedone/*");
			$this->helper->request_syscmd ("sh /opt/sark/scripts/srkV4reloader.sh");
		$this->log .= "<p>database RESET</p>";
	}
	else {
		$this->log .= "<p>database PRESERVED</p>";	
	}
	if ( isset($_POST['backups'] ) ) {
			$this->helper->request_syscmd ("rm -rf /opt/sark/bkup/*");
		$this->log .= "<p>backups DELETED</p>";
	}
	else {
		$this->log .= "<p>backups PRESERVED</p>";	
	}	
	if ( isset($_POST['snaps'] ) ) {
			$this->helper->request_syscmd ("rm -rf /opt/sark/snap/*");
		$this->log .= "<p>snaps DELETED</p>";
	}
	else {
		$this->log .= "<p>snaps PRESERVED</p>";	
	}		 			
	if ( isset($_POST['usergreets'] ) ) {
			$this->helper->request_syscmd ("rm -rf /usr/share/asterisk/sounds/usergreeting*");
		$this->log .= "<p>greetings DELETED</p>";
	}
	else {
		$this->log .= "<p>greetings PRESERVED</p>";	
	}	
	if ( isset($_POST['vmail'] ) ) {
			$this->helper->request_syscmd ("rm -rf /var/spool/asterisk/voicemail/default/*");
		$this->log .= "<p>voicemail DELETED</p>";
	}
	else {
		$this->log .= "<p>voicemail PRESERVED</p>";	
	}	
	if ( isset($_POST['vrec'] ) ) {
			$this->helper->request_syscmd ("rm -rf /var/spool/asterisk/monitor/*");
			$this->helper->request_syscmd ("rm -rf /var/spool/asterisk/monout/*");
			$this->helper->request_syscmd ("rm -rf /var/spool/asterisk/monstage/*");
		$this->log .= "<p>recordings DELETED</p>";
	}
	else {
		$this->log .= "<p>recordings PRESERVED</p>";	
	}				
	if ( isset($_POST['cdrs'] ) ) {
			$this->helper->request_syscmd ("cat /dev/null > /var/log/asterisk/cdr-csv/Master.csv");
		$this->log .= "<p>CDRs DELETED</p>";
	}
	else {
		$this->log .= "<p>CDRs PRESERVED</p>";	
	}	
	if ( isset($_POST['logs'] ) ) {
		foreach ($logs as $log) {
			$this->helper->request_syscmd ("cat /dev/null > /var/log/$log");
			$this->helper->request_syscmd ('rm -rf /var/log/' . $log . '.*');
		}
		$this->log .= "<p>logs DELETED</p>";
	}
	else {
		$this->log .= "<p>logs PRESERVED</p>";	
	}		
	if ( isset($_POST['firewall'] ) ) {
			$this->helper->request_syscmd ("cp -a /opt/sark/cache/sark_rules_reset /etc/shorewall/sark_rules");
			$this->helper->request_syscmd ("sed -i 's|^Ping/REJECT|Ping/ACCEPT|' /etc/shorewall/rules");
			$sql = $this->dbh->prepare("DELETE from shorewall_blacklist");
			$sql->execute();
			$this->helper->request_syscmd ("shorewall restart");
		$this->log .= "<p>firewall RESET</p>";
		$this->log .= "<p>firewall RESTARTED</p>";
	}
	else {
		$this->log .= "<p>firewall PRESERVED</p>";	
	}			
	if ( isset($_POST['dhcp'] ) ) {
			$this->helper->request_syscmd ("echo $dhcp_reset_string > /etc/network/interfaces");
			$this->helper->request_syscmd ('echo "nameserver 8.8.8.8" > /etc/resolv.dnsmasq');
			$this->helper->request_syscmd ('echo "nameserver 8.8.4.4" > /etc/resolv.conf');
			$this->helper->request_syscmd ("rm -rf /etc/dnsmasq.d/sarkdhcp-range");
			$this->helper->request_syscmd ('echo "resolv-file=/etc/resolv.dnsmasq" > /etc/dnsmasq.d/sarkdns');
			
			$this->helper->request_syscmd ("cat /dev/null > /etc/dnsmasq.d/sarkdhcp-router");
			$this->helper->request_syscmd ("cat /dev/null > /etc/dnsmasq.d/sarkdhcp-domain");
			$this->helper->request_syscmd ("cat /dev/null > /etc/dnsmasq.d/sarkdhcp-dnssrv");
			
			$this->helper->request_syscmd ("cp -a /opt/sark/cache/ssmtp-reset.conf /etc/ssmtp/ssmtp.conf");
			$this->helper->request_syscmd ("cp -a /opt/sark/cache/revaliases-reset /etc/ssmtp/revaliases");	
			$this->helper->request_syscmd ("cp -a /opt/sark/cache/ntp-reset.conf /etc/ntp.conf");						
			$this->helper->request_syscmd ("cat /dev/null > /etc/resolv.conf");
		$this->log .= "<p>network RESET</p>";
		$this->reboot = true;	
	}
	else {
		$this->log .= "<p>network PRESERVED</p>";	
	}		
	if ( isset($_POST['host'] ) ) {
			$this->helper->request_syscmd ("echo sark > /etc/hostname");
		$this->log .= "<p>hostname RESET</p>";
		$this->reboot = true;
	}
	else {
		$this->log .= "<p>hostname PRESERVED</p>";	
	}	
	if ( isset($_POST['sshport'] ) ) {
			$this->helper->request_syscmd ("/bin/sed -i 's/^Port [0-9][0-9]*/Port 22/' /etc/ssh/sshd_config");
		$this->log .= "<p>sshport RESET</p>";
		$this->reboot = true;		
	}
	else {
		$this->log .= "<p>sshport PRESERVED</p>";	
	}
	if ( isset($_POST['ldap'] ) ) {
		$this->ldap = new ldaphelper;
		if (!$this->ldap->Connect()) {
			$this->message = "ERROR - Could not connect to LDAP";
		}
		else {
			$result = $this->ldap->Clean();
		}
		$this->log .= "<p>LDAP Directory RESET</p>";
	}
	else {
		$this->log .= "<p>LDAP Directory PRESERVED</p>";	
	}
		
	if ($this->reboot) {
		$this->log .= "<br/><br/><p>Rebooting...</p>";
	}
	else {
		$this->log .= "<br/><br/><p>Reset complete...</p>";
	}
}
}
