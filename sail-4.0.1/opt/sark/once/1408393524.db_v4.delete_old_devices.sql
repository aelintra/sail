BEGIN TRANSACTION;

DELETE FROM device WHERE pkey LIKE 'Grandstream%';
DELETE FROM device WHERE pkey LIKE 'Siemens%';
DELETE FROM device WHERE pkey LIKE 'Aastra 4%';
DELETE FROM device WHERE pkey LIKE 'Aastra 5%';
DELETE FROM device WHERE pkey LIKE 'Aastra 9%';
DELETE FROM device WHERE pkey LIKE 'Cisco 79%';
DELETE FROM device WHERE pkey LIKE 'SPA-%';
DELETE FROM device WHERE pkey LIKE 'spa%cfg';
DELETE FROM device WHERE pkey LIKE 'snom%.htm';
DELETE FROM device WHERE pkey LIKE 'snom%XML';
DELETE FROM device WHERE pkey LIKE 'Snom 3%VXT';

DELETE FROM device WHERE pkey = 'RINGLIST.DAT';
DELETE FROM device WHERE pkey = 'SIPDefault.cnf';
DELETE FROM device WHERE pkey = 'OS79XX.TXT';
DELETE FROM device WHERE pkey = 'XMLDefault.cnf.xml';

DELETE FROM device WHERE pkey = 'AastraVXT';
DELETE FROM device WHERE pkey = 'lang.nl';
DELETE FROM device WHERE pkey = 'snomdirectory1.xml';

COMMIT;
