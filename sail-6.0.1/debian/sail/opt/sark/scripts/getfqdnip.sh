#!/bin/sh

FQDNTRUST=`sqlite3 /opt/sark/db/sark.db 'SELECT FQDNTRUST FROM globals;'`

if ! $(/sbin/ipset list fqdntrust > /dev/null 2>&1); then
  /sbin/ipset -N fqdntrust iphash
fi

if ! $(/sbin/ipset list fqdndrop > /dev/null 2>&1); then
  /sbin/ipset -N fqdndrop iphash
fi

if [ "$FQDNTRUST" = "NO" ]; then
	logger SARKgetfqdnip - FQDN trust not enabled - stopping
	/sbin/ipset flush fqdntrust
	exit
fi

logger SARKgetfqdnip - Loading rules...

> /tmp/fqdntrustip.txt 

SET=`sqlite3 /opt/sark/db/sark.db 'SELECT fqdn FROM shorewall_whitelist;'`

for FQDN in $SET; do
	IP=`dig +short $FQDN`
	if [ ! -z "$IP" ]; then
		echo $IP >> /tmp/fqdntrustip.txt
	fi
done

[ ! -s /tmp/fqdntrustip.txt ] && logger SARKgetfqdnip - FQDN rules file is empty - continuing

# Create temporary chain
/sbin/ipset destroy fqdntrust_temp > /dev/null 2>&1 || true
/sbin/ipset -N fqdntrust_temp iphash
 
#/bin/cat /tmp/fqdntrustip.txt |\
#  /usr/bin/awk '{ /usr/bin/print "if [ ! -z \""$1"\" -a \""$1"\"  != \"#\" ]; then /sbin/ipset  -A fqdntrust_temp \""$1"\" ;fi;"}' | /bin/sh
  
file="/tmp/fqdntrustip.txt"
while IFS= read line
do
	/sbin/ipset add fqdntrust_temp $line
	/sbin/ipset add fqdndrop $line
done <"$file"  

/sbin/ipset swap fqdntrust_temp fqdntrust
/sbin/ipset destroy fqdntrust_temp || true
 
logger  SARKgetfqdnip - Done! Rules loaded
