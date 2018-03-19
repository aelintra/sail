#!/bin/bash
#
# Delete opened voicemail older than globals['VMAILAGE'] days
#
ZERO=0
VMAILAGE=`/usr/bin/sqlite3 /opt/sark/db/sark.db "SELECT VMAILAGE FROM globals WHERE pkey='global'"`
#echo deleting vmail older than $VMAILAGE days
if [ ! -z $VMAILAGE ]; then
	if [ $VMAILAGE -gt $ZERO ]; then
		echo "Deleting vmail older than $VMAILAGE days"
		find /var/spool/asterisk/voicemail/default/*/Old -name "msg*" -type f -mtime +$VMAILAGE -exec rm {} \;
	else
		echo "no vmail ageing (variable is zero)"
	fi
else
	echo "no vmail ageing (variable is null)"
fi

