<?php

// check if the share is mounted
$mount = exec(sprintf('mount -l | grep %s', escapeshellcmd(NAS_NAME)));

// check that the nas is pingable
$ping = exec(sprintf('ping -c1 %s | grep Unreachable', escapeshellcmd(NAS_IP)));

// print a warning if necessary
if ($mount == null || empty($mount)) {
  $message = '<div class="warnbox"><h3>Warning:</h3><p>Network share is not mounted, you wont be able to see any recordings.  Pl
ease contact a system administrator</p></div><br/>';
} else if ($ping != null && !empty($ping)) {
  $message = '<div class="warnbox"><h3>Warning:</h3><p>Network share is not reachable, you wont be able to see any recordings.
Please contact a system administrator</p></div><br/>';
}

