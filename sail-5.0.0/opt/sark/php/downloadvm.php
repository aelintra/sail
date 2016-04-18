<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
$fullPath = strip_tags($_GET['dfile']);

if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    header("Content-type: application/octet-stream"); // add here more headers for diff. extensions
//    header("Content-Disposition: attachment; filename=\"". basename($fullPath, ".WAV" . "\"")); // use 'attachment' to force a download
    header("Content-Disposition: attachment; filename='". basename($fullPath . "'")); // use 'attachment' to force a download
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }
}
fclose ($fd);
exit;
?>
