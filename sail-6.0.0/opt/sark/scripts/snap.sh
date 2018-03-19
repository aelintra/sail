#!/bin/bash
#
# Spin off a snap of the database  
#
/bin/cp -a /opt/sark/db/sark.db /opt/sark/snap/sark.db.`date +%s`
/bin/ls -t /opt/sark/snap/* | /bin/sed -e '1,9d' | /usr/bin/xargs -d '\n' /bin/rm > /dev/null 2>&1

