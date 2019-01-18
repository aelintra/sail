<div class="w3-bar w3-white">
<?php
$imgsource = "/sark-common/Sark_Colour_web_2018.png";
if (file_exists("/opt/sark/www/sark-common/Customer_Branding_Colour_web.png")) {
	echo '<img src="/sark-common/Customer_Branding_Colour_web.png" class="w3-padding w3-image" alt="SARK UCS" style="max-height: 4em;">' . PHP_EOL;
}	
else {
	echo '<img src="/sark-common/Sark_Colour_web_2018.png" class="w3-padding w3-image" alt="SARK UCS" style="max-height: 4em;">' . PHP_EOL;	 
}
?>
</div>