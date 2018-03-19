date >>/var/log/recdump.log
#set this rsync to point to your offload or nearline media
#rsync  --remove-source-files -a /var/spool/asterisk/monout/* /media/sound/`date +%d%m%y`/ >>/var/log/recdump.log 2>&1
