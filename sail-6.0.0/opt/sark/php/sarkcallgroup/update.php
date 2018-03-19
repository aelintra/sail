<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";

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

  if ($column == 'out') {  
	  if (!preg_match('/^[@A-Za-z0-9-_\/\s]+$/',$value)) {
		  echo "Target must be number or number/channel strings separated by whitespace";
		  return;
	  }  
	  if ($helper->loopcheck($id, $value)) {
		  echo "Loop detected in target list!";
		  return;
	  }
  } 
  if ($column == 'longdesc') {
	  if (!preg_match("/^[\s\w\-0-9]+$/",$value)) {
		  echo "Description must be alphanumeric (no special characters)";
		  return;
	  }	  

  }   
/*
 * calculate the route class if outcome has changed
 */   
  if ($column == 'outcome' ) {	  
	  $tuple['outcomerouteclass'] = $helper->setRouteClass($value);
  }  
/*  
 * set column=value in the array
 */
  $tuple[$column] = $value;
  $tuple['pkey'] = $id;
/*
 * call the setter
 */
  $ret = $helper->setTuple('speed',$tuple);
   
  if ($ret == 'OK') {
	echo $_REQUEST['value'];
  }
  else {
	echo $ret;
  }  
?>
