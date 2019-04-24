<?php
require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
$path = "/opt/sark/";
if (isset($_GET['path'])) {
        $path =  strip_tags($_GET['path']);
} 
if (isset($_GET['dtype'])) {
	$path .= strip_tags($_GET['dtype']); 
	$path .= '/';
}
$path .= '/';
$fullPath = $path.strip_tags($_GET['dfile']);

if(ini_get('zlib.output_compression'))
    ini_set('zlib.output_compression', 'Off');


if( file_exists($fullPath) ) {

	$fsize = filesize($fullPath);
    $path_parts = pathinfo($fullPath);
    $ext = strtolower($path_parts["extension"]);
    switch ($ext) {
      case "pdf": $ctype="application/pdf"; break;
      case "exe": $ctype="application/octet-stream"; break;
      case "zip": $ctype="application/zip"; break;
      case "doc": $ctype="application/msword"; break;
      case "xls": $ctype="application/vnd.ms-excel"; break;
      case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
      case "gif": $ctype="image/gif"; break;
      case "png": $ctype="image/png"; break;
      case "jpeg":
      case "jpg": $ctype="image/jpg"; break;
      case "wav": $ctype="application/octet-stream"; break;
      case "mp3": $ctype="audio/mp3"; break;
      default: $ctype="application/force-download";
    }

    header("Pragma: public"); 
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Cache-Control: private",false); 
    header("Content-Type: $ctype");
    header("Content-Disposition: attachment; filename=\"".basename($fullPath)."\";" );
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$fsize);
    ob_clean();
    flush();
    readfile( $fullPath );
}
exit;
