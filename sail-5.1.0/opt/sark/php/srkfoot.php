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
		echo "PASSWORD IS FACTORY DEFAULT - YOUR SYSTEM IS AT RISK!";
		echo '</div>';
	}	

	if (!isset($_GET['edit'])) {
		echo '<span class="copyright" >Copyright &copy; CoCoSoft 2008-2016. All rights reserved.</span>';
	}
?>

</div>
</body>
</html>
<?php
ob_end_flush();
?>
