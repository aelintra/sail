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
// You should have received a copy of the GNU General Public License
// along with this program.  If not, see <http://www.gnu.org/licenses/>.
//


Class sarksplash {
	
	protected $message=NULL;
	protected $head="System Overview"; 
	protected $myPanel;
	protected $dbh;
	protected $helper;
	protected $validator;
	protected $invalidForm;
	protected $error_hash = array();
	protected $distro = array(); 
	protected $HA;


	
public function showForm() {

	$this->myPanel = new page;
	$this->dbh = DB::getInstance();
	$this->helper = new helper;
	$this->helper->qDistro($this->distro);
			
	$this->myPanel->pagename = 'System';
 
//	Debugging		
//	$this->helper->logit(print_r($_POST, TRUE));
	
	
	if (isset($_POST['commit']) || isset($_POST['commitClick'])) { 
		$this->saveEdit();
		if ($this->invalidForm) {
			$this->showMain();
			return;
		}
		else {
			$this->helper->sysCommit();
			$this->message = "Committed!";	
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
	$buttonArray=array();
  	$this->myPanel->actionBar($buttonArray,"sarksplashForm",false,false);

	if ($this->invalidForm) {
		$this->myPanel->showErrors($this->error_hash);
	}
	$this->myPanel->Heading($this->head,$this->message);

	$boxcolor=" w3-white";



	echo '<div class="w3-row-padding ' . $this->myPanel->bgColorClass . '">';

	echo '<div class="w3-col w3-display-container  m6 l6" style="height:140px;">';	
  	echo '<div id="ldavg_div" class="w3-container w3-display-middle"></div>';
  	echo '</div>';
  	echo '<div class="w3-col w3-display-container  m6 l6" style="height:140px;">';
  	echo '<div id="sys_div" class="w3-container w3-display-middle"></div>';
  	echo '</div>';
  	echo '</div>';

	echo '<div class="w3-row-padding w3-light-gray" style="min-height:10em;">' . PHP_EOL;
	echo '<div class="w3-col w3-container w3-hide-small m1 l2">&nbsp;</div>' . PHP_EOL;
    echo '<div class="w3-col w3-container m10 l8">' . PHP_EOL;
	$this->myPanel->aLabelFor("Live Calls");
	echo '<br/><br/>';
	echo '<table class="' . $this->myPanel->tableClass . ' w3-white w3-small" id="chantable"></table>';
	echo '<br/>';
	echo '</div>';
  	echo '</div>'; 

  	  	

	echo '<div class="w3-container w3-padding ' . $this->myPanel->bgColorClass . '" >';
  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">UpCalls</div>';
  	echo '<div id="upcalls" class="w3-display-middle w3-xlarge"></div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">Now</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';

  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">Inbound</div>';
  	echo '<div id="inbound" class="w3-display-middle w3-xlarge">3104</div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">Today</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';
 
  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">Outbound</div>';
  	echo '<div id="outbound" class="w3-display-middle w3-xlarge">2355</div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">Today</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';

  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">Internal</div>';
  	echo '<div id="internal" class="w3-display-middle w3-xlarge">985</div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">Today</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';

	echo '</div>';
	
	


  	echo '<div class="w3-container w3-padding ' . $this->myPanel->bgColorClass . '" >';
  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">Extensions</div>';
  	echo '<div id="extensions" class="w3-display-middle w3-xlarge">0/0</div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">up</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';

  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">Trunks</div>';
  	echo '<div id="trunks" class="w3-display-middle w3-xlarge">0/0</div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">up</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';

  	if (file_exists("/opt/sark/cache/speedtest")) {
		$downlink = `/bin/grep Down /opt/sark/cache/speedtest`;
		$uplink = `/bin/grep Up /opt/sark/cache/speedtest`;
		if ($downlink) {
			$speed = explode(':',$downlink); 
			$num  = explode(' ',$speed[1]);
			$down = $num[1];
		}
		if ($uplink) {
			$speed = explode(':',$uplink); 
			$num  = explode(' ',$speed[1]); 
			$up = $num[1];
		} 
	}
 
  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">Uplink</div>';
  	echo '<div class="w3-display-middle w3-xlarge">' . $up . '<sup>*</sup></div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">Mbps</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';

  	echo '<div class="w3-col w3-quarter">';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
	echo '<div class="w3-display-container w3-card-4 w3-half w3-round-large' . $boxcolor . '" style="height:6em">';
  	echo '<div class="w3-display-topmiddle w3-padding w3-small">Downlink</div>';
  	echo '<div class="w3-display-middle w3-xlarge">' . $down . '<sup>*</sup></div>';
  	echo '<div class="w3-display-bottommiddle w3-padding w3-small">Mbps</div>';
  	echo '</div>';
	echo '<div class="w3-col w3-quarter">&nbsp;</div>';
  	echo '</div>';

	echo '</div>';



	
  	$this->printSysNotes();

  	echo '<div class="w3-tiny w3-padding ' . $this->myPanel->bgColorClass . '"><sup>*</sup>Internet speeds are as observed from the PBX and will usually be somewhat lower than the advertised speed of the circuit. A speedtest is conducted once per hour</div>';

  	echo '</div>';
  	echo '</div>'; 
  	echo '</div>'; 
  	echo '</div>'; 

    $this->myPanel->responsiveClose();
    
}
 
private function printSysNotes () {
#
#   prints sysinfo Box
#
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkNetHelperClass";

	$helper = new helper;
	$nethelper = new nethelper;
	$dbh = DB::getInstance();
	
	$extcount = $dbh->query('select count(*) from ipphone')->fetchColumn();
	$clusteroclo = $dbh->query("SELECT oclo FROM cluster where pkey = 'default'")->fetchColumn();  
	$global = $dbh->query("SELECT EXTLIM,CLUSTER FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);

	$distro=array();
	$helper->qDistro($distro);
    $localip = $nethelper->get_localIPV4();
    $ipv6lla = $nethelper->get_IPV6LLA();
    $ipv6gua = $nethelper->get_IPV6GUA();
	$updays=false;
	$commip=NULL;
	$virtualip=NULL;
	$masteroclo = `sudo /usr/sbin/asterisk -rx 'database get STAT OCSTAT'`;
	$masteroclo = preg_replace( ' /^.*:\s/ ','',$masteroclo);
	if (preg_match('/not\sfound/', $masteroclo)) {
		$masteroclo = 'AUTO';
	}
	
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
		$systemName		=  gethostname();
	}
	else {
		$systemName = php_uname('n');
	}

    $free =  array();
    $free = `/usr/bin/free`;
    $uptime = `/usr/bin/uptime`;
    if (preg_match( ' /up\s(\d+\sdays)/ ', $uptime,$matches)) {
		$updays = $matches[1];
	}
	
	$arch = `uname -m`;
	$disk = array();
	
	if (preg_match( ' /^arm/ ', $arch)) {
	    $disk = `/bin/df -h`;
	}
	else {
		$disk = `/bin/df -h`;
	}
	if ($disk) {
    	$diskusage = preg_match ( '/(\d{1,2}\%)/', $disk,$matches);
    	$diskusage = $matches[1];
    }
    else {
    	$diskusage = 'unknown';	
    }

	if (preg_match( '/Mem:\s+(\d{1,7})\s+(\d{1,7})\s+(\d{1,7})\s+(\d{1,7})\s+(\d{1,7})/',$free,$matches )) {
			$totmem = $matches[1];
			$usedmem = $matches[2];
			$freemem = $matches[3];
			$sharedmem = $matches[4];
			$buffers = $matches[5];
    } 
    $macstr = "ip link show " . $nethelper->get_interfaceName() . " | awk '/ether/ {print $2}'";
    $mac = strtoupper(`$macstr`);
    	
    if ( $helper->check_pid() ) {
        $runstate = "RUNNING";
    }
    else {
        $runstate = "STOPPED";
    }
    if ( file_exists ("/etc/corosync/corosync.conf")) { 
		if  (`/bin/ps -e | /bin/grep corosync | /bin/grep -v grep`) {
        	$harunstate = "RUNNING";
        	$work = `/sbin/ip addr show eth0 | grep secondary`;    
			if (preg_match(" /inet *?([\d.]+)/",$work,$matches)) { 
				$virtualip = $matches[1]; 	 		
			}
			$work = `/sbin/ip addr show eth1 | grep inet`;
			if (preg_match(" /inet *?([\d.]+)/",$work,$matches)) { 
				$commip = $matches[1]; 	 		
			}			
    	}
		else {
        	$harunstate = "STOPPED";
    	}
    }
	echo '<div class="w3-row w3-padding ' . $this->myPanel->bgColorClass . '">';

    echo '<div class="w3-third">';
//    echo '<div class="w3-card">';
    echo '<div class="w3-container">';
    echo '<h3>System Info</h3>';    
    $rlse=''; 
    if ( $distro['rhel']  ) {	
		$rlse = `/bin/rpm -q sail`;
	}
	else {
		$rlse = `dpkg-query -W -f '\${version}\n' sail`;
	}
	if ( $helper->check_pid() ) {  
		$astrelease = `sudo /usr/sbin/asterisk -rx 'core show version'`;
		$astarray = explode(" ", $astrelease);
		$astrelnum = explode('~', $astarray[1]);
		echo "PBX release: <strong>" . $astrelnum[0] . "</strong><br/>";
	}	
    echo "SAIL Release: <strong>$rlse</strong><br/>";

    if ( $distro['debian'] ) {
		$rlse = `dpkg-query -W -f '\${version}\n' sailhpe`;
		echo "HPE Release: <strong>$rlse</strong><br/>";
	}
	if ($global['EXTLIM']) {
		echo "Endpoints licenced: <strong>" .$global['EXTLIM'] . "</strong><br/>";
	}
	echo "Endpoints defined: <strong>$extcount</strong><br/>";
	echo "Serial Num: <strong>" . $helper->rets() . "</strong><br/>";
	echo "MAC: <strong>$mac</strong><br/>";
		    
    preg_match ( '/^(\w*)\b/', $_SERVER['SERVER_SOFTWARE'], $matches);
    $server = $matches[1];
//    echo '<br/>'; 
    echo '</div>'; 
    echo '</div>';        
//	echo '</div>';
//	echo '</div>';

 
    echo '<div class="w3-third">';
//    echo '<div class="w3-card">';
    echo '<div class="w3-container">';
    echo '<h4>Network</h4>';
    echo "hostname: <strong>$systemName</strong><br/>";
    $ipdig = $nethelper->get_externip();
    if ( $ipdig ) {
		echo "Public IP: <strong>$ipdig</strong><br/>";
	}
	else {
		echo "Public IP: <strong>NO INTERNET</strong><br/>";
	}
	if ( $localip ) {
		echo "Local IP: <strong>$localip</strong><br/>";
	}
//	if ( $ipv6gua ) {
//		echo "IPV6: <strong>$ipv6gua</strong><br/>";
//	}
    
    if ( $virtualip ) {
        echo "Virtual IP: <strong>$virtualip</strong><br/>";
	}
	if ( $commip ) {
		echo "Comms IP: <strong>$commip</strong><br/>";
	}
//    print "Netmask: <strong>$snmask</strong><br/>";
/*
	if (file_exists("/opt/sark/cache/speedtest")) {
		$downlink = `/bin/grep Down /opt/sark/cache/speedtest`;
		$uplink = `/bin/grep Up /opt/sark/cache/speedtest`;
		if ($downlink) {
			$speed = explode(':',$downlink); 
			$num  = explode(' ',$speed[1]);
			echo 'Inet Down: <strong>' . $num [1] . ' Mbps</strong><br/>';
		}
		if ($uplink) {
			$speed = explode(':',$uplink); 
			$num  = explode(' ',$speed[1]); 
			echo 'Inet Up: <strong>' . $num [1] . ' Mbps</strong><br/>';
		} 
	}
*/
//	echo '<br/>'; 
//    echo '</div>'; 
    echo '</div>';        
	echo '</div>';

	    echo '<div class="w3-third">';
//    	echo '<div class="w3-card">';
    	echo '<div class="w3-container">';
    	echo '<h4>Resource</h4>';    
    	echo "Disk Usage: <strong>$diskusage</strong><br/>";
    	echo "RAM Size: <strong>$totmem</strong><br/>";
    	echo "RAM Free: <strong>$freemem</strong><br/>";
    	echo "PBX: <strong>$runstate</strong><br/>";
    	echo "Master Timer: <strong>$masteroclo</strong><br/>";
    	if ($global['CLUSTER'] == 'OFF') {
			echo "Timer State: <strong>$clusteroclo</strong><br/>";
   	 	}
//		echo '<br/>'; 
//   		echo '</div>'; 
    	echo '</div>';        
		echo '</div>';
		echo '</div>';

/*
		echo '<div class="w3-row w3-padding ' . $this->myPanel->bgColorClass . '">';
    	echo '<div class="w3-third">';
//    	echo '<div class="w3-card">';
//    	
    	echo '<div class="w3-container">';
    	echo '<h4>Status</h4>';
    	echo "PBX: <strong>$runstate</strong><br/>";
   	 	echo "Master Timer: <strong>$masteroclo</strong><br/>";
    	if ($global['CLUSTER'] == 'OFF') {
			echo "Timer State: <strong>$clusteroclo</strong><br/>";
   	 	}
    	echo "SysTime: <strong>" . `date '+%H:%M:%S'` . "</strong><br/>" . PHP_EOL;
    	if ($updays) {
    		echo "System Uptime: <strong>$updays</strong><br/>";
    	}
//    	echo '<br/>';
//    	echo '</div>';        
		echo '</div>';
		echo '</div>'; 

    	echo '<div class="w3-third">';
//    	echo '<div class="w3-card">';
    	echo '<div class="w3-container">';
    	echo '<h4>Client</h4>';
		$client = $this->myPanel->clientPlatform($_SERVER['HTTP_USER_AGENT']);
		print "Client IP: <strong>" . $_SERVER['REMOTE_ADDR'] ."</strong><br/>";
		print "Platform: <strong>" . $this->myPanel->opSys . "</strong><br/>";
		print "Browser: <strong>" . $client['browser'] . "</strong>";
//   	 	echo '</span>' . PHP_EOL;
//		echo '<br/>'; 
//   		echo '</div>'; 
    	echo '</div>';        
		echo '</div>';
		echo '</div>'; 
*/  	 	
}



}
