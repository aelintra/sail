<?php

  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
  
  $helper = new helper;
  $id = $_REQUEST['id'] ;
 
  /* delete a record using information about id, */ 
    
  $dbh = DB::getInstance();

/* delete a record using information about id, */ 
  $helper->delTuple("user",$id); 
/* delete Panel information */
  $helper->predDelTuple("userpanel","user_pkey",$id);
  
  $helper->logit("I'm deleting user $id ",3 );
  echo "ok";

?>
