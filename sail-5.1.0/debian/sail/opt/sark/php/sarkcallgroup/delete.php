<?php

  require $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
  
  $helper = new helper;
  $id = $_REQUEST['id'] ;
 
  /* delete a record using information about id, */ 
  $helper->delTuple("speed",$id); 
  echo "ok";

?>
