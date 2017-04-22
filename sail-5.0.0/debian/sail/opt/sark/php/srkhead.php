 <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php
ob_start();
if (!strpos($_SERVER['SCRIPT_URL'],'sarklogin')) {
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/session.php";
}
else {
	require_once $_SERVER["DOCUMENT_ROOT"] . "../php/srksessions/common.php";
}
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>SARK PBX</title>
<meta name="copyright" content="Copyright 2008-2015 CoCoSoft" />
<link rel="stylesheet" type="text/css" href="/sark-common/css/sark.css" /> 
<link rel="stylesheet" type="text/css" href="/sark-common/js/jquery-ui-1.10.4.custom/development-bundle/themes/custom-theme/jquery.ui.all.css" media="screen" /> 
<link rel="stylesheet" type="text/css" href="/sark-common/js/jquery.qtip.css" media="screen" />
<link rel="stylesheet" type="text/css" href="/sark-common/js/DataTables-1.10.10/css/dataTables.foundation.css" media="screen" />


<script type="text/javascript" src="/sark-common/js/jquery-1.11.0.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="/sark-common/js/datatables-1.10.10.min.js" type="text/javascript"></script>
<script type="text/javascript" src="/sark-common/jquery-datatables-editable-master/media/js/jquery.dataTables.editable.js" type="text/javascript"></script>
<script type="text/javascript" src="/sark-common/js/jquery.jeditable.js" type="text/javascript"></script>
<script type="text/javascript" src="/sark-common/js/jquery.validate.js" type="text/javascript"></script>
<script type="text/javascript" src="/sark-common/js/jquery-ui-1.10.4.custom/js/jquery-ui-1.10.4.custom.min.js"></script>
<script type="text/javascript" src="/sark-common/js/jquery.qtip.js"></script>

<script type="text/javascript">
	
function confirmOK(myMsg) {
	return window.confirm(myMsg);
}
			
$(document).ready(function() {
			
		$('.myMenu > li').bind('mouseover', openSubMenu);
		$('.myMenu > li').bind('mouseout', closeSubMenu);
		
		function openSubMenu() {
			$(this).find('ul').css('visibility', 'visible');	
		};
		
		function closeSubMenu() {
			$(this).find('ul').css('visibility', 'hidden');	
		};
		window.onload = init;
		function init(){
			if (document.getElementById('searchkey')) {
				document.getElementById("searchkey").focus();
			}
		}; 				   
});

</script>

<script type="text/javascript">
$(window).load(function() {
	$(".loader").fadeOut("slow");
})
</script>
 
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<!--[if lt IE 9]>
   <style type="text/css">
   li a {display:inline-block;}
   li a {display:block;}
   </style>
   <![endif]-->
<?php
	$url = explode('/', $_SERVER['SCRIPT_URL']);
	if (file_exists($_SERVER["DOCUMENT_ROOT"] . "../php/" . $url[2] . '/javascript.js')) {
		echo '<script type="text/javascript" src="/php/' . $url[2] . '/javascript.js" type="text/javascript"></script>' . PHP_EOL;
	}
	if ($url[2] == 'sarkivr' || $url[2] == 'sarkextension' ) {
			echo '<link rel="stylesheet" type="text/css" href="/sark-common/js/jquery.fancybox-2.1.5/source/jquery.fancybox.css" />'. PHP_EOL; 
			echo '<script type="text/javascript" src="/sark-common/js/jquery.fancybox-2.1.5/source/jquery.fancybox.js"></script>'. PHP_EOL;				
	}	
?>
</head>
<body>
<div class="loader"></div>	
<div class="sitediv">	
