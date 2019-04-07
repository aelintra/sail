<?php
//
// Developed by CoCo
// Copyright (C) 2016 CoCoSoft
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

	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkPDFClass";
	
	$target = strip_tags($_GET['pdf']);
	
	$pdf = new PDF();
	$dbh = DB::getInstance();
	$helper = new helper;
	$data = array();
	$header = array();
	$w = array();
	$layout = NULL;
	$margins = NULL;
	
	$table = 'print_' . $target;
	if ( ! function_exists($table)) {
		echo 'No function print' . '_$target.  Contact admin';
		exit;
	}
	$table($pdf,$dbh,$helper,$header,$data,$w,$layout);
	$pdf->SetFont('Arial','',8);
	$pdf->AddPage($layout);
	$pdf->pdfTable($header,$data,$w,$margins);
	$pdf->Output();
	exit;

function print_ipphone($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkAmiHelperClass";

	$amiHelper = new amiHelper();

	$sip_peers = $amiHelper->get_peer_array();
	
	$header = array('Ext', 'User', 'Tenant', 'Device','MAC','IP Addr', 'L/R', 'RECOPTS', 'Active');
	$w = array(10, 30, 40, 20, 25, 70, 15, 20, 10);
	$data = $helper->getTable("ipphone","select pkey,desc,cluster,device,macaddr,externalip,location,devicerec,active from ipphone");
	
	foreach ($data as $key=>$row) {
		$data[$key]['externalip'] = 'N/A';
		if (isset ($sip_peers [$row['pkey']]['IPaddress'])) {
			$data[$key]['externalip'] = $sip_peers [$row['pkey']]['IPaddress'];
		}
		if (empty($row['active'])) {
			$data[$key]['active'] = 'YES';
		}
	}
	$pdf->leftMargin=35;
	$pdf->pageHeading='Extensions';
	$layout='L';

}

function print_ddi($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	$header = array('Name', 'Tenant', 'Name','Open','Closed','Tag','Type','Active');
	$w = array(30, 40, 35, 35, 35, 25,10, 10);
	
	$sql = "select li.pkey,cluster,trunkname,openroute,closeroute,tag,ca.carriertype,active " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey " .
			"where ca.carriertype = 'DiD' OR ca.carriertype = 'CLID' or ca.carriertype = 'Class'";	
			
	$data = $helper->getTable("lineio", $sql,true,false,'li.pkey');
		
	$pdf->leftMargin=40;
	$pdf->pageHeading='DiDs';
	$layout='L';

}

function print_trunks($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkAmiHelperClass";

	$amiHelper = new amiHelper();

	$sip_peers = $amiHelper->get_peer_array();
	$iax_peers = $amiHelper->get_peer_array(True);	

	$header = array('Name', 'Tenant', 'Description','Peername','IP Addr','Type','Active');
	$w = array(30, 40, 65, 35, 30, 10, 10);
	
	$sql = "select li.pkey,cluster,description,peername,pat,ca.technology,active " . 
			"from lineio li inner join carrier ca  on li.carrier=ca.pkey where ca.carriertype!='DiD' " .
			"and ca.carriertype!='CLID' and ca.carriertype!='Class'";
			
	$data = $helper->getTable("lineio", $sql,true,false,'li.pkey');
		
	foreach ($data as $key=>$row) {

		if (isset ($iax_peers [$row['peername']]['IPaddress'])) {
			$data[$key]['pat'] = $iax_peers [$row['peername']]['IPaddress'];
		}
		elseif (isset ($sip_peers [$row['peername']]['IPaddress'])) {
			$data[$key]['pat'] = $sip_peers [$row['peername']]['IPaddress'];
		}
		else {
			$data[$key]['pat'] = 'N/A';
		}
	}
	$pdf->leftMargin=40;
	$pdf->pageHeading='Trunks';
	$layout='L';
}

function print_routes($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	$header = array('Route', 'Tenant', 'Dialplan', 'Description','Strategy','Path1','Path2','Auth','Active');
	$w = array(30, 40, 60, 40, 15, 35, 35, 10, 10);
	
	$sql = "select pkey,cluster,dialplan,desc,strategy,path1,path2,auth,active from route";	
			
	$data = $helper->getTable("route", $sql);
	foreach ($data as $key=>$row) {
		if (empty($row['strategy'])) {
			$data[$key]['strategy'] = 'Hunt';
		}
	}		
	$pdf->leftMargin=15;
	$pdf->pageHeading='Routes';
	$layout='L';
}

