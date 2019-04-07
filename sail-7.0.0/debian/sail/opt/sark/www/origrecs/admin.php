<?php

(@include 'config.php') or die('Please create your config.php before proceeding.');

require 'lib/admin.php';


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

    <head>
        <title>SARK Call recording</title>
        <link href="css/humanity/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css"/>
        <link href="css/tipTip.css" rel="stylesheet" type="text/css"/>
        <link href="css/styles.css" rel="stylesheet" type="text/css"/>
        <!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="css/ielt7.css" /><![endif]-->
        <script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
        <script type="text/javascript" src="js/jquery.pngFix.js"></script>
        <script type="text/javascript" src="js/jquery.tipTip.minified.js"></script>
    </head>

    <body>
<?php if (isset($message)) echo "{$message}\n"; ?>
        <div id="container">
            <div id="header">
                <div class="inner">
                    <div id="sark">
                        <p>Call Recording Administration</p>
<?php if (isset($AUTH_LOGIN)) printf("Logged in as %s <a href=\"?action=logout\" class=\"button\">Log Out</a>", $ADMIN); ?>
                    </div>
                    <img src="images/sark.png" id="logo" alt="SARK"/>
                </div>
            </div>


            <div id="results">
            	<p>User Administration</p>
            	<table>
            		<tr>
            			<th>id</th><th>name</th><th>Remove?</th>
            		</tr>
            		<?php
	            		$statement = DB::instance()->query('SELECT `id`, `name` FROM `users`');
	            		while (($row = $statement->fetchObject()) != null) {
	            			printf("<tr><td>%d</td><td>%s</td><td><a href=\"?action=deleteuser&amp;id=%d\">[X]</a></td></tr>\n", $row->id, $row->name, $row->id);
	            		}
            		?>
                    <tr>
                        <td colspan="3">
            		        <form action="?action=adduser" method="post">
            		            Username: <input type="text" name="username" id="username" /><br/>
            		            Password: <input type="password" name="password" id="password" /><br/>
            		            Confirm password: <input type="password" name="password2" id="password2" /><br/>
            			        <input type="submit" class="button" value="add"/>
            		        </form>
                        </td>
                    </tr>
            	</table>
            </div>
            <div id="footer"></div>
            <div id="player"></div>
        </div>
    </body>

</html>