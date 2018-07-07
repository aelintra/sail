<footer class="w3-bar w3-tiny w3-black " style="height:20em">	
<?php
	$url = explode('/', $_SERVER['SCRIPT_URL']);
		
	if ( $_SESSION['nag'] && $show ) {
		echo '<div class="nag">';
		echo "You must change the default password to continue";
		echo '</div>';
	}	

//	if ($url[2] == 'sarkglobal') {
		echo '<p class="w3-right w3-margin">&copy; Aelintra Telecom. All rights reserved.</p>';
//	}
?>
</footer>
</body>
</html>
<?php
ob_end_flush();
?>