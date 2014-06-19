BEGIN TRANSACTION;
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,sipiaxfriend,technology) values ('Aastra VXT','Aastra Hot Desk Template','AastraVXT','sip proxy ip: $localip
sip outbound proxy: $localip
sip registrar ip: $localip
sip screen name: $desc
sip user name: $ext
sip display name: $ext
sip auth name: $ext
sip password: $password','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,device,technology,zapdevfixed) values ('AnalogFXS','Analogue FXS','AnalogFXS','Analogue','context=internal caller_id=01 signalling=fxo_ks');
INSERT OR IGNORE INTO Device(pkey,desc,device,sipiaxfriend,technology) values ('General SIP','General SIP definition','General SIP','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,device,sipiaxfriend,technology) values ('MAILBOX','Unattached mailbox','MAILBOX','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,device,sipiaxfriend,technology) values ('General IAX','General IAX definition','General IAX','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
callerid=
pickupgroup=','IAX2');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,technology) values ('aastra.cfg','Aastra descriptor','aastra.cfg','download protocol: TFTP
sip mode: 0
sip proxy port: 5060
sip vmail: *50*
sip proxy port: 5060
sip outbound proxy port: 5060
sip registrar port: 5060
sip registration period: 0
time zone name: GB-London
tone set: UK
time server1: pool.ntp.org
auto resync mode: 1
auto resync time: 00:15
','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,sipiaxfriend,technology) values ('Snom VXT','Snom Hot Desk Template','Snom VXT','[#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,technology) values ('polycom-locals.cfg','polycom local settings','polycom-locals.cfg','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

<!-- Our local phone system common configuration in this file -->
<localcfg>
 <server voIpProt.server.1.address="$localip"/>
 <SIP>
   <outboundProxy voIpProt.SIP.outboundProxy.address="$localip"/>
 </SIP>
<TCP_IP>
   <SNTP tcpIpApp.sntp.resyncPeriod="86400" tcpIpApp.sntp.address="$localip" tcpIpApp.sntp.address.overrideDHCP="0" tcpIpApp.sntp.gmtOffset="0" tcpIpApp.sntp.gmtOffset.overrideDHCP="0" tcpIpApp.sntp.daylightSavings.enable="1" />
 </TCP_IP>
 <call>
         <call.directedCallPickupMethod="legacy" call.directedCallPickupString="*8" />
         <missedCallTracking call.missedCallTracking.1.enabled="1" 
          call.missedCallTracking.2.enabled="0" call.missedCallTracking.3.enabled="0" 
          call.missedCallTracking.4.enabled="0" call.missedCallTracking.5.enabled="0" 
          call.missedCallTracking.6.enabled="0"/>
 </call>
<!-- Registration info -->
<attendant
          attendant.uri=""
          attendant.reg="1"
          attendant.behaviors.display.spontaneousCallAppearances.normal="1"
          attendant.behaviors.display.spontaneousCallAppearances.automata="1"
          attendant.behaviors.display.remoteCallerID.normal="1"
          attendant.behaviors.display.remoteCallerID.automata="1"
#INCLUDE polycom.Fkey
$fkey
/>
</localcfg>','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,sipiaxfriend,technology) values ('Polycom VXT','Polycom VXT user','Polycom VXT','<?xml version="1.0" standalone="yes"?>
<!-- $Revision: 1.14 $  $Date: 2005/07/27 18:43:30 $ -->
<APPLICATION APP_FILE_PATH="sip.ld" CONFIG_FILES="[MACADDRESS]-polycom-locals.cfg, [MACADDRESS]-polycom-phone1.cfg" />','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Snom 870','Snom 870 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
call-limit=99
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,device,provision,sipiaxfriend,technology) values ('Aastra','aastra.Fkey','Aastra SIP phone','Aastra','sip proxy ip: $localip
sip outbound proxy: $localip
sip registrar ip: $localip
sip screen name: $desc
sip user name: $ext
sip display name: $ext
sip auth name: $ext
sip password: $password

','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=
subscribecontext=extensions','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,device,provision,sipiaxfriend,technology) values ('CiscoSPA','cisco.Fkey','Cisco/Linksys(SPA)','Cisco/linksys(SPA)','#INCLUDE cisco.Common
$fkey
<User_ID_1_>$ext</User_ID_1_>
<Password_1_>$password</Password_1_>
<Station_Display_Name ua="na">$desc($ext)</Station_Display_Name>
#INCLUDE cisco.Fkey','type=peer
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology) values ('Snom 300','snom.Fkey','5','Snom 300 series','Snom 300','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology) values ('Snom 320','snom.Fkey','12','Snom 320 series','Snom 320','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology) values ('Snom 360','snom.Fkey','12','Snom 360 series','Snom 360','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology) values ('Snom 370','snom.Fkey','12','Snom 370 series','Snom 370','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Snom 710','snom.Fkey','5','Snom 710 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
call-limit=99
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Snom 720','snom.Fkey','18','Snom 720 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
call-limit=99
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Snom 760','snom.Fkey','12','Snom 760 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
call-limit=99
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Snom 820','snom.Fkey','12','Snom 820 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
call-limit=99
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Snom 821','snom.Fkey','12','Snom 821 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
call-limit=99
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink T12','Yealink T12 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 ','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink T18','Yealink T18 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 ','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink T19','Yealink T19 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 ','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink T20','Yealink T20 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 ','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink T22','Yealink T22 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 ','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Yealink T26','yealink.Fkey','10','Yealink T26 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Yealink T28','yealink.Fkey','10','Yealink T28 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink T32','Yealink T32 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 ','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology) values ('Yealink T38','yealink.Fkey','10','Yealink T38 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink VP530','Yealink VP530 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 ','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Yealink VXT','Yealink Hot Desk Template','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,technology) values ('y000000000000.cfg','Yealink T28 descriptor','y000000000000.cfg','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000004.cfg','Yealink T26 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000005.cfg','Yealink T22 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000007.cfg','Yealink T20 descriptor','#INCLUDE yealinkCommon','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000009.cfg','Yealink T18 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000023.cfg','Yealink VP530 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000031.cfg','Yealink T19 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000032.cfg','Yealink T32 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000038.cfg','Yealink T38 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('aastra.Fkey','Aastra BLF template','softkey$seq type: $type
softkey$seq label: $label
softkey$seq value: $value
softkey$seq line: 1','BLF Template');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('yealink.Fkey','Yealink BLF Template','memorykey.$seq.line = 0 
memorykey.$seq.value = $value 
memorykey.$seq.pickup_value = *8
memorykey.$seq.type =  $type','BLF Template');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('snom.Fkey','Snom BLF template','fkey$seq$: $type $value
fkey_label$seq$: $label','BLF Template');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('yealink.Common','Yealink common Y file','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

local_time.time_zone = 0
local_time.time_zone_name = UnitedKingdom(London) 
local_time.ntp_server1 = pool.ntp.org
local_time.ntp_server2 = $localhost

features.pickup.direct_pickup_enable = 1
features.pickup.direct_pickup_code = *8

#######################################################################################
##         	              LDAP Settings                                              ##
#######################################################################################
#Configure the search criteria for name and number lookups.
ldap.name_filter = (|(name=%)(sn=%))
ldap.number_filter = (|(telephoneNumber=%)(mobile=%)(homePhone=%))

ldap.host = $localip
ldap.port = 389

ldap.base = dc=sark,dc=aelintra,dc=com
ldap.user = 
ldap.password = 

#Specify the maximum of the displayed search results. It ranges from 1 to 32000, the default value is 50.
ldap.max_hits = 

ldap.name_attr = displayName cn sn
ldap.numb_attr =  mobile telephoneNumber
ldap.display_name = %cn

#Configure the LDAP version. The valid value is 2 or 3 (default).
ldap.version = 

#Conifugre the search delay time. It ranges from 0 (default) to 2000.
ldap.search_delay = 

#Enable or disable the phone to query the contact name from the LDAP server when receiving an incoming call; 0-Disabled (default), 1-Enabled;
ldap.call_in_lookup = 1

#Enable or disable the phone to sort the search results in alphabetical order; 0-Disabled (default), 1-Enabled; 
ldap.ldap_sort =  1

#Enable or disable the phone to query the LDAP server when in the pre-dialing or the dialing state; 0-Disabled (default), 1-Enabled;
ldap.dial_lookup =  1','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('snom.Common','Snom common config','; thou shalt have no other provsioning servers before me
setting_server: http://$localip/provisioning?mac={mac}
update_policy$: 

edit_alpha_mode&: 123

ldap_server$: $localip
ldap_port!: 
ldap_base!: dc=sark,dc=aelintra,dc=com
ldap_username!: 
ldap_password!: 
ldap_max_hits!: 50
ldap_search_filter!: (|(name=%)(sn=%))
ldap_number_filter!: (|(telephoneNumber=%)(mobile=%)(homePhone=%))
ldap_name_attributes!: displayName cn sn
ldap_number_attributes!: mobile telephoneNumber
ldap_display_name!: %cn
dkey_directory!: keyevent F_DIRECTORY_SEARCH

ignore_asserted_in_gui$: on

; leave dnd codes blank
dnd_on_code!:
dnd_off_code!:

; ntp server - in case it doesnt come from dhcp
ntp_server$: pool.ntp.org

; locale settings
timezone!: GBR-0
language!: English(UK)
tone_scheme!: GBR
date_us_format&: off
time_24_format&: on

; change ringers as no one likes the normal one!
alert_internal_ring_sound!: Ringer6
alert_external_ring_sound!: Ringer2
user_ringer1!: Ringer6
user_ringer2!: Ringer6

; load firmware automatically with no user input if we send it
update_policy&: auto_update

; dont offer encrypted rtp, needs changing it Asterisk ever supports it
user_srtp1$: off
user_srtp2$: off

; turn off long contact sip headers
user_descr_contact1$: off
user_descr_contact2$: off

; "support broken registrar" not sure this is needed any more for *
user_sipusername_as_line1$: on
user_sipusername_as_line2$: on

; shows name & number on inbound call
display_method!: display_name_number

; resync settings hourly, only on v7 firmware
;settings_refresh_timer$: 3600

; settings for easier transfers
; call_join needs to be off for receptionists though!!!
transfer_on_hangup!: on
call_join_xfer!: on

; turns off stupid flash plugin in web gui
with_flash$: off

; stops LED lighting for missed calls
message_led_other!: off
;flash on call waiting
call_waiting$: visual

; Voicemail
dkey_retrieve!: speed *50*

;Dont keep asking for a password
challenge_response$: off

;turn off the logon wizard
logon_wizard$: skip welcome

;set an http UID & password
http_user!: user
http_pass!: 1111

admin_mode_password!: 2222','Descriptor');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology) values ('Polycom','polycom.Fkey','Polycom','<?xml version="1.0" standalone="yes"?>
<!-- $Revision: 1.14 $  $Date: 2005/07/27 18:43:30 $ -->
<APPLICATION APP_FILE_PATH="sip.ld" CONFIG_FILES="[MACADDRESS]-polycom-locals.cfg, [MACADDRESS]-polycom-phone1.cfg" />
','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('polycom-phone1.cfg','polycom phone generic','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- Registration info -->
<userinfo>
  <reg reg.1.displayName="$desc" reg.1.address="$ext" reg.1.label="$desc" reg.1.auth.userId="$ext" reg.1.auth.password="$password" />
</userinfo>','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('directory.xml','polycom local directory','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- $Revision: 1.73.6.2 $  $Date: 2006/07/17 21:46:42 $ -->
<directory>
  <item_list>
    <item>
      <ln>Doe</ln>
      <fn>John</fn>
      <ct>503</ct>
    </item>
  </item_list>
</directory>','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('phone.cfg','polycom phone.cfg','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('polycom.Fkey','Polycom Fkey','attendant.resourceList.$seq.address="sip:$value@$localip" attendant.resourceList.$seq.label="$label"  attendant.resourceList.$seq.type="normal"','BLF Template');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Gigaset','Gigaset SIP XML ','<?xml version="1.0" encoding="ISO-8859-1"?>
<ProviderFrame xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="actc_provider.xsd">
<Provider>

<MAC_ADDRESS value="7C:2F:80:57:EE:64" />
<PROFILE_NAME class="string" value="SARK"/>
<S_SIP_SERVER class="string" value="$localip" />
<SYMB_ITEM ID="BS_AE_Subscriber.stMtDat[0].aucTlnName" class="symb_item" value=$quotedDesc />
<S_SIP_USER_ID class="string" value="$ext" />
<S_SIP_PASSWORD class="string" value="$password" />
<S_SIP_DISPLAYNAME class="string" value="$desc" />
<S_SIP_DOMAIN class="string" value="$localip" />
<S_SIP_LOGIN_ID class="string" value="$ext" />
<B_SIP_ACCOUNT_IS_ACTIVE_1 class="boolean" value="true" />
<SYMB_ITEM ID="BS_AE_SwConfig.ucCountryCodeTone" class="symb_item" value="8" />
<S_SIP_REGISTRAR class="string" value="$localip" />
<I_CHECK_FOR_UPDATES_TIMER_INIT class="integer" value="1400" />


<I_ONESHOT_PROVISIONING_MODE_1 class="integer" value="1"/>
<B_SIP_SHC_ACCOUNT_IS_ACTIVE class="boolean" value="false"/>
<I_DTMF_TX_MODE_BITS class="integer" value="2"/>
<B_AUTO_UPDATE_PROFILE class="boolean" value="true" />

</Provider>
</ProviderFrame>','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('cisco.Common','Cisco common config','<Resync_On_Reset>Yes</Resync_On_Reset>
<Resync_Periodic></Resync_Periodic>
<Resync_At__HHmm_ ua="na">0015</Resync_At__HHmm_>
<Dial_Plan_1_>(*x.|*xx*x.|x.)</Dial_Plan_1_>
<Time_Zone>GMT</Time_Zone>
<NTP_Enable ua="na">Yes</NTP_Enable>
<Daylight_Saving_Time_Rule ua="na">start=3/­1/7/2:0:0;end=10/­1/7/2:0:0;save=1:0:0</Daylight_Saving_Time_Rule>
<Daylight_Saving_Time_Enable ua="na">Yes</Daylight_Saving_Time_Enable>
<Primary_NTP_Server ua="na">pool.ntp.org</Primary_NTP_Server>
<Secondary_NTP_Server ua="na">$localip</Secondary_NTP_Server>
<Enable_IP_Dialing_1_ ua="na">Yes</Enable_IP_Dialing_1_>
<Attendant_Console_Call_Pickup_Code ua="na">*8#</Attendant_Console_Call_Pickup_Code>
<Server_Type ua="na">Asterisk</Server_Type>
<Back_Light_Timer ua="na">Always On</Back_Light_Timer>
<Voice_Mail_Number ua="na">*50*</Voice_Mail_Number>
<Proxy_1_>$localip</Proxy_1_>
<Outbound_Proxy_1_>$localip</Outbound_Proxy_1_>','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('cisco.Fkey','Cisco SPA BLF template','<!-- Line Key $seq -->
<Extension_$seq_ ua="na">Disabled</Extension_$seq_>
<Short_Name_$seq_ ua="na">$label</Short_Name_$seq_>
<Share_Call_Appearance_$seq_ ua="na">private</Share_Call_Appearance_$seq_>','BLF Template');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Snom 715','Snom 715 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
context=internal
call-limit=99
callerid=
dtmfmode=rfc2833
canreinvite=no','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology) values ('Yealink T48','yealink.Lkey','Yealink T48 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000035.cfg','Yealink T48 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000028.cfg','Yealink T46 descriptor ','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000029.cfg','Yealink T42 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000036.cfg','Yealink T41 descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology) values ('Yealink T41','yealink.Lkey','Yealink T41 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology) values ('Yealink T42','yealink.Lkey','Yealink T42 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology) values ('Yealink T46','yealink.Lkey','Yealink T46 phone','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 

#INCLUDE yealink.Fkey','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('yealink.Lkey','Yealink Line key template','linekey.$seq.line = 0 
linekey.$seq.value = $value 
linekey.$seq.pickup_value = *8
linekey.$seq.type =  $type
linekey.$seq.label =  $label','BLF Template');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology) values ('Yealink W52P','None','Yealink W52P DECT','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $desc
account.1.display_name = $desc
account.1.auth_name = $ext
account.1.password = $password  
account.1.user_name =  $ext
account.1.sip_server_host = $localip
account.1.outbound_proxy_enable = 1
account.1.outbound_host = $localip
account.1.proxy_require = $localip

#Enable or disable the phone to subscribe the register status; 0-Disabled (default), 1-Enabled;
account.1.subscribe_register = 1

#Enable or disable the phone to subscribe the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi = 1

#Enable or disable the phone to subscribe to the voicemail through the message waiting indicator; 0-Disabled (default), 1-Enabled;
account.1.subscribe_mwi_to_vm = 1

voice_mail.number.1 = *50*

# Enable/Disable the codecs you want to use - default is law, G729, G722

account.1.codec.1.enable = 1
account.1.codec.1.payload_type = PCMU
account.1.codec.1.priority = 1
account.1.codec.1.rtpmap = 0

account.1.codec.2.enable = 1
account.1.codec.2.payload_type = PCMA
account.1.codec.2.priority = 2 
account.1.codec.2.rtpmap = 8

account.1.codec.3.enable = 0 
account.1.codec.3.payload_type = G723_53
account.1.codec.3.priority =0
account.1.codec.3.rtpmap = 4

account.1.codec.4.enable = 0
account.1.codec.4.payload_type = G723_63
account.1.codec.4.priority = 0
account.1.codec.4.rtpmap = 4

account.1.codec.5.enable = 1
account.1.codec.5.payload_type = G729
account.1.codec.5.priority = 3
account.1.codec.5.rtpmap = 18

account.1.codec.6.enable = 1
account.1.codec.6.payload_type = G722
account.1.codec.6.priority = 4
account.1.codec.6.rtpmap = 9

account.1.codec.7.enable = 0
account.1.codec.7.payload_type = iLBC
account.1.codec.7.priority =  0
account.1.codec.7.rtpmap = 102

account.1.codec.8.enable = 0
account.1.codec.8.payload_type = G726-16
account.1.codec.8.priority = 0
account.1.codec.8.rtpmap = 112

account.1.codec.9.enable = 0
account.1.codec.9.payload_type = G726-24
account.1.codec.9.priority = 0
account.1.codec.9.rtpmap = 102

account.1.codec.10.enable = 0
account.1.codec.10.payload_type = G726-32 
account.1.codec.10.priority = 0 
account.1.codec.10.rtpmap = 99

account.1.codec.11.enable = 0
account.1.codec.11.payload_type = G726-40
account.1.codec.11.priority = 0
account.1.codec.11.rtpmap = 104

account.1.codec.12.enable = 0
account.1.codec.12.payload_type = iLBC_13_3
account.1.codec.12.priority = 0 
account.1.codec.12.rtpmap = 97

account.1.codec.13.enable = 0
account.1.codec.13.payload_type = iLBC_15_2
account.1.codec.13.priority = 0 
account.1.codec.13.rtpmap = 97 
','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
call-limit=3
callerid=
pickupgroup=
dtmfmode=rfc2833
','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('y000000000025.cfg','Yealink W52P descriptor','#INCLUDE yealink.Common','Descriptor');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology) values ('Panasonic KX','panasonic.Fkey','Panasonic KX sip phone','# Panasonic SIP Phone Standard Format File #
# This is a simplified sample configuration file.
############################################################
# Configuration Setting #
############################################################
# URL of this configuration file
OPTION66_ENABLE="Y"
OPTION66_REBOOT="Y"
PROVISION_ENABLE="Y"
CFG_RESYNC_FROM_SIP="check-sync"
CFG_STANDARD_FILE_PATH="http://$localip/provisioning?mac={mac}"
############################################################
# SIP Settings #
# Suffix "_1" indicates this parameter is for "line 1". #
############################################################
# IP Address or FQDN of SIP registrar server, proxy server
SIP_RGSTR_ADDR_1="$localip"
SIP_PRXY_ADDR_1="$localip"
# IP Address or FQDN of SIP presence server
SIP_PRSNC_ADDR_1="$localip"
# Enables DNS SRV lookup
SIP_DNSSRV_ENA_1="Y"
# ID, password for SIP authentication
PHONE_NUMBER_1="$ext"
SIP_AUTHID_1="$ext"
SIP_PASS_1="$password"
NUM_PLAN_PICKUP_DIRECT="*8"
NTP_ADDR="pool.ntp.org"
LOCAL_TIME_ZONE_POSIX="GMT0BST,M3.5.0/1,M10.5.0"
HTTPD_PORTOPEN_AUTO="Y"
VM_SUBSCRIBE_ENABLE="Y"
VM_NUMBER_1="*50*"
','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology) values ('panasonic.Fkey','Panasonic Fkey','FLEX_BUTTON_FACILITY_ACT$seq="X_PANASONIC_IPTEL_$type"
FLEX_BUTTON_FACILITY_ARG$seq="$value"
FLEX_BUTTON_FACILITY_LABEL$seq="$label"','BLF Template');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology) values ('Panasonic TGP5xx','Panasonic DECT base station','# PCC Standard Format File #
# This is a simplified sample configuration file.
############################################################
# Configuration Setting #
############################################################
# URL of this configuration file
#OPTION66_ENABLE="Y"
#OPTION66_REBOOT="Y"
PROVISION_ENABLE="Y"
#CFG_RESYNC_FROM_SIP="check-sync"
CFG_STANDARD_FILE_PATH="http://$localip/provisioning?mac={mac}"
############################################################
# SIP Settings #
# Suffix "_1" indicates this parameter is for "line 1". #
############################################################
# IP Address or FQDN of SIP registrar server, proxy server
SIP_RGSTR_ADDR_1="$localip"
SIP_PRXY_ADDR_1="$localip"
# IP Address or FQDN of SIP presence server
SIP_PRSNC_ADDR_1="$localip"
# Enables DNS SRV lookup
#SIP_DNSSRV_ENA_1="Y"
# ID, password for SIP authentication
PHONE_NUMBER_1="$ext"
SIP_AUTHID_1="$ext"
SIP_PASS_1="$password"
NUM_PLAN_PICKUP_DIRECT="*8"
NTP_ADDR="pool.ntp.org"
LOCAL_TIME_ZONE_POSIX="GMT0BST,M3.5.0/1,M10.5.0"
HTTPD_PORTOPEN_AUTO="Y"
VM_SUBSCRIBE_ENABLE="Y"
VM_NUMBER_1="*50*"
#SHARED_CALL_ENABLE_1="Y"
#SHARED_USER_ID_1="$ext"
LINE_ID_1=""
DISPLAY_NAME_1="$desc"


','type=friend
username=
secret=
mailbox=
host=dynamic
qualify=3000
canreinvite=no
context=internal
callerid=
pickupgroup=','SIP');
COMMIT;
