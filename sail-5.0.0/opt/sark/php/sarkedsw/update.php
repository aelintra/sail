<?php
	require_once "../srkDbClass";
	require_once "../srkHelperClass";
	$file = '/etc/shorewall/sark_rules';
	$helper = new helper;
	$OUT = null;
	$id = $_REQUEST['id'] ;
//	print_r($_REQUEST);
	$value = strip_tags($_REQUEST['value']) ;
	$column = $_REQUEST['columnName'] ;
	$columnPosition = $_REQUEST['columnPosition'] ;
	$columnId = $_REQUEST['columnId'] ;
	$rowId = $_REQUEST['rowId'] ;
	
// clean the file
	$helper->request_syscmd ("sed -i 's/\t/ /g' /etc/shorewall/sark_rules");
	$helper->request_syscmd ("sed -i 's/ +/ /g' /etc/shorewall/sark_rules");
	
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
	
	if ($column == 'fwdesc') {
		$value = str_replace(array("\r\n", "\r", "\n", "\t"), ' ', $value);
		$value = trim($value);
		if (!empty($value)) {
			if (!preg_match("/^[a-zA-Z0-9\(\)\.\-_\s]{2,30}$/",$value)) {
				echo "Description maxlen=30 and can only contain characters a-zA-Z0-9().-_ and space";
				return;
			}
		}
	}		
	
	if ($column == 'fwdestport') {
	  if (!preg_match("/^[0-9:,\s]+$/",$value)) {
		  echo "Dest port has invalid characters - use only [0-9,:] ";
		  return;
	  }
	} 	
 
	if ($column == 'fwconnrate') {
	  if (!preg_match("/^\d{1,3}\/\w{3,7}:\d{1,3}$/",$value)) {
		  echo "Connection Rate looks wrong (^\d{1,3}\/\w{3,7}:\d{1,3}$) ";
		  return;
	  }
	} 	
	 
	if (file_exists($file)) {
		$rec = file($file, FILE_IGNORE_NEW_LINES|FILE_SKIP_EMPTY_LINES) or die('Could not read file!');
	}
	
	if (preg_match(" /#/ ", $rec[$rowId])) {
		$splitComments = explode("#",$rec[$rowId],2);
		if (!empty($splitComments [1])) {
			$cols[9] = $splitComments [1];
		}
		$cols = explode(" ",$splitComments[0],9);
	}
	else {
		$cols = explode(" ",$rec[$rowId],9);
	}
		
	if ($columnId == 8) {		
		$value = preg_replace(' /#/ ', '', $value);  
		$value = trim($value);
		if (!empty($value)) {
			$cols[8] = '#' . $value;
		}
		else {
			$cols[8] = $value;
		}
	}
	else {
		$cols[$columnId] = $value;
	}
//	print_r ($cols);
	$rec[$rowId] = implode(" ",$cols);
	foreach ($rec as $line) {
		$OUT .= trim($line) . "\n";
	}
	
	$fh = fopen($file, 'w') or die('Could not open file!');
	fwrite($fh, $OUT) 
		or die('Could not write to file');
	fclose($fh);
	
	
	$helper->logit("I'm updating shorewall rule $id with a new " . $column . " of $value",3 );
	echo $_REQUEST['value'];
?>
