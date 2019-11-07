<?php
// AMI EVENT READER v0.1 by Jeff Sherk - May 2011
//
// This script will continuously read all the Asterisk AMI events and output them to your browser
//
// This FREE SCRIPT is not copyrighted and is offered AS IS with no guarantees and no warrantees. Do what you want with it!

/////////////////////////////////////////////////
// NOTE: Required for this script to work, and also required is you want to use PHPAGI
/* MODIFY /etc/asterisk/manager.conf and add this (make sure user is asterisk 0664):
    [myamiclient]
    secret=********
    deny=0.0.0.0/0.0.0.0
    permit=127.0.0.1/255.255.255.0
    read = system,call,log,verbose,command,agent,user,config,command,dtmf,reporting,cdr,dialplan,originate
    write = system,call,log,verbose,command,agent,user,config,command,dtmf,reporting,cdr,dialplan,originate
/*

/////////////////////////////////////////////////
// NOTE: Only required if you want to use PHPAGI
/* MODIFY /etc/asterisk/phpagi.conf and add this (make sure user is asterisk 0777):
    [asmanager]
    server=127.0.0.1 ; server to connect to
    port=5038 ; default manager port
    username=myamiclient ; username for login
    secret=******** ; password for login
*/

//username and secret need to match the /etc/asterisk/manager.conf file
$username = 'sark';
$secret = 'mysark';

//Script should run forever, so prevent it from timing out
set_time_limit(0);

//Use fsockopen to connect the same way you would with Telnet
$fp = fsockopen("127.0.0.1", 5038, $errno, $errstr, 30);

//Unsuccessful connect
if (!$fp) {
    echo "$errstr ($errno)\n<br>";

//Successful connect
} else {

    //login
    fputs($fp,"Action: login\r\nUsername: ".$username."\r\nSecret: ".$secret."\r\n\r\n");
//TO DO: check if login was successful or not
    
    //LOOP FOREVER - continuously read data 
    $line = '';
    while(1) {
        $read = fread($fp,1); //Read one byte at a time from the socket
        $line .= $read;

        //Check if we are at the end of a line
        if ("\n" == $read) {

            //Determine when we have reached a blank line which 
            // signals the end of a single events info
            $event_separator = false;
            if ("\r\n" == $line) {
                $event_separator = true;
            }
//TO DO: Add code that does something when we have an entire events info

            echo $line.'<br>'; //DEBUG ONLY: For screen display. Remove <br> for db storage
//TO DO: How do we redirect echo statement so we can just save info in db?

            flush($fp); //Flush the stdout to get it to display
            $line = '';
            
        } //end IF -> Check if we are at the end of a line
    } //end WHILE -> LOOP FOREVER
    
    fclose($fp); //Will never get here, but looks good to have it!
} //end ELSE -> Successful connect

?>

