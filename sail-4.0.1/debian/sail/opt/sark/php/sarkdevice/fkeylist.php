<?php
	require "../srkDbClass";
    require "../srkHelperClass";
  
    $helper = new helper;     
	$dbh = DB::getInstance();

	$helper->logit("I'm sending fkey list ",3 );
	
	$res = $dbh->query("SELECT pkey from device WHERE technology='BLF Template'");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$blf = $res->fetchAll(); 
	
	 							 		
	echo "{" . PHP_EOL;	
	echo "'None':'None'," . PHP_EOL;
 
	foreach ($blf as $blf => $value)  {
		echo "'$value':'$value'," . PHP_EOL;	
	}


	echo "}" . PHP_EOL; 
?>
