
<?php 
	require 'view.php'; 
	include "../srkhead.php"; 
	echo '<script type="text/javascript" src="/php/sarkdevice/javascript.js" type="text/javascript"></script>' . PHP_EOL;	
	echo '</head>' .  PHP_EOL;   
	$Panel = new device;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
