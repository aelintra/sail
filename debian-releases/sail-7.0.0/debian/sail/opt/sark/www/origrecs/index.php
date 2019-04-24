<?php

// Newer vesions of SARK now populate the tenant field when creating a call recording file 
$TENANT = 'default';

(@include 'config.php') or die('Please create your config.php before proceeding.');

if (isset($AUTH_LOGIN)) {
	require('lib/auth.php');
}

if (defined('ACCESSED_OVER_SHARE') && ACCESSED_OVER_SHARE) {
    require('lib/mount.php');
}

$results = '';

if ($_POST) {
    ob_start();
    include('lib/Recording.class.php');
    include('lib/recording_results.php');
    $results = ob_get_contents();
    ob_end_clean();

    if (array_key_exists('ajax', $_POST)) {
        echo $results;
        exit();
    }
}

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">

    <head>
        <title>SARK Call recording</title>
        <link href="css/humanity/jquery-ui-1.8.16.custom.css" rel="stylesheet" type="text/css"/> 
<!--        <link href="css/tipTip.css" rel="stylesheet" type="text/css"/>
        <link href="css/jPlayer/blue.monday/jplayer.blue.monday.css" rel="stylesheet" type="text/css"/> -->
        <link href="css/styles.css" rel="stylesheet" type="text/css"/> 

        <!--[if lte IE 7]><link rel="stylesheet" type="text/css" href="css/ielt7.css" /><![endif]-->

        <script type="text/javascript" src="js/jquery-1.6.2.min.js"></script>
        <script type="text/javascript" src="js/jquery-ui-1.8.16.custom.min.js"></script>
<!--        <script type="text/javascript" src="js/jquery.pngFix.js"></script>  -->
<!--        <script type="text/javascript" src="js/jquery.tipTip.minified.js"></script> -->
        <script type="text/javascript" src="js/jPlayer/jquery.jplayer.min.js"></script>
        <script type="text/javascript" src="js/sark-recording.js"></script>
    </head>

    <body>
<?php if (isset($message)) echo "{$message}\n"; ?>
        <div id="container">
<!--            
            <div id="header">
                <div class="inner">
                    <div id="sark">
                        <p>Call Recording</p>
<?php if (isset($AUTH_LOGIN)) printf("Logged in as %s <a href=\"?action=logout\" class=\"button\">Log Out</a>", $TENANT); ?>
                    </div>
                    <img src="images/sark.png" id="logo" alt="SARK"/>
                </div>
            </div>
-->
            <div id="filter">
                <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="post">
                <span id="trigger" class="nojs hidden"><span class="ui-icon ui-icon-arrowthick-1-n"></span><span>hide</span></span>
                <div class="inner">

                    <div class="col">
                        <h2>Filter results:</h2>
                        <br/>
                        <div class="date">
                            <p>
                                Date:<br/>
                                <input type="text" name="date" value="<?php echo date('d-m-Y'); ?>" class="datepicker"/>
                            </p>
                        </div>

                        <div>
                            <p>
                                <label for="amount">Time Range:</label>
                                <input type="text" id="amount"/>
                                <input type="hidden" name="start_time"/>
                                <input type="hidden" name="end_time"/>
                            </p>

                            <div id="slider-range"></div>

                        </div>
                    </div>
                    <br/>
                    <div class="col">
                        <p>
                            <span class="tip-tip ui-icon ui-icon-info" title="If you only want to see calls made or received by a specific extension number or agent number. Note: only agent number can only be used for inbound queued calls."></span>
                            Filter by extension or agent number:<br/>
                            <input type="text" name="extension"/>

                        </p>
                        <p>
                            <span class="tip-tip ui-icon ui-icon-info" title="If you only want to see calls from or to a certain number. Can type a full phone number or a partial number (e.g. typing 07 will search for any number containing 07)."></span>
                            Filter by called/calling number:<br/>
                            <input type="text" name="number"/>
                        </p>
                    </div>


                    <input type="submit" class="button" id="search-button" value="search"/>
                </div>

                <div class="clear"></div>
                </form>
            </div>

            <div id="results"><?php echo $results; ?></div>
            <div id="footer"></div>
            <div id="player"></div>
        </div>
    </body>

</html>