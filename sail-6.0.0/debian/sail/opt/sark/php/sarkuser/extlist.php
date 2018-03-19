<?php
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
    $helper = new helper;     
	$dbh = DB::getInstance();

	$helper->logit("I'm sending extension list ",3 );
		
	$res = $dbh->query("SELECT pkey from ipphone  ORDER BY pkey");  
	$res->setFetchMode(PDO::FETCH_COLUMN, 0);   
	$ipphone = $res->fetchAll(); 
	

	echo "{" . PHP_EOL;	 
	echo "'None':'None'," . PHP_EOL;	

	if (! empty($ipphone) ) {
		foreach ($ipphone as $ipphone => $value)  {
			echo "'$value':'$value'," . PHP_EOL;	
		}
	}

	echo "}" . PHP_EOL; 
?>
