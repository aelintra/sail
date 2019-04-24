<?php

//    echo rand(0,100);
//    return;

    $var=array();

/*        
        $cpuinfo = file_get_contents('/proc/cpuinfo');
        preg_match_all('/^processor/m', $cpuinfo, $matches);
        $var ['numCpus'] = count($matches[0]);
*/

        $iowait = `iostat -c|awk '/^ /{print $4}'`;
        $var['iowait'] = trim($iowait); 

        $free = shell_exec('free');
        $free = (string)trim($free);
        $free_arr = explode("\n", $free);
        $mem = explode(" ", $free_arr[1]);
        $mem = array_filter($mem);
        $mem = array_merge($mem);
        $memory_usage = round($mem[2]/$mem[1]*100); 
        $var['mem'] = $memory_usage;
        
        $ccount = `sudo /usr/sbin/asterisk -rx 'core show channels count'`;
        $acount = explode(PHP_EOL,$ccount);
        
        preg_match("/^(\d+)/",$acount[1],$matches);
        $var['upcalls'] = $matches[1];

        $disk = `/bin/df -h`;

        if ($disk) {
            $diskusage = preg_match ( '/(\d{1,2})\%/', $disk,$matches);
            $var['disk'] = $matches[1];
        }
        else {
            $var['disk'] = 'unknown'; 
        }
        $cpu_usage = sys_getloadavg();
        $var['lga'] = $cpu_usage[0];
        $var['lgb'] = $cpu_usage[1];
        $var['lgc'] = $cpu_usage[2];


        if (file_exists("/opt/sark/cache/speedtest")) {
            $downlink = `/bin/grep Down /opt/sark/cache/speedtest`;
            $uplink = `/bin/grep Up /opt/sark/cache/speedtest`;
            if ($downlink) {
                $speed = explode(':',$downlink);
                $num  = explode(' ',$speed[1]);
                $var['dlink'] =  $num [1];
            }
            else {
                $var['dlink'] =  0;
            }
            if ($uplink) {
                $speed = explode(':',$uplink);
                $num  = explode(' ',$speed[1]); 
                $var['ulink'] =  $num [1];
            } 
            else {
                $var['ulink'] = 0;
            }
        }
        

//    syslog(LOG_WARNING, "system.php sending values");

    if ($var) {      
       echo  json_encode($var, JSON_NUMERIC_CHECK);
    }
?>