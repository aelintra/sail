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
    
  if ($column == 'description') {
	  if (!preg_match("/^[\s\w\-0-9\(\)\.\*]+$/",$value)) {
		  echo "Description must be alphanumeric (no special characters)";
		  return;
	  }
  }  
   
  if ($column == 'pin' || $column == 'adminpin') {
	  if (is_numeric($value) && $value >= 1000 && $value <= 9999) {
	  }
	  elseif (!empty($value)) {
		  echo "Pin must be numeric 1000-9999";
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

  $ret = $helper->setTuple('meetme',$tuple);  

  
  echo $_REQUEST['value'];
?>
