<?php

  require "../srkDbClass";
  require "../srkHelperClass";

  $id = $_REQUEST['id'] ;
 
  $helper = new helper;
	  
  $helper->request_syscmd ( "/bin/rm $id" );
  $helper->logit("I'm deleting file $id ",3 );
  
  echo "ok";

?>
