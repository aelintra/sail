<?php

# if ( `/bin/ps -e  | /bin/grep asterisk | /bin/grep -v grep` == "") {
# 	echo "Asterisk is NOT running \n";
# 	exit (-1);
# }


 $socket = fsockopen("127.0.0.1","5038", $errno, $errstr, 1)
        or exit("unable to open AMI socket \n");

 fputs($socket, "Action: Login\r\n");
 fputs($socket, "UserName: sark\r\n");
 fputs($socket, "Secret: mysark\r\n\r\n");

 fputs($socket, "Action: Command\r\n");
 fputs($socket, "Command: reload\r\n\r\n");


 fputs($socket, "Action: Logoff\r\n\r\n");

 $count=0;$array;
 while (!feof($socket)) {
     $wrets = fgets($socket, 8192);
#     $token = strtok($wrets,':(');
#     $j=0;
#     while($token!=false & $count>=5) {
#     	$array[$count][$j]=$token;
#        $j++; $token = strtok(':(');
#     }
#     $count++;
#     $wrets .= '<br>';
 }

 fclose($socket); ;

?>
