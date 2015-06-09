<?php
  require "../srkDbClass";
     
  $dbh = DB::getInstance();

  syslog(LOG_WARNING, "I'm sending queues " );
  
  echo "{" . PHP_EOL;
  echo "'None':'None'," . PHP_EOL;
  $sql = "select * from Queue order by pkey";
  foreach ($dbh->query($sql) as $row) {
	echo "'" . $row['pkey'] . "':'" . $row['pkey'] . "'," . PHP_EOL;
  }
  echo "}" . PHP_EOL; 
?>
