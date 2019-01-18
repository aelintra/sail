<?php

    require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkDbClass";
    

    $xlateRelNames = array(

        "IPphone" => "Extension",
        "lineIO" => "Trunk/DDI",
        "speed" => "Ring group",
        "User" => "User",
        "meetme" => "Conference Room",
        "appl" => "Custom App",
        "ivrmenu" => "IVR",
        "Queue" => "Queue",
        "Greeting" => "Greeting",
        "agent" => "Agent",
        "COS" => "Class of Service",
        "Route" => "Outbound Route",
        "mcast" => "Multicast"
    );

    $var = array();
    $dbh = DB::getInstance();

    $qKey = $_REQUEST['searchkey'] . '%';

    syslog(LOG_WARNING, "search.php running request for $qKey");


    $sql = $dbh->prepare("SELECT pkey,relation FROM master_xref where pkey LIKE ? ORDER BY pkey COLLATE NOCASE");
    $sql->execute(array($qKey));
    $res = $sql->fetchAll();
    foreach ($res as $row) {
        if ( $row['relation'] == "Device" || $row['relation'] == "Carrier" || $row['relation'] == "dateSeg" ) {
            continue;
        }
        $relationName = $row['relation'];
        if (isset($xlateRelNames[$relationName])) {
            $relationName = $xlateRelNames[$relationName];
        }
        array_push($var, $row['pkey'] . ' (' . $relationName . ')' );
    }
 

//    syslog(LOG_WARNING, "search.php running");


    if ($var) {
//       echo "header('Content-Type: application/json')";        
       echo  json_encode($var, JSON_NUMERIC_CHECK);
    }
?>