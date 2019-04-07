<?php

  	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
  
  	$helper = new helper;
  	$rets = $helper->delTupleById("ivrmenu",$_REQUEST['id']); 

  	echo "ok";

?>