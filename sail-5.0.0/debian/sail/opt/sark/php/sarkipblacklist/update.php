<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
	
	$id = $_REQUEST['id'] ;
//	print_r($_REQUEST);
	$value = strip_tags($_REQUEST['value']) ;
	$column = $_REQUEST['columnName'] ;
	$columnPosition = $_REQUEST['columnPosition'] ;
	$columnId = $_REQUEST['columnId'] ;
	$rowId = $_REQUEST['rowId'] ;
	
	
	if ($column == 'source') {
		if (trim($value) != 'LAN' && trim($value) != '0.0.0.0/0' ) {
			if (!preg_match("/^(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))?$/",$value)) {
				if (!preg_match("/([a-z]+)\.[a-z0-9\-\.]+\.([a-z\.]{2,7})$/",$value)) {
					echo "Source address looks wrong ";
					return;
				}
			}
		}
	}
	
	if ($column == 'comment') {
		$value = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $value);
		$value = trim($value);
		if (!empty($value)) {
			if (!preg_match("/^[a-zA-Z0-9\(\)\.\-_\s]{2,30}$/",$value)) {
				echo "comment maxlen=30 and can only contain characters a-zA-Z0-9().-_ and spaces";
				return;
			}
		}
	}		
 
	if ($column == 'portrange') {
	  if (!preg_match("/^[0-9:,\s]+$/",$value)) {
		  echo "portrange has invalid characters - use only [0-9,:] ";
		  return;
	  }
	}   

	
	$helper = new helper;
	$helper->logit("I'm updating shorewall rule $id with a new " . $column . " of $value",3 );
/*  
 * set column=value in the array
 */
	$tuple[$column] = $value;
	$tuple['pkey'] = $id;
/*
 * call the setter
 */
	$ret = $helper->setTuple('shorewall',$tuple);
	echo $_REQUEST['value'];
?>
