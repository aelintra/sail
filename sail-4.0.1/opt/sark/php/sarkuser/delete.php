<?php

  require "../srkDbClass";
  require "../srkHelperClass";
  
  $helper = new helper;
  $id = $_REQUEST['id'] ;
 
  /* delete a record using information about id, */ 
    
  $dbh = DB::getInstance();
  $dbh->exec("DELETE FROM user WHERE pkey = '" . $id . "'" );
  $dbh->exec("DELETE FROM userpanel WHERE user_pkey = '" . $id . "'" );
  $helper->logit("I'm deleting user $id ",3 );
  echo "ok";

?>
