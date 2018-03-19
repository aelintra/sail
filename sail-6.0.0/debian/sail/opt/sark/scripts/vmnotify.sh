#!/bin/bash

VM_CONTEXT=$1
EXTEN=$2
VM_COUNT=$3

ASTCMD='/usr/sbin/asterisk -rx'

if [ $VM_COUNT = "0" ]; then
	$ASTCMD 'devstate change Custom:vm'"$EXTEN"' NOT_INUSE'
else
	$ASTCMD 'devstate change Custom:vm'"$EXTEN"' INUSE'
fi

exit 0
