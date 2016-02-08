<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
  require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass");
  require_once ($_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass");
  $helper = new helper;
  $helper->logit("I'm sending trunks ",3 );
  
  $stream = "{" ;
  $stream .= "'None':'None'," ;
  $sql = "select * from lineio order by pkey";
  $rows = $helper->getTable("lineio", $sql);
  
  foreach ($rows as $row) {
	$stream .= "'" . $row['pkey'] . "':'" . $row['pkey'] . "'," ;
  }
  /*
 * remove trailing commas
 */ 
  $stream = substr($stream, 0, -1);
  $stream .= "}" . PHP_EOL; 
  echo $stream;
?>
