<?php

  require "../srkDbClass";
  require "../srkHelperClass";

  $id = $_REQUEST['id'] ;
 
  $helper = new helper;
  $path = '/opt/sark/snap/';
  if (preg_match(' /sarkbak/ ', $id)) {
	  $path = '/opt/sark/bkup/';
  }
	  
  $helper->request_syscmd ( "/bin/rm $path$id" );
  $helper->logit("I'm deleting file $path$id ",3 );
  
  echo "ok";

?>
