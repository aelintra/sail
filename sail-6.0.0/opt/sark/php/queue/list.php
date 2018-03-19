<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
     
  $helper = new helper;
  $dbh = DB::getInstance();

  $helper->logit("I'm sending queues ",3 );
  $user =  $_SESSION['user']['pkey'];
  $wherestring = 'ORDER BY pkey';
	
  if ($_SESSION['user']['pkey'] == 'admin') {
		
  }
  else {
		$res = $dbh->query("SELECT cluster from user where pkey='" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);		
		$wherestring = "ORDER BY pkey WHERE cluster='" . $res['cluster'] . "'" ;
  }
  
  echo "{" . PHP_EOL;
  echo "'None':'None'," . PHP_EOL;
  $sql = "select * from Queue $wherestring";
  foreach ($dbh->query($sql) as $row) {
	echo "'" . $row['pkey'] . "':'" . $row['pkey'] . "'," . PHP_EOL;
  }
  echo "}" . PHP_EOL; 
?>
