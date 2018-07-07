#!/bin/bash
#
# age the backups and delete anything older than 7 days 
#
  find /opt/sark/bkup/sark.db* -type f -mtime +7 -exec rm {} \;


