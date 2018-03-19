<?

session_start();

function cdrpage_getpost_ifset($test_vars)
{
	if (!is_array($test_vars)) {
		$test_vars = array($test_vars);
	}
	foreach($test_vars as $test_var) { 
		if (isset($_POST[$test_var])) { 
			global $$test_var;
			$$test_var = $_POST[$test_var]; 
		} elseif (isset($_GET[$test_var])) {
			global $$test_var; 
			$$test_var = $_GET[$test_var];
		}
	}
}


cdrpage_getpost_ifset(array('s', 't'));


$array = array ("", "CDR REPORT", "CALLS COMPARE", "MONTHLY TRAFFIC","DAILY LOAD");
$s = $s ? $s : 1;
$section="section$s$t";

$racine=$PHP_SELF;
$update = "03 March 2005";

$paypal="NOK"; //OK || NOK
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
	<head>		
		<title>Asterisk CDR</title>
		<meta http-equiv="Content-Type" content="text/html">
		<link rel="stylesheet" type="text/css" media="print" href="css/print.css">
		<SCRIPT LANGUAGE="JavaScript" SRC="./encrypt.js"></SCRIPT>
		<style type="text/css" media="screen">
			@import url("css/layout.css");
			@import url("css/content.css");
			@import url("css/docbook.css");
		</style>
		<meta name="MSSmartTagsPreventParsing" content="TRUE">
	</head>
	<body>
	
	

	
	
		<!-- header BEGIN -->
<!--		<div id="fedora-header">
			
			<div id="fedora-header-logo">
				 <table border="0" cellpadding="0" cellspacing="0"><tr>
				 <td><img src="images/sark-colour-web.jpg"  alt="CDR (Call Detail Records) Software by Areski"></td> 
				 <td>
				 <H1><font color=#990000>&nbsp;&nbsp;&nbsp;CDR (Call Detail Records)</font></H1>
				 </td>
				 </tr>
				 </table>
			</div> 

		</div>-->
		
		<div id="fedora-nav"></div>
		<!-- header END -->
		<br>


		<!-- content BEGIN -->
		<div id="fedora-middle-two">
			<div class="fedora-corner-tr">&nbsp;</div>
			<div class="fedora-corner-tl">&nbsp;</div>
			<div id="fedora-content">



<?if ($section=="section0"){?>

	<?require("call-log.php");?>

<?}elseif ($section=="section1"){?>

	<?require("call-log.php");?>

<?}elseif ($section=="section2"){?>

	<?require("call-comp.php");?>

<?}elseif ($section=="section3"){?>

	<?require("call-last-month.php");?>

<?}elseif ($section=="section4"){?>

	<?require("call-daily-load.php");?>

<?}else{?>
	<h1>Coming soon ...</h1>
   
<?}?>

		
		<br><br><br><br><br><br>
		</div>

			<div class="fedora-corner-br">&nbsp;</div>
			<div class="fedora-corner-bl">&nbsp;</div>
		</div>
		<!-- content END -->
		
		<!-- footer BEGIN -->
		<div id="fedora-footer">

			<br>			
		</div>
		<!-- footer END -->
	</body>
</html>
