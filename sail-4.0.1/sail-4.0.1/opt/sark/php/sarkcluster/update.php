<?php
  require_once "../srkDbClass";
  require_once "../srkHelperClass";
  
  $tuple = array();

//  print_r($_REQUEST);
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
   
  if ($column == 'abstimeout') {
		if (!preg_match("/^([0-9])+$/",$value)) {
			echo "abstimeout must be Numeric (seconds)";
			return;
		}
  } 
  
  if ($column == 'chanmax') {
		if (!preg_match("/^([0-9])+$/",$value)) {
			echo "channel must be Numeric";
			return;
		}
  } 
    
    if ($column == 'localarea') {
	  if ($value) {
		if (!preg_match("/^([0-9])+$/",$value)) {
			echo "Local area code must be Numeric";
			return;
		}
	  } 
  } 
    
  if ($column == 'localdplan') {
	  if ($value) {
		if (!preg_match("/^[_0-9XNZxnz!#\s\*\.\-\[\]]+$/",$value)) {
		  echo "Dialplan must be a valid Asterisk dialplan";
		  return;
		}
	  }
  }
     
/*  
 * set column=value in the array
 */
  $tuple[$column] = $value;
  $tuple['pkey'] = $id;
/*
 * call the setter
 */
  $ret = $helper->setTuple('cluster',$tuple);
  
  echo $_REQUEST['value'];

?>
