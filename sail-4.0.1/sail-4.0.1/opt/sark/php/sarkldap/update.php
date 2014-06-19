<?php
  require_once "../srkDbClass";
  require_once "../srkHelperClass";
  require_once "../srkLDAPHelperClass";
//  print_r($_REQUEST);
  
  $helper = new helper;
  $ldap = new ldaphelper;
  
  $helper->removeLrCr($value); 
  
  $id = $_REQUEST['id'] ;
  $value = strip_tags($_REQUEST['value']) ;
  $column = $_REQUEST['columnName'] ;
  

  $argument=array();
  
  if (!$ldap->Connect()) {
	echo  "LDAP ERROR - " . ldap_error($ldap->ds);
  }
  
  $dn = "cn=" . $_REQUEST['id'] . "," . $ldap->addressbook . "," . $ldap->base;
  
  if ($value == "") {
	$argument[$column] = array();
	if (ldap_mod_del($ldap->ds,$dn,$argument)) {  
		echo $_REQUEST['value'];
	}
	else { 
		echo  "LDAP ERROR - " . ldap_error($ldap->ds);
	} 	
  }
  else {
	if ($column=='phone' || $column=='mobile' || $column=='home') {
		if (!is_numeric($value)) {
			echo "ERROR - phone numbers must be numeric";
			return;
		}
	}	  
	$argument[$column] = $value;    
	if (ldap_mod_replace($ldap->ds,$dn,$argument)) {  
		echo $_REQUEST['value'];
	}
	else { 
		echo  "LDAP ERROR - " . ldap_error($ldap->ds);
	}
  }   

  $ldap->Close();
  return;   
?> 

  
  
?>
