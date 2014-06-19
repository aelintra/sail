
<?php 
	require 'view.php'; 
	include "../srkhead.php"; 
	echo '<script type="text/javascript" src="/php/sarkpasswd/javascript.js" type="text/javascript"></script>' . PHP_EOL;	

?>	
	</head> 
<?php  
	$Panel = new passwd;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
