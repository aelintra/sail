</form>
<div class="push"></div>
</div>
<div class="footer">	
<?php
	$url = explode('/', $_SERVER['SCRIPT_URL']);
	$show = True;
	if ( isset($_GET['edit']) ) {
		if ($url[2] == 'sarktrunk' || $url[2] == 'sarkextension') {
			$show = False;
		}
	}
		
	if ( $_SESSION['nag'] && $show ) {
		echo '<div class="nag">';
		echo "You must change the default password to continue";
		echo '</div>';
	}	

	if (!isset($_GET['edit'])) {
		echo '<span class="copyright" >Copyright &copy; Aelintra Telecom 2008-2018. All rights reserved.</span>';
	}
?>

</div>
</body>
</html>
<?php
ob_end_flush();
?>
