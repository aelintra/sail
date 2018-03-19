#!/bin/bash

# UNTESTED - DO NOT USE

exit 0

DBROOT=/opt/sark/db				#db directory
SARKDB=sark.db					#updateable copy of the db
RECDIR="/var/spool/asterisk/monout/";
RECLIMIT=$(sqlite3 $DBROOT/$SARKDB "SELECT RECLIMIT FROM globals")

[ -z "$RECLIMIT" ] && echo "reclimit not set - exiting" && exit 4
echo reclimit is $RECLIMIT

RECDIRSIZE=$(du -sm /var/lib/asterisk/sounds | cut -f1)
echo recdirsize is $RECDIRSIZE

while [ $RECDIRSIZE -gt $RECLIMIT ]
do
	CANDIDATE = $(ls -t $RECDIR | tail -1)
	echo  "Reclimit threshold reached; $CANDIDATE will be deleted";
#	rm -rf $CANDIDATE
	RECDIRSIZE=$(du -sm /var/lib/asterisk/sounds | cut -f1)	
done

