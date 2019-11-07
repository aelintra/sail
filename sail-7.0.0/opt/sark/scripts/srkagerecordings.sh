#!/bin/sh
#
# delete recordings older than $RECAGE days
#
RECAGE="+60"
find /opt/sark/www/origrecs/recordings/*  -mtime $RECAGE -type d -exec rm -rf {} +

