<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
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
  
  if ($column == 'password') {
	  if (!preg_match("/^([\w\-0-9]+){8,}$/",$value)) {
		  echo "Password must be minimum 8 char alphanumeric (no spaces)";
		  return;
	  }
      $salt = dechex(mt_rand(0, 2147483647)) . dechex(mt_rand(0, 2147483647)); 
      $password = hash('sha256', $value . $salt); 
      for($round = 0; $round < 65536; $round++) {
		$password = hash('sha256', $password . $salt); 
      } 
	  $helper = new helper;
	  $helper->logit("I'm updating the password for $id",3 );
	  $tuple['password'] = $password;
	  $tuple['salt'] = $salt;
	  $tuple['pkey'] = $id;
	  $ret = $helper->setTuple('user',$tuple);
	  echo "Password successfully updated";
	  return;	  
  } 
  if ($column == 'email') {
	  $helper = new helper;
	  if (! $helper->validEmail($value)) {
		  echo "email must have a valid email format";
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
  $ret = $helper->setTuple('user',$tuple);
  
  echo $_REQUEST['value'];

?>
