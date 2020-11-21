BEGIN TRANSACTION;
INSERT OR IGNORE INTO Carrier(pkey,carriertype,desc,technology) values ('AnalogFXO','PSTN','Analogue FXO','Analogue');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,technology) values ('CLID','CLID','CLID','CLID ','NO','CLID');
INSERT OR IGNORE INTO Carrier(pkey,carriertype,desc,technology) values ('Custom','group','Custom Trunk','Custom');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,technology) values ('DAHDIGroup','DAHDIGroup','group','DAHDI Group','DAHDI');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,technology) values ('DiD','DiD','DiD','DiD ','NO','DiD');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,register,sipiaxpeer,sipiaxuser,technology) values ('GeneralIAX2','GeneralIAX2','VOIP','A general IAX2 carrier','NO','username:password@url/username','type=peer
host=
qualify=yes
canreinvite=no
username=
fromuser=
secret=','type=user
context=mainmenu
requirecalltoken=no','IAX2');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,register,sipiaxpeer,technology) values ('GeneralSIP','GeneralSIP','VOIP','A general SIP carrier','NO','username:password@url/username','type=peer
host=
qualify=yes
canreinvite=no
username=
fromuser=
secret=
insecure=port,invite','SIP');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,register,sipiaxpeer,sipiaxuser,technology) values ('IAX2','GeneralIAX2','VOIP','A general IAX2 carrier','NO','username:password@url/username','type=peer
host=
qualify=yes
canreinvite=no
username=
fromuser=
secret=','type=user
context=mainmenu','IAX2');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,sipiaxpeer,sipiaxuser,technology) values ('InterSARK','InterSARK','Sibling','Sibling Comms','NO','type=peer
host=
qualify=yes
canreinvite=no
username=
fromuser=
secret=
trunk=no','type=user
secret=
context=internal
requirecalltoken=no','IAX2');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,technology) values ('PTT_CLID','PTT_CLID','CLID','CLID ','NO','CLID');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,technology) values ('PTT_CLID_Class','PTT_CLID_Class','Class','CLID Class ','NO','Class');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,technology) values ('PTT_DiD_Class','PTT_DiD_Class','Class','DiD Class ','NO','Class');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,technology) values ('PTT_DiD_Group','PTT_DiD_Group','DiD','DiD ','NO','DiD');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,register,sipiaxpeer,technology) values ('SIP','SIP','VOIP','A general SIP carrier','NO','username:password@url/username','type=peer
host=
port=5060
qualify=yes
canreinvite=no
username=
fromuser=
secret=
insecure=port,invite','SIP');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,md5encrypt,sipiaxpeer,sipiaxuser,technology) values ('SailToSail','SailToSail','Sibling','Sibling Comms','NO','type=peer
host=
qualify=yes
canreinvite=no
username=
fromuser=
secret=
trunk=yes','type=user
secret=
context=internal
requirecalltoken=no','IAX2');
INSERT OR IGNORE INTO Carrier(pkey,carrier,carriertype,desc,sipiaxpeer,technology) values ('mISDN','mISDN','group','msdn drivers','type=peer
host=
qualify=yes
canreinvite=no
username=
fromuser=
secret=','mISDN');
INSERT OR IGNORE INTO mfgmac(pkey,name) values ('00:01:E3','Siemens');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:04:13','snom','snom-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:04:F2','Polycom','polycom-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('64:16:7F','Polycom','polycom-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:08:5D','Aastra','aastra-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:10:BC','Aastra','aastra-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:0B:82','Grandstream','grandstream-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:0E:08','Linksys','sipura-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:11:5C','Cisco','cisco-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('0C:11:67','CiscoMP','cisco-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:15:65','Yealink','yealink-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('80:5E:0C','Yealink','yealink-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('80:5E:C0','Yealink','yealink-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:23:69','Linksys','sipura-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:80:F0','Panasonic','panasonic-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('08:00:23','Panasonic','panasonic-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('BC:C3:42','Panasonic','panasonic-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('64:9E:F3','CiscoSPA','sipura-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('7C:2F:80','Gigaset','gigaset-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('14:B3:70','Gigaset','gigaset-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:21:04','Gigaset','gigaset-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('14:AE:DB','Vtech','vtech-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('00:12:2A','Vtech','vtech-check-cfg');
INSERT OR IGNORE INTO mfgmac(pkey,name,notify) values ('C4:68:D0','Vtech','vtech-check-cfg');

INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('100','sarkextension/main.php','Extensions','30','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('105','sarkphone/main.php','Phone','0','update');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('110','sarktrunk/main.php','Trunks','30','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('115','sarkdiscover/main.php','Discover','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('120','sarkdevice/main.php','Templates','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('130','sarkroute/main.php','Route(Outbound)','30','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('134','sarkddi/main.php','Route(Inbound)','30','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('140','sarkcos/main.php','Class of Service','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('150','sarkcallgroup/main.php','Ring Groups','20','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('160','sarktimer/main.php','Timers(Recurring)','20','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('170','sarkagent/main.php','Agents','20','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('180','sarkqueue/main.php','Queues','20','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('190','sarkivr/main.php','IVR','20','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('200','sarkgreeting/main.php','Greetings','20','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('210','sarkcluster/main.php','Multi-Tenant','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('215','sarkconference/main.php','Conferences','30','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('220','sarkcallback/main.php','Callback','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('224','sarkmcast/main.php','Multicast','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('230','sarkglobal/main.php','Globals','50','update');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('232','sarkreception/main.php','Home','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('235','sarkthreat/main.php','Threats','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('236','sarkthreathist/main.php','Threat History','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('240','sarkbackup/main.php','Backup/Restore','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('242','sarkcert/main.php','Certificates','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('250','sarklog/main.php','Logs','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('252','sarkpcap/main.php','SIP PCAP Logs','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('255','sarkshell/main.php','Console','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('260','sarkuser/main.php','Users','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('265','sarkldap/main.php','Directory','0','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('270','sarkedit/main.php','Asterisk Edit','50','update');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('280','sarkpci/main.php','PCI Cards','50','update');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('290','sarkapp/main.php','Custom Apps','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('300','CDR','CDRs','50','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('310','sarkpasswd/main.php','Password','0','update');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('320','sarknetwork/main.php','IP Settings','50','update');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('330','sarkedsw/main.php','IPV4 Firewall','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('331','sarkedsw6/main.php','IPV6 Firewall','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('333','sarkfqdnwlist/main.php','Dynamic FQDNs','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('335','sarkipblacklist/main.php','IP Blacklist','50','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('340','sarkfreset/main.php','Factory Reset','50','update');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('350','sarkholiday/main.php','Timers(Holidays)','30','create');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('370','sarksplash/main.php','Dashboard','0','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('390','sarkrecordings/main.php','Call Recording','30','view');
INSERT OR IGNORE INTO Panel(pkey,classname,displayname,weight,ability) values ('400','sarkreport/main.php','Reports','30','view');


INSERT OR IGNORE INTO PanelGroup(pkey,groupname) values ('100','Endpoints');
INSERT OR IGNORE INTO PanelGroup(pkey,groupname) values ('200','Routing');
INSERT OR IGNORE INTO PanelGroup(pkey,groupname) values ('400','PBX');
INSERT OR IGNORE INTO PanelGroup(pkey,groupname) values ('500','Settings');
INSERT OR IGNORE INTO PanelGroup(pkey,groupname) values ('550','Net');
INSERT OR IGNORE INTO PanelGroup(pkey,groupname) values ('600','Asterisk');
INSERT OR IGNORE INTO PanelGroup(pkey,groupname) values ('700','CDRs');

INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('100','100');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('100','105');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('100','110');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('100','115');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('100','130');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('100','134');

INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','140');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','150');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','160');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','170');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','180');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','190');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','200');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','210');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','215');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','400');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','350');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('400','370');

INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','120');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','230');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','235');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','236');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','240');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','242');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','250');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','252');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','255');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','260');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','265');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','270');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','280');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','290');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','340');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','310');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('500','390');

INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('550','320');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('550','330');
INSERT OR IGNORE INTO PanelGroupPanel(PanelGroup_pkey,Panel_pkey) values ('550','331');


COMMIT;
