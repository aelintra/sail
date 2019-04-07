<?php
/*
	find and delete unused system messages
 */
$msgFile = file("/opt/sark/db/db_v4_system.sql");
$targetList = array();
foreach ($msgFile as $msg) {
// ignore non message rows	
	$pos = strpos($msg, 'core');
	if (! $pos) {
		continue;
	}
// extractthe message name
	preg_match( " /\'([\w|-]+)\'/ ",$msg,$matches);
	if ( ! isset ( $matches[1] )) {
		continue;
	}
// OK, we have a value to work on
	$grep = "grep -r '" . $matches[1] . "' /opt/sark/php";
	$ret = `$grep`;
	if ( ! $ret ) {
		$rows = explode (PHP_EOL,$ret);
		foreach ($rows as $row) {
//			if ( preg_match( " /aLabelFor/ ", $row ) || preg_match (" /aHeaderFor/ ", $row)) {
//						echo $row . "\n";
//			}	
			$targetList[$matches[1]] = true;
		} 
//			echo $matches[1] . " matched in /php\n";
//			echo $ret . "\n";
	}
} 
//print_r ($targetList);
// now we have out list of ununused messages - build a new output file without them

$OUT = NULL; 
foreach ($msgFile as $msg) {
// ignore non message rows	
	$pos = strpos($msg, 'core');
	if (! $pos) {
		continue;
	}
// extract the message name
// 	echo $msg . "\n"; 
	preg_match( " /\'([\w|-]+)\'/ ",$msg,$matches);
//	echo $matches[1] . "\n";
	if ( ! isset ( $matches[1] )) {
		continue;
	}
// ignore messages which aopear in the array
	if (array_key_exists ($matches[1], $targetList)) {
		continue;
	}			
// write out the row
	$OUT .= $msg;
}
echo $OUT;






