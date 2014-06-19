
<?php 
	require 'view.php'; 
	include "../srkhead.php"; 
	echo '<script type="text/javascript" src="/php/sarkedit/javascript.js" type="text/javascript"></script>' . PHP_EOL;	
?>
	<style>
	a:link {text-decoration:none;}    /* unvisited link */
	a:visited {text-decoration:none;} /* visited link */
	a:hover {text-decoration:underline;}   /* mouse over link */
	a:active {text-decoration:underline;}  /* selected link */
	</style>
<?php
	echo '</head>' .  PHP_EOL;   
	$Panel = new edit;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>
