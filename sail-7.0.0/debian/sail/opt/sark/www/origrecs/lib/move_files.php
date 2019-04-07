<?php
$path = "/var/www/recordings/main/";
$dir = opendir($path);

while ($file=readdir($dir)) {
  if ($file != "." && $file != ".." && !is_dir($file)) {
    $folder = date("dmy", substr($file, 0, strpos($file, "-")));
    // echo "$folder \n";
    if (!file_exists("$path$folder")) {
	mkdir("$path$folder");
    }
    rename("$path$file", "$path$folder/$file");
  }

}

closedir($dir);

?>