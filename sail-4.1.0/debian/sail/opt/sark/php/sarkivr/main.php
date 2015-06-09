
<?php 
	require 'view.php'; 
	include "../srkhead.php"; 
	echo '<link rel="stylesheet" type="text/css" href="/sark-common/js/jquery.fancybox-2.1.5/source/jquery.fancybox.css" />'. PHP_EOL; 
	echo '<script type="text/javascript" src="/sark-common/js/jquery.fancybox-2.1.5/source/jquery.fancybox.js"></script>'. PHP_EOL;		
	echo '<script type="text/javascript" src="/php/sarkivr/javascript.js" type="text/javascript"></script>' . PHP_EOL;	
	echo '</head>' .  PHP_EOL;   
	$Panel = new ivr;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
