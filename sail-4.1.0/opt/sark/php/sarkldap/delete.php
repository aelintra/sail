<?php
  require_once "../srkLDAPHelperClass";

  $ldap = new ldaphelper;
  
  if (!$ldap->Connect()) {
	echo  "LDAP ERROR - " . ldap_error($ldap->ds);
  }
  
  $dn = "uid=" . $_REQUEST['id'] . "," . $ldap->addressbook . "," . $ldap->base;
  if (ldap_delete($ldap->ds,$dn)) {
	echo "ok";
  }
  else { 
	echo  "LDAP ERROR - " . ldap_error($ldap->ds);
  }
  
  $ldap->Close();

?>
