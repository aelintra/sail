<?php 
	require 'view.php'; 
	include "../srkhead.php"; 
?>
	<script type="text/javascript" src="/php/sarkagent/javascript.js" type="text/javascript"></script>	
	</head>
<?php  
	$Panel = new agent;
	$Panel->showForm();
	include "../srkfoot.php"; 
	echo '</div>'. PHP_EOL;
?>

