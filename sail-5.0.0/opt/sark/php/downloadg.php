<?php
$fullPath = strip_tags($_GET['dfile']);
if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
        case "pdf":
        header("Content-type: application/pdf"); // add here more headers for diff. extensions
        header('Content-Disposition: attachment; filename="'.$path_parts["basename"].'"'); // use 'attachment' to force a download
        break;

        case "wav":
        case "mp3";
        case "gsm";
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="'.$path_parts["basename"].'"'); // use 'attachment' to force a download
        break;  
              
        default;
        header("Content-type: application/octet-stream");
        header('Content-Disposition: attachment; filename="'.$path_parts["basename"].'"'); // use 'attachment' to force a download
    }
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
else {
	die ("ERROR coudn't open $fullPath");
}
fclose ($fd);
exit;
?>
