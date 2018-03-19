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
  $dbh = DB::getInstance();
  
  $helper->removeLrCr($value);
    
  if ($column == 'desc') {
	  if (!preg_match("/^[\s\w\-0-9\(\)\.\*]+$/",$value)) {
		  echo "Description must be alphanumeric (no special characters)";
		  return;
	  }
  }
  $res = $dbh->query("SELECT technology,blfkeyname from device where pkey='" . $id . "'")->fetch(PDO::FETCH_ASSOC);	
  if ($column == 'blfkeyname' && $res['technology'] != 'SIP') {
	  echo "Can't set BLF key file for non-SIP object!";
	  return;
  }
  if ($column == 'blfkeys' && $res['technology'] != 'SIP') {
	  echo "Can't set BLF name for non-SIP object!";
	  return;
  } 
  if ($column == 'blfkeys' && ! is_numeric($value)) {
	  echo "BLF key number MUST be numeric!";
	  return;
  }  
   
/*  
 * set column=value in the array
 */
  $tuple[$column] = $value;
  $tuple['pkey'] = $id;
/*
 * call the setter
 */
  $ret = $helper->setTuple('device',$tuple);
  
  echo $_REQUEST['value'];
?>
