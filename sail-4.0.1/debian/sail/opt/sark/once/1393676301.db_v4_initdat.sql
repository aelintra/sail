BEGIN TRANSACTION;

INSERT OR IGNORE INTO Route(pkey,active,auth,cluster,desc,dialplan,path1,path2,path3,path4) values ('DEFAULT','YES','NO','default','DEFAULT TRUNK','_XXXX.','None','None','None','None');
INSERT OR IGNORE INTO lineIO(pkey,active,carrier,closeroute,cluster,desc,faxdetect,lcl,moh,monitor,openroute,peername,routeclassopen,routeclassclosed,swoclip,technology) values ('_XXXX.','YES','PTT_DiD_Class','Operator','default','user1804','NO','NO','NO','NO','Operator','peer1798','100','100','NO','Class');
INSERT OR IGNORE INTO speed(pkey,cluster,devicerec,grouptype,outcome,outcomerouteclass,ringdelay) values ('RINGALL','default','default','Ring','Operator','100','180');

DELETE FROM device WHERE pkey LIKE 'y0000000000%';
DELETE FROM device WHERE pkey LIKE 'Yealink%';
DELETE FROM device WHERE pkey LIKE 'Grandstream%';
DELETE FROM device WHERE pkey LIKE 'Siemens%';
DELETE FROM device WHERE pkey LIKE 'Aastra 4%';
DELETE FROM device WHERE pkey LIKE 'Aastra 5%';
DELETE FROM device WHERE pkey LIKE 'Aastra 9%';
DELETE FROM device WHERE pkey LIKE 'Cisco 79%';
DELETE FROM device WHERE pkey LIKE 'SPA-%';
DELETE FROM device WHERE pkey LIKE 'spa%cfg';
DELETE FROM device WHERE pkey LIKE 'snom%.htm';

DELETE FROM device WHERE pkey = 'Snom 300';
DELETE FROM device WHERE pkey = 'Snom 320';
DELETE FROM device WHERE pkey = 'Snom 360';
DELETE FROM device WHERE pkey = 'Snom 370';
DELETE FROM device WHERE pkey = 'Snom 710';
DELETE FROM device WHERE pkey = 'Snom 720';
DELETE FROM device WHERE pkey = 'Snom 760';
DELETE FROM device WHERE pkey = 'Snom 820';
DELETE FROM device WHERE pkey = 'Snom 821';
DELETE FROM device WHERE pkey = 'Snom 860';

DELETE FROM device WHERE pkey = 'RINGLIST.DAT';
DELETE FROM device WHERE pkey = 'SIPDefault.cnf';
DELETE FROM device WHERE pkey = 'OS79XX.TXT';
DELETE FROM device WHERE pkey = 'XMLDefault.cnf.xml';

DELETE FROM device WHERE pkey = 'AastraVXT';
DELETE FROM device WHERE pkey = 'lang.nl';
DELETE FROM device WHERE pkey = 'snomdirectory1.xml';

COMMIT;
