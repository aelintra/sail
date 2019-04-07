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
    
  if ($column == 'mcastdesc') {
	  if (!preg_match("/^[\s\w\-0-9\(\)\.\*]+$/",$value)) {
		  echo "Description must be alphanumeric (no special characters)";
		  return;
	  }
  } 
  
   if ($column == 'mcastip') {
	  if (!preg_match("/^([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])\\.([01]?\\d\\d?|2[0-4]\\d|25[0-5])$/",$value)) {
		  echo "Invalid IP address";
		  return;
	  }
  } 
   
  if ($column == 'mcastport' ) {
	  if (is_numeric($value) && $value >= 0 && $value <= 65535) {
	  }
	  else {
		echo "Port must be numeric 1-65535";
		return; 
	  } 
   }
   
   if ($column == 'mcastlport') {
	  if ($value) { 
		if (is_numeric($value) && $value >= 0 && $value <= 65535) {
		}
		else {
			echo "Port must be numeric 1-65535";
			return; 
		} 
	  }
   }  
  
  if ($column == 'pkey') {
	  if (is_numeric($value) && $value <= 9999) {
			  continue;
	  }
	  else {
		  echo "key must be numeric and 4 digits or less";
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
	$ret = $helper->setTuple('mcast',$tuple,$value);
  }
  else {
	$ret = $helper->setTuple('mcast',$tuple);  
  }
  
  echo $_REQUEST['value'];
?>
