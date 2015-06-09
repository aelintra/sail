<?php
  require "../srkDbClass";
  require "../srkHelperClass";
  
  $helper = new helper;        
  $dbh = DB::getInstance();

  $helper->logit("I'm sending clusters ",3 );
  
  echo "{" . PHP_EOL;
  $sql = "select * from cluster order by pkey";
  foreach ($dbh->query($sql) as $row) {
	echo "'" . $row['pkey'] . "':'" . $row['pkey'] . "'," . PHP_EOL;
  }
  echo "}" . PHP_EOL; 
?>
