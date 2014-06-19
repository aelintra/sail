<?php
	require_once "../srkDbClass";
	require_once "../srkHelperClass";
	$file = '/etc/shorewall/sark_rules';
	$OUT = null;
	$id = $_REQUEST['id'] ;
//	print_r($_REQUEST);
	$value = strip_tags($_REQUEST['value']) ;
	$column = $_REQUEST['columnName'] ;
	$columnPosition = $_REQUEST['columnPosition'] ;
	$columnId = $_REQUEST['columnId'] ;
	$rowId = $_REQUEST['rowId'] ;
	
	if ($column == 'fwsource') {
		if (trim($value) != 'net:$LAN' && trim($value) != 'net') {
			if (!preg_match("/^net:(([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])\.){3}([0-9]|[1-9][0-9]|1[0-9]{2}|2[0-4][0-9]|25[0-5])(\/(\d|[1-2]\d|3[0-2]))?$/",$value)) {
				if (!preg_match("/^net:([a-z]+)\.[a-z0-9\-\.]+\.([a-z\.]{2,7})$/",$value)) {
					echo "Source address looks wrong ";
					return;
				}
			}
		}
	}
 
	if ($column == 'fwdestport') {
	  if (!preg_match("/^[0-9:,\s]+$/",$value)) {
		  echo "Dest port has invalid characters - use only [0-9,:] ";
		  return;
	  }
	}   
	if (file_exists($file)) {
		$rec = file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die('Could not read file!');
	}
	$cols = explode (" ", $rec[$rowId]);
	$cols[$columnId] = $value;
	$rec[$rowId] = implode(" ",$cols);
	foreach ($rec as $line) {
		$OUT .= trim($line) . "\n";
	}
	
	$fh = fopen($file, 'w') or die('Could not open file!');
	fwrite($fh, $OUT) 
		or die('Could not write to file');
	fclose($fh);
	
	$helper = new helper;
	$helper->logit("I'm updating shorewall rule $id with a new " . $column . " of $value",3 );
	echo $_REQUEST['value'];
?>
