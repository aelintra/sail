
<?php 
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkPageClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkhead.php"; 
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/banner.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/navigation.php";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/formvalidator.php";
	
	$myPanel = new page;
	$dbh = DB::getInstance();
	$helper = new helper;
	
	if (!empty($_POST['searchkey'])) {
		$myPanel->pageSwitch($_POST['searchkey']);
	}
	else {
		$res = $dbh->query("SELECT CLUSTER FROM globals where pkey = 'global'")->fetch(PDO::FETCH_ASSOC);
		echo '<input type="hidden" id="cosflag" name="cosflag" value="' . $res['CLUSTER'] .  '" />' . PHP_EOL;
		$_SESSION['ctrl']['cosflag'] =  $res['CLUSTER'];		
		$val = 'NO';
		if ( $_SESSION['user']['pkey'] == 'admin' ) {
			$val = 'YES';
		}
		echo '<input type="hidden" id="sysuser" name="sysuser" value="' . $val .  '" />' . PHP_EOL;	
		$_SESSION['ctrl']['sysuser'] = $val;
					
		$url = explode('/', $_SERVER['SCRIPT_URL']);	
		require 'view.php';
		if (class_exists($url[2])) { 	  
			$Panel = new $url[2];
			$Panel->showForm();
		}
		else {
			die ("Class " . $url[2] . " not found");
		}
	}

	include $_SERVER["DOCUMENT_ROOT"] . "../php/srkfoot.php"; 
?>
