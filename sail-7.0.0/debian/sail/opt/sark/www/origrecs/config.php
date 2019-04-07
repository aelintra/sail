<?php

define('DEBUG_MODE', false);

date_default_timezone_set('Europe/London');

/*
 * The application will look in the following dir for recordings
 */
define('RECORDING_DIR', 'recordings' . DIRECTORY_SEPARATOR);


/*
 * Date format of the subdirectories in RECORDING_DIR
 * REF: http://uk.php.net/manual/en/function.date.php
 */
define('RECORDING_DIR_DATE_FORMAT', 'dmy');


/*
 * Using mounted filesystems?
 */
define('ACCESSED_OVER_SHARE', false);

/*
 * Where is the NAS? Ping this to check if NAS is available.
 */
define('NAS_IP', '127.0.0.1');

/*
 * What should this appear as when doing a `mount`?
 */
define('NAS_NAME', 'recordings');

/*
 * Should we use our own login system?
 * If this is set then the authentication module is included.

$AUTH_LOGIN = array(
	'pdo_connection_string' => 'sqlite:/path/to/recordings.db',
	'admin' => array('username'=>'admin', 'password'=>'password')
);
*/

/*
 * Use the older call recording filename structure up-to and including this date.
 * A directory containing mixed results will incorrectly show recordings in the
 * newer format as queued calls.
*/
define('OLD_FORMAT_CUTOFF_DATE', '08-10-2013');
