<?php 
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkPageClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkhead.php"; 
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

		$res = $dbh->query("SELECT extension FROM user where pkey = '" . $_SESSION['user']['pkey'] . "'")->fetch(PDO::FETCH_ASSOC);
		$userext = 'none';
		if (isset($res['extension'])) {
			$userext = $res['extension'];
		}
		echo '<input type="hidden" id="userext" name="userext" value="' . $userext .  '" />' . PHP_EOL;	
		echo '<input type="hidden" id="sessext" name="sessext" value="' . $_SESSION['user']['pkey'] .  '" />' . PHP_EOL;	

		echo '<div class="w3-top" style="z-index:99">';
		require_once $_SERVER["DOCUMENT_ROOT"] . "../php/banner.php";
/*		
		if (!$_SESSION['nag']) {
			require_once $_SERVER["DOCUMENT_ROOT"] . "../php/navigation.php";
		}
*/
					
		$url = explode('/', $_SERVER['SCRIPT_URL']);	
		require 'view.php';
		if (class_exists($url[2])) { 	  
			$Panel = new $url[2];
			$helper->logit("srkmain starting panel $url[2]",3 );
			$searchPanel = $url[2] . '%';
			$sql = $dbh->prepare("SELECT pkey FROM panel where classname like ?");
			$sql->execute(array($searchPanel));
			$panres = $sql->fetch();
			$sql = $dbh->prepare("SELECT perms FROM userpanel where user_pkey = ? AND panel_pkey = ?");
			$sql->execute(array($_SESSION['user']['pkey'],$panres['pkey']));
			$res = $sql->fetch();
			$helper->logit("srkmain starting panel $url[2] for user " . $_SESSION['user']['pkey'] . " with permissions " . $res['perms'],3 );	
			echo '<input type="hidden" id="perms" value="' . $res['perms'] .  '" />' . PHP_EOL;
			$_SESSION['user']['perms'] =  $res['perms'];
			$Panel->showForm();
		}
		else {
			die ("Class " . $url[2] . " not found");
		}
	}
	include $_SERVER["DOCUMENT_ROOT"] . "../php/srkfoot.php"; 
?>
