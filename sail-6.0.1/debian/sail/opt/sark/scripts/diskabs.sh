#!/bin/bash

# UNTESTED - DO NOT USE

DBROOT=/opt/sark/db				#db directory
SARKDB=sark.db					#updateable copy of the db
RECDIR="/opt/sark/www/origrecs/recordings/";
RECLIMIT=$(sqlite3 $DBROOT/$SARKDB "SELECT RECLIMIT FROM globals")

[ -z "$RECLIMIT" ] && logger "reclimit not set - exiting" && exit 4
logger "srkdiskabs - reclimit is $RECLIMIT"

RECDIRSIZE=$(du -sm /var/lib/asterisk/sounds | cut -f1)
logger "srkdiskabs - recdirsize is $RECDIRSIZE"

while [ $RECDIRSIZE -gt $RECLIMIT ]
do
	CANDIDATE = $(ls -t $RECDIR | tail -1)
	logger  "srkdiskabs - Reclimit threshold reached; $CANDIDATE will be deleted";
#	rm -rf $CANDIDATE
	RECDIRSIZE=$(du -sm /var/lib/asterisk/sounds | cut -f1)	
done