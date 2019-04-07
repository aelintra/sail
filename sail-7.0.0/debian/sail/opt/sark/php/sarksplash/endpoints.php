<?php

    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkAmiHelperClass";
    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";

    $var=array();
    $var['msg'] = 'OK';

    $astrunning = false;

    $dbh = DB::getInstance();
    $helper = new helper;

    if ( $helper->check_pid() ) { 
        $astrunning = true;
    }

    if ( $astrunning ) { 
        $amiHelper = new amiHelper();
        $sip_peers = $amiHelper->get_peer_array();
        $iax_peers = $amiHelper->get_peer_array(true);
    }
    else {
        $var['msg'] = "No Asterisk running";
        echo  json_encode($var, JSON_NUMERIC_CHECK);
        exit;
    }

/*
    Phone count
 */
    $sql = "select pkey from ipphone";
    $res = $dbh->query($sql);    
    $rows = $res->fetchAll();

    $var['phoneCount'] = 0;
    $var['phoneUpCount'] = 0;

    foreach ($rows as $row) {
        $var['phoneCount']++;
        if (isset ($sip_peers [$row['pkey']]['IPaddress']) && $sip_peers [$row['pkey']]['IPaddress'] != '-none-') {
            $var['phoneUpCount']++;
        }
    } 
    
/*
    Trunk count
 */
    $sql = "select li.pkey,cluster,description,trunkname,peername,routeclassopen,routeclassclosed,active,ca.technology,ca.carriertype " . 
            "from lineio li inner join carrier ca  on li.carrier=ca.pkey";
    $res = $dbh->query($sql);    
    $rows = $res->fetchAll();

    $var['trunkCount'] = 0;
    $var['trunkUpCount'] = 0;

    foreach ($rows as $row) {
        if ($row['carriertype'] == 'DiD' || $row['carriertype'] == 'CLID' || $row['carriertype'] == 'Class' || $row['active'] == 'NO') {
            continue;
        }
        $searchkey = $row['peername'];
        $var['trunkCount']++;
        if ($row['technology'] == 'SIP' ) {
                if (preg_match(' /\((\d+)\sms/ ',$sip_peers [$searchkey]['Status'],$matches)) {
                    $var['trunkUpCount']++;
                }
        } 
        else if ($row['technology'] == 'IAX2') {
                if (preg_match(' /\((\d+)\sms/ ',$iax_peers [$searchkey]['Status'],$matches)) {
                    $var['trunkUpCount']++;
                }
        }
        else {
            $var['trunkUpCount']++;
        } 

    }           

    syslog(LOG_WARNING, "endpoints.php sending values");

    if ($var) {      
       echo  json_encode($var, JSON_NUMERIC_CHECK);
    }
?>