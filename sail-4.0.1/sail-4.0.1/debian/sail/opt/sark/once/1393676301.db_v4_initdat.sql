BEGIN TRANSACTION;
INSERT OR IGNORE INTO Route(pkey,active,auth,cluster,desc,dialplan,path1,path2,path3,path4) values ('DEFAULT','YES','NO','default','DEFAULT TRUNK','_XXXX.','None','None','None','None');
INSERT OR IGNORE INTO globals(pkey,ABSTIMEOUT,ACL,AGENTSTART,ALERT,ALLOWHASHXFER,ASTDLIM,ATTEMPTRESTART,BINDADDR,BLINDBUSY,CALLRECORD1,CAMPONQONOFF,CAMPONQOPT,CFWDEXTRNRULE,CFWDPROGRESS,CFWDANSWER,CLUSTER,CONFTYPE,COSSTART,COUNTRYCODE,DIGITS,EMERGENCY,EXTLEN,FAX,FAXDETECT,FOPPASS,HAAUTOFAILBACK,HACLUSTERIP,HAENCRYPT,HAMODE,HAPRINODE,HASYNCH,INTRINGDELAY,LOGOPTS,LTERM,MEETMEDIAL,MISDNRUN,MONITOROUT,MONITORSTAGE,MYCOMMIT,NUMGROUPS,ONBOARDMENU,OPRT,PWDLEN,PCICARDS,PLAYBEEP,PLAYBUSY,PLAYCONGESTED,PROXY,RECFINALDEST,RECQDITHER,RECQSEARCHLIM,RESTART,RUNFOP,SIPIAXSTART,SIPMULTICAST,SNO,SPYPASS,SUPEMAIL,SYSOP,SYSPASS,TFTP,UNDO,UNDONUM,VDELAY,VLIBS,VMAILAGE,VOICEINSTR,VOIPMAX,ZTP) values ('global','14400','NO','1001','email','enabled',',','YES','OFF','Operator','None','OFF','r,,,30','enabled','enabled','disabled','OFF','simple','ON','uk','None','911 999 112','3','401','4','1234','on','','sha1','OFF','one8','LOOSE','20','native','YES','_30[0-7]','disabled','/var/spool/asterisk/monout','/var/spool/asterisk/monstage','NO','2','disabled','YES','8','none','YES','YES','YES','YES','home/sark/monitor_by_day/`date +%d%m%y`','2','200','enabled','disabled','401','enabled','993428','4444','admin@yourco.com','RINGALL','1111','enabled','YES','1','2','/var/log /var/spool','60','YES','10','disabled');
INSERT OR IGNORE INTO lineIO(pkey,active,carrier,closeroute,cluster,desc,faxdetect,lcl,moh,monitor,openroute,peername,routeclassopen,routeclassclosed,swoclip,technology) values ('_XXXX.','YES','PTT_DiD_Class','Operator','default','user1804','NO','NO','NO','NO','Operator','peer1798','100','100','NO','Class');
INSERT OR IGNORE INTO page(pkey) values ('pageall');
INSERT OR IGNORE INTO speed(pkey,cluster,devicerec,grouptype,outcome,outcomerouteclass,ringdelay) values ('RINGALL','default','default','Ring','Operator','100','180');
UPDATE cluster SET chanmax=30 WHERE chanmax IS NULL;
UPDATE cluster SET abstimeout=14400 WHERE abstimeout IS NULL;
UPDATE globals SET COUNTRYCODE='uk' WHERE COUNTRYCODE IS NULL;
UPDATE globals SET OPERATOR=0 WHERE OPERATOR IS NULL OR OPERATOR=0;
UPDATE globals SET PWDLEN=8 WHERE PWDLEN IS NULL;
UPDATE globals SET RECLIMIT=1000 WHERE RECLIMIT IS NULL;

UPDATE User SET password=NULL;

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
