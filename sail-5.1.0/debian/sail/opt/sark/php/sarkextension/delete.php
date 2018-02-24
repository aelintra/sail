<?php

  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
  
  $helper = new helper;
  $id = $_REQUEST['id'] ;
 
  /* delete a record using information about id, */ 
  $helper->delTuple("ipphone",$id); 
  /* delete COS information */
  $helper->predDelTuple("IPphoneCOSopen","IPphone_pkey",$id);
  $helper->predDelTuple("IPphoneCOSclosed","IPphone_pkey",$id);
  $helper->predDelTuple("IPphone_Fkey","pkey",$id);
   
  echo "ok";

?>
