<?php

  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
 
  $helper = new helper;
  $id = $_REQUEST['id'] ;

  //syslog(LOG_WARNING, "agent delete $id");
 
  /* delete a record using information about id, */ 
  $helper->delTuple("agent",$id); 
  echo "ok";

?>
