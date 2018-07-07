<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srkHelperClass";
$helper = new helper; 
$fullPath = strip_tags($_GET['dfile']);
$fileperms = trim(`stat -c %a $fullPath`);
$helper->request_syscmd ( "/bin/chmod +r $fullPath" );
if ($fd = fopen ($fullPath, "r")) {
    $fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    header("Content-type: application/octet-stream");
    header('Content-Disposition: attachment; filename="'.$path_parts["basename"].'"'); // use 'attachment' to force a download
    header("Content-length: $fsize");
    header("Cache-control: private"); //use this to open files directly
    ob_clean();
    flush();
    while(!feof($fd)) {
        $buffer = fread($fd, 2048);
        echo $buffer;
    }   
}
else {
        $nethelper->logit("SRKDOWNLOADG - ERROR coudn't open $fullPath");
}
fclose ($fd);
$helper->request_syscmd ( "/bin/chmod $fileperms $fullPath" );
exit;
?>