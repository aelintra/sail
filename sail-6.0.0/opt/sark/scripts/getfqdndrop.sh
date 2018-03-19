#!/bin/sh

logger SARKgetfqdndrop - Offloading rules...

FQDNDROPBUFF=`sqlite3 /opt/sark/db/sark.db 'SELECT FQDNDROPBUFF FROM globals;'`

if [ ! -e /opt/sark/cache/fqdndrop ]; then
	logger "SARKgetfqdndrop /opt/sark/cache/fqdndrop not present - ending"
	exit
fi

LINECNT=$(wc -l < /opt/sark/cache/fqdndrop)

if [ "$LINECNT" -lt "$FQDNDROPBUFF" ]; then
	logger "SARKgetfqdndrop drop set has less than $FQDNDROPBUFF entries - ending"
	exit
fi

tail -n $FQDNDROPBUFF /opt/sark/cache/fqdndrop > /tmp/fqdndrop
cp /tmp/fqdndrop /opt/sark/cache/fqdndrop

# Create temporary chain
ipset destroy fqdndrop_temp > /dev/null 2>&1 || true
ipset -N fqdndrop_temp iphash
 
cat /opt/sark/cache/fqdndrop |\
  awk '{ print "if [ ! -z \""$1"\" -a \""$1"\"  != \"#\" ]; then ipset  -A fqdntrust_temp \""$1"\" ;fi;"}' | sh
 
ipset swap fqdndrop_temp fqdndrop
ipset destroy fqdndrop_temp || true
 
logger  "SARKgetfqdndrop - Done! Rules loaded"