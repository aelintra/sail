<?php

    $servername = "localhost";
    $username = "asterisk";
    $password = "aster1sk";


    try {
        $pdo = new PDO("mysql:host=$servername;dbname=asterisk", $username, $password);
    // set the PDO error mode to exception
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//        syslog(LOG_WARNING, "Connected successfully"); 
    }
        catch(PDOException $e)
    {
        syslog(LOG_WARNING, "Connection failed: " . $e->getMessage() );
    }

    $rets=$pdo->prepare('SELECT COUNT(*) from cdr WHERE length(dst) > 5 AND calldate > CURDATE();');
    $rets->execute();
    $var['outbound'] = $rets->fetchColumn();
    $rets=$pdo->prepare('SELECT COUNT(*) from cdr WHERE length(src) > 5 AND calldate > CURDATE();');
    $rets->execute();
    $var['inbound'] = $rets->fetchColumn();
    $rets=$pdo->prepare('SELECT COUNT(*) from cdr WHERE length(dst) < 5 AND length(src) < 5 AND calldate > CURDATE();');
    $rets->execute();
    $var['internal'] = $rets->fetchColumn();
    $pdo=null;


//    syslog(LOG_WARNING, "cdrcount.php sending values");
//    print_r ($var);
    if ($var) {
//       echo "header('Content-Type: application/json')";        
       echo  json_encode($var, JSON_NUMERIC_CHECK);
    }
?>