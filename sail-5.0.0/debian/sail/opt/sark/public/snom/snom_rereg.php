<?php
sleep (3);
syslog(LOG_WARNING, " Sending re-registration command to " . $_SERVER["REMOTE_ADDR"]);	
$url = 'http://'. $_SERVER['REMOTE_ADDR'] .'/dummy.htm?REREGISTER:1=Re-Register';
readfile($url);
?>
