<!DOCTYPE html>
<html lang="en">
<head>
<title>SARK PBX</title>
<meta name="copyright" content="Copyright 2018 Aelintra Telecom Limited" />
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://www.w3schools.com/w3css/4/w3.css">
</head>
<body>
<?php 
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/common.php";		
	require 'view.php'; 	  
	$Panel = new sarklogin;
	$Panel->showForm();
?>
</body>
</html>