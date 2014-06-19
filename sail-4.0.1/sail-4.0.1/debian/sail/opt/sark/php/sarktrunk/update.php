<?php
  require_once "../srkDbClass";
  require_once "../srkHelperClass";

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
 	
  if ($column == 'trunkname') {  
	  if (!preg_match("/^[\w\-0-9]+$/",$value)) {
		  echo "Trunkname must be alphanumeric (no spaces or special characters)";
		  return;
	  }
  }
/*
 * calculate the route class if a route has changed
 */   
  if ($column == 'openroute' ) {
	  $tuple['routeclassopen'] = $helper->setRouteClass($value);
  }
  if ($column == 'closeroute' ) {	  
	  $tuple['routeclassclosed'] = $helper->setRouteClass($value);
  }	   
/*  
 * set column=value in the array
 */
  $tuple[$column] = $value;
  $tuple['pkey'] = $id;
/*
 * call the setter
 */
  $ret = $helper->setTuple('lineio',$tuple);
  
  if ($ret == 'OK') {
	echo $_REQUEST['value'];
  }
  else {
	echo $ret;
  }  
?>
