<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
  require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass");
  require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass");

  $helper = new helper;
  $helper->logit("I'm sending routes ",3 );
  
  $stream = "{" ;
  $stream .= "'None':'None'," ;
//  $sql = "select li.pkey,ca.technology from lineio li inner join carrier ca on li.carrier=ca.pkey where ca.technology='IAX2' OR ca.technology='SIP' OR ca.technology='DAHDI' OR ca.technology='Custom'";
  $sql = "select li.pkey,ca.technology,ca.carriertype from lineio li inner join carrier ca on li.carrier=ca.pkey";
  $rows = $helper->getTable("lineio", $sql, true, true);
  
  foreach ($rows as $row) {
	if ($row['carriertype'] == 'DiD' || $row['carriertype'] == 'CLID' || $row['carriertype'] == 'Class' ) {
			continue;
	} 
	$stream .= "'" . $row['pkey'] . "':'" . $row['pkey'] . "'," ;
  }
/*
 * remove trailing commas
 */ 
  $stream = substr($stream, 0, -1);
  $stream .= "}" . PHP_EOL; 
  echo $stream;
?>
