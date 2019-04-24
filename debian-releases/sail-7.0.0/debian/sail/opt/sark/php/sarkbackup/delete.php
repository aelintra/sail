<?php

  require $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";

  $id = $_REQUEST['id'] ;
 
  $helper = new helper;
  $path = '/opt/sark/snap/';
  if (preg_match(' /sarkbak/ ', $id)) {
	  $path = '/opt/sark/bkup/';
  }
	  
  $helper->request_syscmd ( "/bin/rm $id" );
  $helper->logit("I'm deleting file $id ",3 );
  
  echo "ok";

?>
