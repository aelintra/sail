<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8"/>
        <title>SARK Call recording</title>
        <link href="css/humanity/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <link href="css/login.css" rel="stylesheet" type="text/css"/>
        <!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="css/ielt7.css" /><![endif]-->
        <script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.pngFix.js"></script>
        <script type="text/javascript">
        $(document).ready(function() {
<?php if (count($errors) > 0): ?>
            $('#loginform').effect('shake', { times: 3 }, 100, function (){$('#user_<?php echo $focus; ?>').focus();});
<?php else: ?>
            $('#user_<?php echo $focus; ?>').focus();
<?php endif; ?>
        });
        </script>
    </head>
    <body class="login">

        <div id="container">
            <div id="header">
                <div class="inner">
                    <div id="sark">
                        <p>Call Recording</p>
                    </div>
                    <img src="images/sark.png" id="logo" alt="SARK"/>
                </div>
            </div>


            <div id="results">

		        <fieldset id="login"><legend>Login</legend>

		        <?php if (count($errors) > 0): ?>
		        <div id="login_error"><strong>ERROR</strong>: <?php foreach ($errors as $error) printf("<li>%s</li>", $error); ?><br /></div>
		        <?php endif; ?>

		        <form name="loginform" id="loginform" action="?action=login" method="post">
		                <p>
		                        <label>Username<br />
		                        <input type="text" name="user_login" id="user_login" class="input" size="20" tabindex="20" autocomplete="off" value="<?php echo $username; ?>" /></label>
		                </p>
		                <p>
		                        <label>Password<br />
		                        <input type="password" name="user_pass" id="user_pass" class="input" value="" size="20" tabindex="30" autocomplete="off" /></label>
		                </p>

		                <p class="submit">
		                        <input type="submit" name="user_submit" id="user_submit" class="button" value="Log In" tabindex="100" />
		                </p>
		        </form>
		        </fieldset>

            </div>
            <div id="footer"></div>
        </div>

    </body>

</html>