function print_groups($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	$header = array('Number', 'Tenant', 'Description', 'Type','Tag','Target','Outcome');
	$w = array(15, 40, 40, 15, 25, 80, 50);
	
	$sql = "select pkey,cluster,longdesc,grouptype,calleridname,out,outcome from speed";	
			
	$data = $helper->getTable("route", $sql);
	
	$pdf->leftMargin=15;
	$pdf->pageHeading='Call Groups';
	$layout='L';
}

function print_cluster($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	$header = array('Tenant', 'Operator', 'Include', 'Area Code', 'Dialplan','Abs Timeout','Channels');
	$w = array(40, 30, 80, 25, 25, 25, 15);
	
	$sql = "select pkey,operator,include,localarea,localdplan,abstimeout,chanmax from cluster";	
			
	$data = $helper->getTable("cluster", $sql);
	
	$pdf->leftMargin=30;
	$pdf->pageHeading='Tenants';
	$layout='L';
}

function print_ldap($pdf,$dbh,$helper,&$header,&$data,&$w) {
	
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkLDAPHelperClass";
	
	$ldap = new ldaphelper;
	$header = array('Surname', 'Forename', 'Phone', 'Mobile', 'Home');
	$w = array(30, 30, 25, 25, 25);
	if ( ! $ldap->Connect() ) {
		echo "ERROR - Could not connect to LDAP";
		exit;
	}
	$search_arg = array("uid","givenname", "sn", "telephoneNumber", "mobile", "homePhone", "cn");
	$result = $ldap->Search($search_arg);

	for ($i=0; $i<$result["count"]; $i++) {
		$data[] = array($result[$i]["sn"][0],
						$result[$i]["givenname"][0],
						$result[$i]["telephonenumber"][0],
						$result[$i]["mobile"][0],
						$result[$i]["homephone"][0]);			
	}
	
	
	$pdf->pageHeading='Directory';
	$pdf->leftMargin=40;
}

function print_shorewall($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	$header = array('Policy', 'Source', 'Proto', 'Port(s)', 'ConnRate','Comments');
	$w = array(20,50,10,35,25,70);
	$file="/etc/shorewall/sark_rules";
	$rec = file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die('Could not read file!');
	foreach ($rec as $row) {
		$nl=array();
		if (preg_match(" /^#|^\s*$/ ", $row)) {
			continue;
		}
		if (preg_match(" /#/ ", $row)) {
			$splitComments = explode("#",$row,2);
			$cols = explode(" ",$splitComments[0]);
		}
		else {
			$cols = explode(" ",$row);
		}

		for($i=0;$i<5;$i++) {
			if ($i != 2) {
				array_push($nl, $cols[$i]);
			}
		}
		if (! empty($cols[8])) {
			array_push($nl, $cols[8]);
		}
		else {
			array_push($nl, 'Unrestricted');
		}
		if (!empty($splitComments[1])) {			
			array_push($nl, trim($splitComments[1]));
		}
		else {
			array_push($nl, '-- none --');
		}
//		print_r($nl); 
		array_push($data,$nl);
		unset ($nl);
//		unset ($cols);	
		unset ($splitComments);	
	}

	$layout='L';
	$pdf->pageHeading='IPV4 Firewall Rules';
	$pdf->leftMargin=40;
	
}

function print_shorewall6($pdf,$dbh,$helper,&$header,&$data,&$w,&$layout) {

	$header = array('Policy', 'Source', 'Proto', 'Port(s)', 'ConnRate','Comments');
	$w = array(20,60,10,35,25,70);
	$file="/etc/shorewall6/sark_rules6";
	$rec = file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die('Could not read file!');
	foreach ($rec as $row) {
		$nl=array();
		if (preg_match(" /^#|^\s*$/ ", $row)) {
			continue;
		}
		if (preg_match(" /#/ ", $row)) {
			$splitComments = explode("#",$row,2);
			$cols = explode(" ",$splitComments[0]);
		}
		else {
			$cols = explode(" ",$row);
		}

		for($i=0;$i<5;$i++) {
			if ($i != 2) {
				array_push($nl, $cols[$i]);
			}
		}
		if (! empty($cols[8])) {
			array_push($nl, $cols[8]);
		}
		else {
			array_push($nl, 'Unrestricted');
		}
		if (!empty($splitComments[1])) {			
			array_push($nl, trim($splitComments[1]));
		}
		else {
			array_push($nl, '-- none --');
		}
//		print_r($nl); 
		array_push($data,$nl);
		unset ($nl);
//		unset ($cols);	
		unset ($splitComments);	
	}

	$layout='L';
	$pdf->pageHeading='IPV6 Firewall Rules';
	$pdf->leftMargin=40;
	
}
