
<?php 
	require 'view.php'; 
	include "../srkhead.php"; 
	echo '<script type="text/javascript" src="/php/sarkldap/javascript.js" type="text/javascript"></script>' . PHP_EOL;	
	echo '</head>' .  PHP_EOL;   
	$Panel = new ldap;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
