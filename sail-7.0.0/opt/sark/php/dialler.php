<?php
    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/AsteriskManager.php";
    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";


    $params = array('server' => '127.0.0.1', 'port' => '5038');
    $astrunning=false;

    $pkey=null;
    $extension=null;
    $msg = 'OK';
    $dbh = DB::getInstance();
    $helper = new helper;




    if ( $helper->check_pid() ) { 
        $astrunning = true;
    }
    else {
        $msg='(No Asterisk)';
    }
    
    if (isset($_POST['pkey'])) {
        $pkey = $_POST['pkey']; 
        syslog(LOG_WARNING, "PKEY IS " . $_POST['pkey']);
    }
    else {
        $msg = 'No pkey';
        syslog(LOG_WARNING, "NO PKEY");
    }   
    
    if (isset($_POST['number'])) {
        $extension = $_POST['number'];
       	syslog(LOG_WARNING, "POST NUMBER IS " . $_POST['number']);
    }
    else {
    	$msg = 'No pkey';
        syslog(LOG_WARNING, "NO NUMBER");
    }

    $querystring = "SELECT pkey,callbackto,cellphone,cluster from ipphone where pkey=?";   
    $sql = $dbh->prepare ($querystring);
    $sql->execute(array($pkey));
    $res = $sql->fetch();
    if ($res['callbackto'] == 'cell') {
        if (isset($res['cellphone'])) {
            $pkey=$res['cellphone'];
        }
    }

    if ($msg == 'OK') {
        if ($astrunning) {
            $ami = new ami($params);
            $amiconrets  = $ami->connect();
            if ( !$amiconrets ) {
                $msg .= "  (AMI Connect failed)";
            }
            else {

                $amiloginret = $ami->login('sark','mysark');
                syslog(LOG_WARNING, "amilogin=" . $amiloginret);
                $amisiprets = $ami->originateCall($extension, 
                           'Local/' . $pkey . '@' . $res['cluster'] . 'COS',
                           $res['cluster'] . 'COS', 
                           $pkey);       
                $amirets = $ami->logout();
                syslog(LOG_WARNING, "amiret=" . $amirets);
                $msg='Good Connect';
            }
        }
    }

    $var['msg'] = $msg;

    echo  json_encode($var, JSON_NUMERIC_CHECK);

    
?>