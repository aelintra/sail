
<?php 
	require 'view.php'; 
	include "../srkhead.php"; 	
	echo '<script type="text/javascript" src="/php/sarkglobal/javascript.js" type="text/javascript"></script>' . PHP_EOL;	
	echo '</head>' .  PHP_EOL;   
	$Panel = new globals;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
