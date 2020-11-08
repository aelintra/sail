<?php

$ext_search = $_POST['extension'];
$num_search = $_POST['number'];


$from_time_search = strtotime($_POST['date'] . ' ' . $_POST['start_time'] . ':00');
$to_time_search = strtotime($_POST['date'] . ' ' . $_POST['end_time'] . ':59');


$folder = RECORDING_DIR . date(RECORDING_DIR_DATE_FORMAT, $from_time_search) . '/';

syslog(LOG_WARNING, "Recording folder is $folder");


function wavDur($file)
{
    $fp = fopen($file, 'r');
    if (fread($fp,4) == 'RIFF')
    {
        fseek($fp, 20);
        $rawheader = fread($fp, 16);
        $header = unpack('vtype/vchannels/Vsamplerate/Vbytespersec/valignment/vbits',$rawheader);

        $pos = ftell($fp);
        while (fread($fp,4) != "data" && !feof($fp))
        {
            $pos++;
            fseek($fp,$pos);
        }
        $rawheader = fread($fp, 4);
        $data = unpack('Vdatasize',$rawheader);
        $sec = $data[datasize]/$header[bytespersec];
        $minutes = intval(($sec / 60) % 60);
        $seconds = intval($sec % 60);

        return str_pad($minutes,2,'0', STR_PAD_LEFT).':'.str_pad($seconds,2,'0', STR_PAD_LEFT);
    }

    return '0:00';
}



$recordings = new RecordingList();

if (is_dir($folder))
{
    $dir = opendir($folder);


    // If there's a tenant name then we expect an extra field in the file name
    $offset = strlen($TENANT)>0 ? 1 : 0;
    syslog(LOG_WARNING, "Tenant is  $TENANT, offset is $offset");
    
    if (defined('OLD_FORMAT_CUTOFF_DATE'))
    {
        if (strtotime($_POST['date']) <= strtotime(OLD_FORMAT_CUTOFF_DATE))
        {
            // No offsets needed at this point!
            $offset = 0;
        }
    }

    $recordings->setOffset($offset);

    while ($file=readdir($dir))
    {
        syslog(LOG_WARNING, "Considering file $file");
        if ($file == '.' || $file == '..' || is_dir($file))
        {
            
            syslog(LOG_WARNING, "ignoring .{.} file $file");
            continue;
        }
// replace with preg_split for php7
//        $file_list = split('[-.]', $file);

	$file_list = preg_split('/[-.]/', $file);	

        // Unless the filename contains the tenant name then we don't want to include this recording
        if ($offset > 0 && $TENANT != 'MASTER-USER' && $file_list[1] != $TENANT)
        {
            syslog(LOG_WARNING, "Failed tenant rule $file");
            continue;
        }

        $queue_prefix = (strpos($file_list[0], 'Qexec') === 0);

        // Fix queue names with dashes in them
        if (count($file_list) == 6+$offset)
        {
            // As an example: 1462471158-default-R-Booking-Agent1010-7590690929.wav
            $file_list = array_merge(
                array_slice($file_list, 0, 2),
                array(implode('-', array_slice($file_list, 2, 2))),
                array_slice($file_list, 4)
            );
        }
        syslog(LOG_WARNING, "Time series (epoch) $from_time_search $to_time_search");
        if ($queue_prefix || count($file_list) == 5+$offset)
        {
            # (epoch, queuename, agent/ext, CLID)

            if ($queue_prefix)
            {
                $file_list[0] = str_replace('Qexec', '', $file_list[0]);

                // Queue name shows as "-" in web interface
                array_splice($file_list, 1+$offset, 0, array('-'));
            }

            # only add files with timestamp between the right dates
            
            if (($file_list[0] <= $to_time_search) && ($file_list[0] >= $from_time_search))
            {
                //$file_list[0] = date('D, j M Y H:i:s', $file_list[0]);
                $file_list[4+$offset] = wavDur($folder . $file);

                if (
                        (($ext_search == null) && ($num_search == null)) ||
                        (($ext_search != null) && (($file_list[2+$offset] == ('Agent' . $ext_search)) || ($file_list[2+$offset] == ('SIP' . $ext_search)))) ||
                        (($num_search != null) && (strpos($file_list[3+$offset], $num_search) !== false))
                   )
                {
                    $recordings->addCall($file_list, $file, $folder);
                }
            }
        }

        # queued calls now have three elements whereas normal calls have three
        else if (count($file_list) == 4+$offset)
        {
            syslog(LOG_WARNING, "Epoch dialled num and CLID $file");
            # (epoch, dialled num, CLID)
            # only add files with timestamp between the right dates
            
            if (($file_list[0] <= $to_time_search) && ($file_list[0] >= $from_time_search))
            {
                //$file_list[0] = date('D, j M Y H:i:s', $file_list[0]);
                $file_list[3+$offset] = wavDur($folder . $file);

                # if a number filter has been entered
                if (
                        (($ext_search == null) && ($num_search == null)) ||
                        (($ext_search != null) && (($file_list[1+$offset] == $ext_search) || ($file_list[2+$offset] == $ext_search))) ||
                        (
                            ($num_search != null) &&
                            (
                                ((strpos($file_list[1+$offset], $num_search) !== false) && (strlen($file_list[1+$offset]) > 4 )) ||
                                ((strpos($file_list[2+$offset], $num_search) !== false) && (strlen($file_list[2+$offset]) > 4))
                            )
                        )
                   )
                {
                    syslog(LOG_WARNING, "adding $file to result set");
                    $recordings->addCall($file_list, $file, $folder);
                }

            }
            else
            {
                error_log(sprintf('File %s is outside expected time window: %s to %s',
                    $file, $from_time_search, $to_time_search));
                syslog(LOG_WARNING, "$file is outside time window");
            }
        }
    }
    
    closedir($dir);
}
else {
    syslog(LOG_WARNING, "Recording folder is not a Directory");
}


if ($recordings->resultsFound() == false)
{
    echo 'No results found.';
}
else
{

    foreach ($recordings->getCallTypes() as $type)
    {
        if ($recordings->hasCallsOfType($type))
        {
            $recordings->generateTableOfType($type);
        }
    }
}
