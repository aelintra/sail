
<?php 
	require 'view.php'; 
	include "../srkhead.php"; 
	echo '<script type="text/javascript" src="/php/sarkcallback/javascript.js" type="text/javascript"></script>' . PHP_EOL;	
	echo '</head>' .  PHP_EOL;   
	$Panel = new callback;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
