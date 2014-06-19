<?php
$path = "/var/spool/asterisk/monitor/";
$dir = opendir($path);

while ($file=readdir($dir)) {
  if ($file != "." && $file != ".." && !is_dir($file)) {
    $folder = date("dmy", substr($file, 0, strpos($file, "-")));
    if (!file_exists("$path$folder")) {
      mkdir("$path$folder");
      echo "Creating: $folder \n";                                                                                                                                    
    }
    rename("$path$file", "$path$folder/$file");
  }

 }

closedir($dir);

?>
