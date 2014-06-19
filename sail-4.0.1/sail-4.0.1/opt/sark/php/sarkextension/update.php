<?php
  require_once "../srkDbClass";
  require_once "../srkHelperClass";
//  print_r($_REQUEST);

  $tuple = array();

  $id = $_REQUEST['id'] ;
  $value = strip_tags($_REQUEST['value']) ;
  $column = $_REQUEST['columnName'] ;
  $columnPosition = $_REQUEST['columnPosition'] ;
  $columnId = $_REQUEST['columnId'] ;
  $rowId = $_REQUEST['rowId'] ;
   
  /* Update a record using information about id, columnName (property
     of the object or column in the table) and value that should be
     set */ 
  $helper = new helper;
  
  $helper->removeLrCr($value);
  
/*  
 * set column=value in the array
 */
  $tuple[$column] = $value;
  $tuple['pkey'] = $id;
/*
 * call the setter
 */
  $ret = $helper->setTuple('ipphone',$tuple);
  
  if ($ret == 'OK') {
	echo $_REQUEST['value'];
  }
  else {
	echo $ret;
  }  
?>
