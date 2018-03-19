<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
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
    
  if ($column == 'desc') {
	  if (!preg_match("/^[\s\w\-0-9\(\)\.\*]+$/",$value)) {
		  echo "Description must be alphanumeric (no special characters)";
		  return;
	  }
  } 
 
  if ($column == 'pkey') {
	  if (!preg_match("/^[\d]+$/",$value)) {
		  echo "key must be numeric; no spaces";
		  return;
	  }
  } 
  
  if ($column == 'prefix') {
	  if (!preg_match("/^[+*#0-9][\d*#]*$/",$value)) {
		  echo "prefix must be (+)*#0-9";
		  return;
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
  if ($column == 'pkey') {
	$ret = $helper->setTuple('callback',$tuple,$value);
  }
  else {
	$ret = $helper->setTuple('callback',$tuple);  
  }
  
  echo $_REQUEST['value'];
?>
