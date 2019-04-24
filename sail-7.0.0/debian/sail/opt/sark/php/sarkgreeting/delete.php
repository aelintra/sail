<?php

  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
  
  $helper = new helper;
  $id = $_REQUEST['id'];
  $key = $_REQUEST['key'];
  $cluster = $_REQUEST['cluster'];
 
  /* delete a record using information about id, */ 
  $helper->delTupleById("greeting",$id);
  $helper->request_syscmd ("/bin/rm -rf /usr/share/asterisk/sarksounds/$cluster/$id" . '.*>/dev/null 2>&1');  
  echo "ok";

?>
