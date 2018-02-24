<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
//  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
  
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
  
  if ($column == 'passwd') {
	  if (!preg_match("/^[0-9]{4}$/",$value)) {
		  echo "PIN must be numeric, 4 digits and greater than 1000";
		  return;
	  }
  } 
  
  if ($column == 'name') {
	  if (!preg_match("/^[\s\w\-0-9]+$/",$value)) {
		  echo "Name must be alphanumeric (no special characters)";
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
  $ret = $helper->setTuple('agent',$tuple);
  
  echo $_REQUEST['value'];

?>
