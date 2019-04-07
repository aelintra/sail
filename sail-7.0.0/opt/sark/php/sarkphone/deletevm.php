<?php

  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";

  $id = $_REQUEST['id'] ;
 
  $helper = new helper;
	  
  $helper->request_syscmd ( "/bin/rm $id" );
  $helper->logit("I'm deleting file $id ",3 );
  
  echo "ok";

?>
