#!/bin/bash
if mount | grep "/dev/sda1 on /media/usbdrive type" > /dev/null
then
 /usr/bin/rsync --remove-source-files /var/spool/asterisk/monout/* /media/usbdrive/sound/`date +%F`/
else
 mount /dev/sda1 /media/usbdrive
 if mount | grep "/dev/sda1 on /media/usbdrive type" > /dev/null
 then
   /usr/bin/rsync --remove-source-files /var/spool/asterisk/monout/* /media/usbdrive/sound`date +%F`/
 else
   echo "unable to mount disk"
 fi
fi
