<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";

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
  
  preg_match( '/^(\d+)~(.*)$/',$id,$matches);
  $pkey = $matches[2];
  $seq = $matches[1];
/*
 * call PDO
 */
  try {
	$res=$dbh->prepare('UPDATE ipphone_Fkey SET ' . $column . '=? WHERE pkey=? and seq=?');
	$res->execute(array($value,$pkey,$seq ));	
  }
  catch (PDOException $e) {
    	echo $e->getMessage();	
  }
  
 
  echo $_REQUEST['value'];

?>
