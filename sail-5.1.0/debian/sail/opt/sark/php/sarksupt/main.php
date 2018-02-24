<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SARK PBX</title>
<meta name="copyright" content="Copyright 2008-2012 CoCoSoft" />
<link rel="stylesheet" type="text/css" href="/sark-common/css/sark.css" /> 

</head>
<?php 
	require 'view.php'; 

	$Panel = new support;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
