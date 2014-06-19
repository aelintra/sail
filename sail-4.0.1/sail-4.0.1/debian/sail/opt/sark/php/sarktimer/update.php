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
    
  if ($column == 'desc') {
	  if (!preg_match("/^[\s\w\-0-9\(\)\.\*]+$/",$value)) {
		  echo "Description must be alphanumeric (no special characters)";
		  return;
	  }
  } 
  if ($column == 'beginclose' || $column == 'endclose') {
	  if (!preg_match("/^(20|21|22|23|[01]\d|\d)(([:][0-5]\d){1,2})$/",$value)) {
			if (!preg_match("/^\*$/",$value)) {
				echo "Incorrect time format - use either * or hh:mm";
				return;
			}
	  }
  }
	  
	  
	  
  if ($column == 'beginclose') {
	$column = 'timespan';   
	if ($value != '*') {  		 
		$dbh = DB::getInstance();  
		$row = $dbh->query("SELECT timespan FROM dateseg WHERE pkey = '" . $id . "'")->fetch(PDO::FETCH_ASSOC);
		if ($row['timespan'] == '*') {
			$newvalue = $value . '-' . $value;
			$value = $newvalue; 
		}
		else {		
			$times = explode('-', $row['timespan']);
			$value .= '-' . $times[1];
		}
	}
  } 
  
  if ($column == 'endclose') {
	$column = 'timespan';    
	if ($value != '*') {				
		$dbh = DB::getInstance();  
		$row = $dbh->query("SELECT timespan FROM dateseg WHERE pkey = '" . $id . "'")->fetch(PDO::FETCH_ASSOC);
		if ($row['timespan'] == '*') {
			$newvalue = $value . '-' . $value;
			$value = $newvalue; 
		}
		else {		
			$times = explode('-', $row['timespan']);
			$newvalue = $times[0] . '-' . $value;
			$value = $newvalue; 
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
  $ret = $helper->setTuple('dateseg',$tuple);
  
  echo $_REQUEST['value'];
?>
