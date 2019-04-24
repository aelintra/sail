#!/bin/sh

EXTBLKLST=`sqlite3 /opt/sark/db/sark.db 'SELECT EXTBLKLST FROM globals;'`

if [ "$EXTBLKLST" = "NO" ]; then
	logger SARKgetvoip - Blacklist not enabled - stopping
	exit
fi

URL="http://www.voipbl.org/update/"

set -e
logger "Downloading rules from VoIP Blacklist"
wget -qO - $URL -O /tmp/voipbl.txt

if [ ! -s  /tmp/voipbl.txt ]; then
	logger "No list returned from voipbl, - ending"
	exit
fi

logger SARKgetvoip - Loading rules...

# Check if rule set exists and create one if required
if ! $(ipset list voipbl > /dev/null 2>&1); then
  ipset -N voipbl iphash
fi
 
# Create temporary chain
ipset destroy voipbl_temp > /dev/null 2>&1 || true
ipset -N voipbl_temp iphash
 
cat /tmp/voipbl.txt |\
  awk '{ print "if [ ! -z \""$1"\" -a \""$1"\"  != \"#\" ]; then ipset  -A voipbl_temp \""$1"\" ;fi;"}' | sh
 
ipset swap voipbl_temp voipbl
ipset destroy voipbl_temp || true
 
logger  SARKgetvoip - Done! Rules loaded
