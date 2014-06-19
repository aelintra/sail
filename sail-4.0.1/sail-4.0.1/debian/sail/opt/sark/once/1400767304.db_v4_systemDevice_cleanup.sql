DELETE FROM device WHERE pkey LIKE '%VXT';
DELETE FROM device WHERE pkey LIKE 'Snom%XML';
DELETE FROM device WHERE pkey='Cisco/Linksys(SPA)';
UPDATE device set sipiaxfriend = '' WHERE EXISTS (SELECT pkey FROM device_atl WHERE device.pkey=device_atl.pkey);
UPDATE device SET sipiaxfriend = 'type=friend
defaultuser=$desc
secret=$password
mailbox=$ext
host=dynamic
qualify=yes
context=internal
call-limit=3
callerid="$desc" <$ext>
canreinvite=no
pickupgroup=1
callgroup=1
subscribecontext=extensions
disallow=all 
allow=alaw
allow=ulaw' WHERE EXISTS (SELECT pkey FROM device_atl WHERE (device.technology='SIP' OR device.technology='IAX2') AND device.pkey=device_atl.pkey AND device.pkey NOT LIKE '%Fkey');

