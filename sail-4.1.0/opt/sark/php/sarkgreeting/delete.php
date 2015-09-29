<?php

  require "../srkDbClass";
  require "../srkHelperClass";
  
  $helper = new helper;
  $id = $_REQUEST['id'] ;
 
  /* delete a record using information about id, */ 
  $helper->delTuple("greeting",$id);
  $helper->request_syscmd ("/bin/rm -rf /usr/share/asterisk/sounds/$id" . '.*>/dev/null 2>&1');  
  echo "ok";

?>
