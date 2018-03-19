<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkLDAPHelperClass";
//  print_r($_REQUEST);
  
  $helper = new helper;
  $ldap = new ldaphelper;
  
  $helper->removeLrCr($value); 
  
  $id = $_REQUEST['id'] ;
  $value = strip_tags($_REQUEST['value']) ;
  $column = $_REQUEST['columnName'] ;
  
  $argument=array();
  
  if (!$ldap->Connect()) {
	echo  "LDAP ERROR19 - " . ldap_error($ldap->ds);
  }
  
  $dn = "uid=" . $_REQUEST['id'] . "," . $ldap->addressbook . "," . $ldap->base;
  
  if ($value == "") {
	if ($column=='sn') {
		echo "LDAP ERROR26 - Surname/Company name cannot be blank";
		$ldap->Close();
		return;
	}		
	$argument[$column] = array();
	if (ldap_mod_del($ldap->ds,$dn,$argument)) {  
		echo $_REQUEST['value'];
	}
	else { 
		echo  "LDAP ERROR30 - " . ldap_error($ldap->ds);
	} 	
  }
  else {
	if ($column=='phone' || $column=='mobile' || $column=='home') {
		if (!is_numeric($value)) {
			echo "V-ERROR36 - phone numbers must be numeric";
			$ldap->Close();
			return;
		}
	}	  

	
// entity integrity check 
	if ($column=='givenname' || $column=='sn') {
		$search_arg = array("uid", "givenname", "sn", "telephoneNumber", "mobile", "homePhone", "cn");
		if (!$result = $ldap->Get("uid=" . $id, $search_arg)) {
			echo  "LDAP ERROR47 - Couldn't retrieve UID $value";
			$ldap->Close();
			return; 
		}	
	}
	if ($column=='givenname') {
		$argument["cn"] = $value . ' ' . $result[0]["sn"][0];
	}
	if ($column=='sn') {
		$argument["cn"] = $result[0]["givenname"][0] . ' ' . $value;
	}
			
	$argument[$column] = $value;		   
	if (ldap_mod_replace($ldap->ds,$dn,$argument)) {
		echo $_REQUEST['value'];		
	}
	else { 
		print_r($argument);
		echo  "LDAP ERROR65\ndn=$dn\nERROR - " . ldap_error($ldap->ds);
	}	
  }   

  $ldap->Close();
  return;   
?> 

  
  
?>
