<?php
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
  require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
  
  $dbh = DB::getInstance();
  $tuple = array();

//  print_r($_REQUEST);
// print_r($_POST);
/*  
  if (!empty($_POST)) {
	  foreach ($_POST as $key=>$data) {
		   $this->logit(" AddData found $key => $data", 0 ); 
	  }
  }
  else {
	  $this->logit(" AddData POST is empty!", 0 );
  }
	
*/ 

  	$tuple = array();	
	
	$res = $dbh->query("SELECT AGENTSTART FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
	$agentstart = $res['AGENTSTART'];
	
	while (1) {		
		$res = $dbh->query("SELECT pkey FROM agent where pkey = '" . $agentstart . "'")->fetch(PDO::FETCH_ASSOC);
		if ( isset($res['pkey']) ) {
			$agentstart++;
		}
		else {
			break;
		}
	}
    
	$tuple['pkey'] 		=  $agentstart;
	$_REQUEST['pkey'] = $agentstart;
/*	
	$tuple['passwd']	=  $agentstart;
	$ret = $this->helper->createTuple("agent",$tuple);
	if ($ret == 'OK') {
//		$this->helper->commitOn();	
		$this->message = "Saved new agent " . $tuple['pkey'] . "!";
	}
	else {
		$this->invalidForm = True;
		$this->message = "<B>  --  Validation Errors!</B>";	
		$this->error_hash['agentinsert'] = $ret;	
	}
*/ 
  echo "ok";
  

?>
