<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<title>SARK PBX</title>
<link rel="stylesheet" type="text/css" href="/sark-common/css/sark_login.css" /> 
</head>
<body>
<div id="sitediv">	
<?php 
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/common.php";		
	require 'view.php'; 	  
	$Panel = new sarklogin;
	$Panel->showForm();
?>
</body>
</html>
