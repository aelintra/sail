BEGIN TRANSACTION;
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Aastra','aastra.Fkey','Aastra SIP phone','Aastra','sip proxy ip: $localip
sip outbound proxy: $localip
sip registrar ip: $localip
sip screen name: $desc
sip user name: $ext
sip display name: $ext
sip auth name: $ext
sip password: $password

','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Aastra VXT','Aastra Hot Desk Template','AastraVXT','["$MAC.cfg"
download protocol: TFTP
sip outbound proxy: $localip
sip registrar ip: $localip
sip screen name: $desc
sip user name: $ext
sip display name: $ext
sip vmail: *50*
sip auth name: $ext
sip password: $password
sip mode: 0
sip proxy ip: $localip
sip proxy port: 5060
missed calls indicator disabled: 1
directory 1: seldir
#softkey1 type: blf
#softkey1 label: name
#softkey1 value: 5000
#softkey1 line: 1
]','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,technology,zapdevfixed,z_created,z_updated,z_updater) values ('AnalogFXS','Analogue FXS','AnalogFXS','Analogue','context=internal caller_id=01 signalling=fxo_ks','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('CiscoSPA','cisco.Fkey','Cisco/Linksys(SPA)','Cisco/linksys(SPA)','#INCLUDE cisco.Common
$fkey
<User_ID_1_>$ext</User_ID_1_>
<Password_1_>$password</Password_1_>
<Station_Display_Name ua="na">$desc($ext)</Station_Display_Name>
#INCLUDE cisco.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('General IAX','General IAX definition','General IAX','type=friend
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
allow=ulaw','IAX2','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,noproxy,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('General SIP','General SIP definition','General SIP','1','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Gigaset','Gigaset SIP XML ','<?xml version="1.0" encoding="ISO-8859-1"?>
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('MAILBOX','Unattached mailbox','MAILBOX','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Panasonic KX','panasonic.Fkey','Panasonic KX sip phone','# Panasonic SIP Phone Standard Format File #
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Panasonic KX-HDV','panasonicHDV.Fkey','Panasonic KX-HDV sip phone','# Panasonic SIP Phone Standard Format File #
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
# 
# Timezone needs to be set for your zone if not UK
#
LOCAL_TIME_ZONE_POSIX="GMT0BST,M3.5.0/1,M10.5.0"
HTTPD_PORTOPEN_AUTO="Y"
VM_SUBSCRIBE_ENABLE="Y"
VM_NUMBER_1="*50*"
HTTPD_PORTOPEN_AUTO="Y"
## LDAP Settings
LDAP_ENABLE="Y"
LDAP_DNSSRV_ENABLE="N"
LDAP_SERVER="ldap://$localip"
LDAP_SERVER_PORT="389"
LDAP_MAXRECORD="20"
LDAP_NUMB_SEARCH_TIMER="30"
LDAP_NAME_SEARCH_TIMER="5"
# 
# UID and PWD need to be set for your installation!
LDAP_USERID=""
LDAP_PASSWORD=""
#
LDAP_NAME_FILTER="(|(cn=%)(sn=%))"
LDAP_NUMB_FILTER="(|(telephoneNumber=%)(mobile=%)(homePhone=%))"
LDAP_NAME_ATTRIBUTE="cn,sn"
LDAP_NUMB_ATTRIBUTE="telephoneNumber,mobile,homePhone"
LDAP_BASEDN="$ldapbase"
LDAP_SSL_VERIFY="0"
LDAP_ROOT_CERT_PATH=""
LDAP_CLIENT_CERT_PATH=""
LDAP_PKEY_PATH=""
','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Panasonic TGP5xx','Panasonic DECT base station','# PCC Standard Format File #
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Polycom','polycom.Fkey','Polycom','<?xml version="1.0" standalone="yes"?>
<!-- $Revision: 1.14 $  $Date: 2005/07/27 18:43:30 $ -->
<APPLICATION APP_FILE_PATH="sip.ld" CONFIG_FILES="[MACADDRESS]-polycom-locals.cfg, [MACADDRESS]-polycom-phone1.cfg" />
','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 300','snom.Fkey','5','Snom 300 series','Snom 300','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 320','snom.Fkey','12','Snom 320 series','Snom 320','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 360','snom.Fkey','12','Snom 360 series','Snom 360','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 370','snom.Fkey','12','Snom 370 series','Snom 370','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 710','snom.Fkey','5','Snom 710 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 715','snom.Fkey','Snom 715 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 15:59:38','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 720','snom.Fkey','18','Snom 720 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 760','snom.Fkey','12','Snom 760 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 820','snom.Fkey','12','Snom 820 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 821','snom.Fkey','12','Snom 821 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom 870','snom.Fkey','12','Snom 870 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D305','snom.Fkey','5','Snom D305 series','Snom D305','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D315','snom.Fkey','5','Snom D315 series','Snom D315','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D345','snom.Fkey','5','Snom D345 series','Snom D345','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D375','snom.Fkey','Snom D375 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:24','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D710','snom.Fkey','5','Snom D710 series','Snom D710','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D712','snom.Fkey','Snom D712 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:24','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D715','snom.Fkey','5','Snom D715 series','Snom D715','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D725','snom.Fkey','5','Snom D725 series','Snom D725','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D745','snom.Fkey','5','Snom D745 series','Snom D745','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom D765','snom.Fkey','5','Snom D765 series','Snom D765','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,device,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Snom VXT','snom.Fkey','Snom Hot Desk Template','Snom VXT','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('VCS754A.cfg','Vtech cfg','#INCLUDE Vtech.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('VSP600A.cfg','Vtech cfg','#INCLUDE Vtech.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('VSP715A.cfg','Vtech cfg','#INCLUDE Vtech.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('VSP725A.cfg','Vtech cfg','#INCLUDE Vtech.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('VSP726A.cfg','Vtech cfg','#INCLUDE Vtech.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('VSP735A.cfg','Vtech cfg','#INCLUDE Vtech.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Vtech','Vtech SIP Phone','sip_account.1.sip_account_enable = 1
sip_account.1.label = $ext
sip_account.1.display_name = $desc
sip_account.1.user_id = $ext
sip_account.1.authentication_name = $ext 
sip_account.1.authentication_access_password = $password 
sip_account.1.primary_sip_server_port = 5060
sip_account.1.primary_sip_server_address = $localip
sip_account.1.primary_outbound_proxy_server_port = 5060
sip_account.1.primary_outbound_proxy_server_address = $localip
sip_account.1.primary_registration_server_port = 5060
sip_account.1.primary_registration_server_address = $localip
sip_account.1.access_code_retrieve_voicemail = *50*
sip_account.1.blf_remote_pickup_code = *8','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('Vtech.Common','Vtech Common cfg','# Model Number = VSP735A
# SW Version = 1.1.4.0-0
time_date.date_format = DD/MM/YY
time_date.24hr_clock = 1
time_date.ntp_dhcp_option = 0
time_date.ntp_server = 1
time_date.ntp_server_addr = europe.pool.ntp.org
time_date.ntp_server_update_interval = 1000
time_date.timezone_dhcp_option = 0
time_date.selected_timezone = Europe/London
time_date.daylight_saving_auto_adjust = 1
time_date.daylight_saving_user_defined = 0
time_date.daylight_saving_start_month = March
time_date.daylight_saving_start_week = 5
time_date.daylight_saving_start_day = Sunday
time_date.daylight_saving_start_hour = 2
time_date.daylight_saving_end_month = October
time_date.daylight_saving_end_week = 5
time_date.daylight_saving_end_day = Sunday
time_date.daylight_saving_end_hour = 2
time_date.daylight_saving_amount = 60
profile.admin.access_password = sarkadmin 
profile.user.access_password = 1111
remoteDir.ldap_directory_name = Sark
remoteDir.ldap_number_filter = (|(telephoneNumber=%)(mobile=%)(homePhone=%))
remoteDir.ldap_firstname_filter = givenName=%
remoteDir.ldap_lastname_filter = sn=%
remoteDir.ldap_server_address = $localip
remoteDir.ldap_port = 389
remoteDir.ldap_authentication_type = simple
remoteDir.ldap_base = $ldapbase
remoteDir.ldap_user_name = 
remoteDir.ldap_access_password = 
remoteDir.ldap_max_hits = 200
remoteDir.ldap_work_number_attributes = telephoneNumber
remoteDir.ldap_mobile_number_attributes = mobile
remoteDir.ldap_other_number_attributes = homePhone
remoteDir.ldap_protocol_version = version_3
remoteDir.ldap_search_delay = 0
remoteDir.ldap_incall_lookup_enable = 1
remoteDir.ldap_outcall_lookup_enable = 1
remoteDir.ldap_enable = 1
remoteDir.ldap_firstname_attribute = givenName
remoteDir.ldap_lastname_attribute = sn
remoteDir.ldap_check_certificate = 0
sip_account.use_first_trusted_certificate_for_all = 0
network.ip.dhcp_enable = 1
network.ip.dns1 = 
network.ip.dns2 = 
network.ip.static_ip_addr = 
network.ip.subnet_mask = 
network.ip.gateway_addr = 
network.ip.dns_cache_clear_timeout = 60
network.nat.masquerading_enable = 0
network.nat.public_ip_addr = 
network.nat.public_sip_port = 5060
network.nat.public_rtp_port_start = 18000
network.nat.public_rtp_port_end = 19000
network.vlan.wan.enable = 0
network.vlan.wan.id = 0
network.vlan.wan.priority = 0
network.vlan.pc.enable = 0
network.vlan.pc.id = 0
network.vlan.pc.priority = 0
network.rtp.port_start = 18000
network.rtp.port_end = 19000
network.lldp_med.enable = 1
network.lldp_med.interval = 30
network.eapol.enable = 0
network.eapol.identity = 
network.eapol.access_password = 
network.vendor_class_id = 
network.user_class = 
provisioning.bootup_check_enable = 1
provisioning.crypto_enable = 0
provisioning.crypto_passphrase = 
provisioning.dhcp_option_enable = 1
provisioning.dhcp_option_priority_1 = 66
provisioning.dhcp_option_priority_2 = 159
provisioning.dhcp_option_priority_3 = 160
provisioning.firmware_url = 
provisioning.handset_firmware_url = 
provisioning.fw_server_username = 
provisioning.fw_server_access_password = 
provisioning.resync_mode = config_and_firmware
provisioning.resync_time = 0
provisioning.server_address = http://et.vtechphones.com/redirectserver
provisioning.server_username = 
provisioning.server_access_password = 
provisioning.check_trusted_certificate = 0
provisioning.click_to_dial = 0
provisioning.remote_check_sync_enable = 1
provisioning.pnp_enable = 1
provisioning.pnp_response_timeout = 10
provisioning.schedule_mode = interval
provisioning.weekdays = 
provisioning.weekdays_start_hr = 0
provisioning.weekdays_end_hr = 0
user_pref.language = en-GB
user_pref.text_input_option = uc_western,lc_western,number
user_pref.web_language = en-GB
user_pref.lcd_contrast = 4
user_pref.backlight = high
user_pref.idle_backlight = off
user_pref.backlight_timeout = 10
user_pref.absent_timeout = 30
user_pref.account.1.ringer = 1
user_pref.account.2.ringer = 1
user_pref.account.3.ringer = 1
user_pref.account.4.ringer = 1
user_pref.account.5.ringer = 1
user_pref.ringer_volume = 5
user_pref.speaker_volume = 5
user_pref.headset_volume = 5
user_pref.handset_volume = 5
user_pref.audio_mode = speaker
user_pref.key_beep_enable = 1
user_pref.hold_reminder.enable = 1
user_pref.hold_reminder.interval = 30
user_pref.call_waiting.mode = enable
user_pref.call_waiting.tone_enable = 1
user_pref.call_waiting.tone_interval = 30
user_pref.notify.led.missed_call.enable = 0
log.syslog_enable = 0
log.syslog_level = 2
log.syslog_server_address = 
log.syslog_server_port = 514
page_zone.call_priority_threshold = 2
page_zone.1.name = 
page_zone.1.multicast_address = 
page_zone.1.multicast_port = 
page_zone.1.accept_incoming_page = 1
page_zone.1.priority = 5
page_zone.2.name = 
page_zone.2.multicast_address = 
page_zone.2.multicast_port = 
page_zone.2.accept_incoming_page = 1
page_zone.2.priority = 5
page_zone.3.name = 
page_zone.3.multicast_address = 
page_zone.3.multicast_port = 
page_zone.3.accept_incoming_page = 1
page_zone.3.priority = 5
page_zone.4.name = 
page_zone.4.multicast_address = 
page_zone.4.multicast_port = 
page_zone.4.accept_incoming_page = 1
page_zone.4.priority = 5
page_zone.5.name = 
page_zone.5.multicast_address = 
page_zone.5.multicast_port = 
page_zone.5.accept_incoming_page = 1
page_zone.5.priority = 5
page_zone.6.name = 
page_zone.6.multicast_address = 
page_zone.6.multicast_port = 
page_zone.6.accept_incoming_page = 1
page_zone.6.priority = 5
page_zone.7.name = 
page_zone.7.multicast_address = 
page_zone.7.multicast_port = 
page_zone.7.accept_incoming_page = 1
page_zone.7.priority = 5
page_zone.8.name = 
page_zone.8.multicast_address = 
page_zone.8.multicast_port = 
page_zone.8.accept_incoming_page = 1
page_zone.8.priority = 5
page_zone.9.name = 
page_zone.9.multicast_address = 
page_zone.9.multicast_port = 
page_zone.9.accept_incoming_page = 1
page_zone.9.priority = 5
page_zone.10.name = 
page_zone.10.multicast_address = 
page_zone.10.multicast_port = 
page_zone.10.accept_incoming_page = 1
page_zone.10.priority = 5
tone.call_waiting_tone.num_of_elements = 1
tone.call_waiting_tone.element.1 = 1 440 -150 0 0 0 0 0 0 500 0 1
tone.call_waiting_tone.element.2 = 
tone.call_waiting_tone.element.3 = 
tone.call_waiting_tone.element.4 = 
tone.call_waiting_tone.element.5 = 
tone.hold_reminder.num_of_elements = 1
tone.hold_reminder.element.1 = 1 770 -120 0 0 0 0 0 0 300 0 1
tone.hold_reminder.element.2 = 
tone.hold_reminder.element.3 = 
tone.hold_reminder.element.4 = 
tone.hold_reminder.element.5 = 
tone.inside_dial_tone.num_of_elements = 1
tone.inside_dial_tone.element.1 = 2 440 -180 350 -180 0 0 0 0 4294967295 0 65535
tone.inside_dial_tone.element.2 = 
tone.inside_dial_tone.element.3 = 
tone.inside_dial_tone.element.4 = 
tone.inside_dial_tone.element.5 = 
tone.stutter_dial_tone.num_of_elements = 2
tone.stutter_dial_tone.element.1 = 2 440 -180 350 -180 0 0 0 0 100 100 10
tone.stutter_dial_tone.element.2 = 2 440 -180 350 -180 0 0 0 0 4294967295 0 65535
tone.stutter_dial_tone.element.3 = 
tone.stutter_dial_tone.element.4 = 
tone.stutter_dial_tone.element.5 = 
tone.busy_tone.num_of_elements = 1
tone.busy_tone.element.1 = 1 400 -180 0 0 0 0 0 0 375 375 65535
tone.busy_tone.element.2 = 
tone.busy_tone.element.3 = 
tone.busy_tone.element.4 = 
tone.busy_tone.element.5 = 
tone.ring_back_tone.num_of_elements = 1
tone.ring_back_tone.element.1 = 2 440 -180 480 -180 0 0 0 0 2000 4000 65535
tone.ring_back_tone.element.2 = 
tone.ring_back_tone.element.3 = 
tone.ring_back_tone.element.4 = 
tone.ring_back_tone.element.5 = 
web.http_port = 80
web.https_port = 443
web.https_enable = 0
call_settings.account.1.block_anonymous_enable = 0
call_settings.account.1.outgoing_anonymous_enable = 0
call_settings.account.1.dnd_enable = 0
call_settings.account.1.dnd_incoming_calls = reject
call_settings.account.1.call_fwd_always_enable = 0
call_settings.account.1.call_fwd_always_target = 
call_settings.account.1.call_fwd_busy_enable = 0
call_settings.account.1.call_fwd_busy_target = 
call_settings.account.1.cfna_enable = 0
call_settings.account.1.cfna_target = 
call_settings.account.1.cfna_delay = 6
call_settings.account.2.block_anonymous_enable = 0
call_settings.account.2.outgoing_anonymous_enable = 0
call_settings.account.2.dnd_enable = 0
call_settings.account.2.dnd_incoming_calls = reject
call_settings.account.2.call_fwd_always_enable = 0
call_settings.account.2.call_fwd_always_target = 
call_settings.account.2.call_fwd_busy_enable = 0
call_settings.account.2.call_fwd_busy_target = 
call_settings.account.2.cfna_enable = 0
call_settings.account.2.cfna_target = 
call_settings.account.2.cfna_delay = 6
call_settings.account.3.block_anonymous_enable = 0
call_settings.account.3.outgoing_anonymous_enable = 0
call_settings.account.3.dnd_enable = 0
call_settings.account.3.dnd_incoming_calls = reject
call_settings.account.3.call_fwd_always_enable = 0
call_settings.account.3.call_fwd_always_target = 
call_settings.account.3.call_fwd_busy_enable = 0
call_settings.account.3.call_fwd_busy_target = 
call_settings.account.3.cfna_enable = 0
call_settings.account.3.cfna_target = 
call_settings.account.3.cfna_delay = 6
call_settings.account.4.block_anonymous_enable = 0
call_settings.account.4.outgoing_anonymous_enable = 0
call_settings.account.4.dnd_enable = 0
call_settings.account.4.dnd_incoming_calls = reject
call_settings.account.4.call_fwd_always_enable = 0
call_settings.account.4.call_fwd_always_target = 
call_settings.account.4.call_fwd_busy_enable = 0
call_settings.account.4.call_fwd_busy_target = 
call_settings.account.4.cfna_enable = 0
call_settings.account.4.cfna_target = 
call_settings.account.4.cfna_delay = 6
call_settings.account.5.block_anonymous_enable = 0
call_settings.account.5.outgoing_anonymous_enable = 0
call_settings.account.5.dnd_enable = 0
call_settings.account.5.dnd_incoming_calls = reject
call_settings.account.5.call_fwd_always_enable = 0
call_settings.account.5.call_fwd_always_target = 
call_settings.account.5.call_fwd_busy_enable = 0
call_settings.account.5.call_fwd_busy_target = 
call_settings.account.5.cfna_enable = 0
call_settings.account.5.cfna_target = 
call_settings.account.5.cfna_delay = 6
call_settings.missed_call_alert_enable = 1
call_settings.hotline_enable = 0
call_settings.hotline_account = 0
call_settings.hotline_number = 
call_settings.hotline_delay = 0
pfk.1.feature = line
pfk.1.account = 1
pfk.1.quick_dial = 
pfk.1.incall_dtmf = 
pfk.1.page_destination = 
pfk.1.park_destination = 
pfk.1.park_retrieval_source = 
pfk.1.prefix = 
pfk.1.multicast_zone = 
pfk.2.feature = line
pfk.2.account = 1
pfk.2.quick_dial = 
pfk.2.incall_dtmf = 
pfk.2.page_destination = 
pfk.2.park_destination = 
pfk.2.park_retrieval_source = 
pfk.2.prefix = 
pfk.2.multicast_zone = 
pfk.3.feature = quick dial
pfk.3.account = 1
pfk.3.quick_dial = 
pfk.3.incall_dtmf = 
pfk.3.page_destination = 
pfk.3.park_destination = 
pfk.3.park_retrieval_source = 
pfk.3.prefix = 
pfk.3.multicast_zone = 
pfk.4.feature = quick dial
pfk.4.account = 1
pfk.4.quick_dial = 
pfk.4.incall_dtmf = 
pfk.4.page_destination = 
pfk.4.park_destination = 
pfk.4.park_retrieval_source = 
pfk.4.prefix = 
pfk.4.multicast_zone = 
pfk.5.feature = quick dial
pfk.5.account = 1
pfk.5.quick_dial = 
pfk.5.incall_dtmf = 
pfk.5.page_destination = 
pfk.5.park_destination = 
pfk.5.park_retrieval_source = 
pfk.5.prefix = 
pfk.5.multicast_zone = 
pfk.6.feature = quick dial
pfk.6.account = 1
pfk.6.quick_dial = 
pfk.6.blf = 
pfk.6.incall_dtmf = 
pfk.6.page_destination = 
pfk.6.park_destination = 
pfk.6.park_retrieval_source = 
pfk.6.prefix = 
pfk.6.multicast_zone = 
pfk.7.feature = quick dial
pfk.7.account = 1
pfk.7.quick_dial = 
pfk.7.blf = 
pfk.7.incall_dtmf = 
pfk.7.page_destination = 
pfk.7.park_destination = 
pfk.7.park_retrieval_source = 
pfk.7.prefix = 
pfk.7.multicast_zone = 
pfk.8.feature = quick dial
pfk.8.account = 1
pfk.8.quick_dial = 
pfk.8.blf = 
pfk.8.incall_dtmf = 
pfk.8.page_destination = 
pfk.8.park_destination = 
pfk.8.park_retrieval_source = 
pfk.8.prefix = 
pfk.8.multicast_zone = 
pfk.9.feature = quick dial
pfk.9.account = 1
pfk.9.quick_dial = 
pfk.9.blf = 
pfk.9.incall_dtmf = 
pfk.9.page_destination = 
pfk.9.park_destination = 
pfk.9.park_retrieval_source = 
pfk.9.prefix = 
pfk.9.multicast_zone = 
pfk.10.feature = quick dial
pfk.10.account = 1
pfk.10.quick_dial = 
pfk.10.blf = 
pfk.10.incall_dtmf = 
pfk.10.page_destination = 
pfk.10.park_destination = 
pfk.10.park_retrieval_source = 
pfk.10.prefix = 
pfk.10.multicast_zone = 
pfk.11.feature = quick dial
pfk.11.account = 1
pfk.11.quick_dial = 
pfk.11.blf = 
pfk.11.incall_dtmf = 
pfk.11.page_destination = 
pfk.11.park_destination = 
pfk.11.park_retrieval_source = 
pfk.11.prefix = 
pfk.11.multicast_zone = 
pfk.12.feature = quick dial
pfk.12.account = 1
pfk.12.quick_dial = 
pfk.12.blf = 
pfk.12.incall_dtmf = 
pfk.12.page_destination = 
pfk.12.park_destination = 
pfk.12.park_retrieval_source = 
pfk.12.prefix = 
pfk.12.multicast_zone = 
pfk.13.feature = quick dial
pfk.13.account = 1
pfk.13.quick_dial = 
pfk.13.blf = 
pfk.13.incall_dtmf = 
pfk.13.page_destination = 
pfk.13.park_destination = 
pfk.13.park_retrieval_source = 
pfk.13.prefix = 
pfk.13.multicast_zone = 
pfk.14.feature = quick dial
pfk.14.account = 1
pfk.14.quick_dial = 
pfk.14.blf = 
pfk.14.incall_dtmf = 
pfk.14.page_destination = 
pfk.14.park_destination = 
pfk.14.park_retrieval_source = 
pfk.14.prefix = 
pfk.14.multicast_zone = 
pfk.15.feature = quick dial
pfk.15.account = 1
pfk.15.quick_dial = 
pfk.15.incall_dtmf = 
pfk.15.page_destination = 
pfk.15.park_destination = 
pfk.15.park_retrieval_source = 
pfk.15.prefix = 
pfk.15.multicast_zone = 
pfk.16.feature = quick dial
pfk.16.account = 1
pfk.16.quick_dial = 
pfk.16.incall_dtmf = 
pfk.16.page_destination = 
pfk.16.park_destination = 
pfk.16.park_retrieval_source = 
pfk.16.prefix = 
pfk.16.multicast_zone = 
pfk.17.feature = quick dial
pfk.17.account = 1
pfk.17.quick_dial = 
pfk.17.incall_dtmf = 
pfk.17.page_destination = 
pfk.17.park_destination = 
pfk.17.park_retrieval_source = 
pfk.17.prefix = 
pfk.17.multicast_zone = 
pfk.18.feature = quick dial
pfk.18.account = 1
pfk.18.quick_dial = 
pfk.18.incall_dtmf = 
pfk.18.page_destination = 
pfk.18.park_destination = 
pfk.18.park_retrieval_source = 
pfk.18.prefix = 
pfk.18.multicast_zone = 
pfk.19.feature = quick dial
pfk.19.account = 1
pfk.19.quick_dial = 
pfk.19.incall_dtmf = 
pfk.19.page_destination = 
pfk.19.park_destination = 
pfk.19.park_retrieval_source = 
pfk.19.prefix = 
pfk.19.multicast_zone = 
pfk.20.feature = quick dial
pfk.20.account = 1
pfk.20.quick_dial = 
pfk.20.incall_dtmf = 
pfk.20.page_destination = 
pfk.20.park_destination = 
pfk.20.park_retrieval_source = 
pfk.20.prefix = 
pfk.20.multicast_zone = 
pfk.21.feature = quick dial
pfk.21.account = 1
pfk.21.quick_dial = 
pfk.21.incall_dtmf = 
pfk.21.page_destination = 
pfk.21.park_destination = 
pfk.21.park_retrieval_source = 
pfk.21.prefix = 
pfk.21.multicast_zone = 
pfk.22.feature = quick dial
pfk.22.account = 1
pfk.22.quick_dial = 
pfk.22.incall_dtmf = 
pfk.22.page_destination = 
pfk.22.park_destination = 
pfk.22.park_retrieval_source = 
pfk.22.prefix = 
pfk.22.multicast_zone = 
pfk.23.feature = quick dial
pfk.23.account = 1
pfk.23.quick_dial = 
pfk.23.incall_dtmf = 
pfk.23.page_destination = 
pfk.23.park_destination = 
pfk.23.park_retrieval_source = 
pfk.23.prefix = 
pfk.23.multicast_zone = 
pfk.24.feature = quick dial
pfk.24.account = 1
pfk.24.quick_dial = 
pfk.24.incall_dtmf = 
pfk.24.page_destination = 
pfk.24.park_destination = 
pfk.24.park_retrieval_source = 
pfk.24.prefix = 
pfk.24.multicast_zone = 
pfk.25.feature = quick dial
pfk.25.account = 1
pfk.25.quick_dial = 
pfk.25.incall_dtmf = 
pfk.25.page_destination = 
pfk.25.park_destination = 
pfk.25.park_retrieval_source = 
pfk.25.prefix = 
pfk.25.multicast_zone = 
pfk.26.feature = quick dial
pfk.26.account = 1
pfk.26.quick_dial = 
pfk.26.incall_dtmf = 
pfk.26.page_destination = 
pfk.26.park_destination = 
pfk.26.park_retrieval_source = 
pfk.26.prefix = 
pfk.26.multicast_zone = 
pfk.27.feature = quick dial
pfk.27.account = 1
pfk.27.quick_dial = 
pfk.27.incall_dtmf = 
pfk.27.page_destination = 
pfk.27.park_destination = 
pfk.27.park_retrieval_source = 
pfk.27.prefix = 
pfk.27.multicast_zone = 
pfk.28.feature = quick dial
pfk.28.account = 1
pfk.28.quick_dial = 
pfk.28.incall_dtmf = 
pfk.28.page_destination = 
pfk.28.park_destination = 
pfk.28.park_retrieval_source = 
pfk.28.prefix = 
pfk.28.multicast_zone = 
pfk.29.feature = quick dial
pfk.29.account = 1
pfk.29.quick_dial = 
pfk.29.incall_dtmf = 
pfk.29.page_destination = 
pfk.29.park_destination = 
pfk.29.park_retrieval_source = 
pfk.29.prefix = 
pfk.29.multicast_zone = 
pfk.30.feature = quick dial
pfk.30.account = 1
pfk.30.quick_dial = 
pfk.30.incall_dtmf = 
pfk.30.page_destination = 
pfk.30.park_destination = 
pfk.30.park_retrieval_source = 
pfk.30.prefix = 
pfk.30.multicast_zone = 
pfk.31.feature = quick dial
pfk.31.account = 1
pfk.31.quick_dial = 
pfk.31.incall_dtmf = 
pfk.31.page_destination = 
pfk.31.park_destination = 
pfk.31.park_retrieval_source = 
pfk.31.prefix = 
pfk.31.multicast_zone = 
pfk.32.feature = quick dial
pfk.32.account = 1
pfk.32.quick_dial = 
pfk.32.incall_dtmf = 
pfk.32.page_destination = 
pfk.32.park_destination = 
pfk.32.park_retrieval_source = 
pfk.32.prefix = 
pfk.32.multicast_zone = 
softkey.idle = 
softkey.call_active = 
softkey.call_held = 
softkey.live_dial = 
speed_dial.0.name = 
speed_dial.0.number = 
speed_dial.0.account = 0
speed_dial.1.name = 
speed_dial.1.number = 
speed_dial.1.account = 0
speed_dial.2.name = 
speed_dial.2.number = 
speed_dial.2.account = 0
speed_dial.3.name = 
speed_dial.3.number = 
speed_dial.3.account = 0
speed_dial.4.name = 
speed_dial.4.number = 
speed_dial.4.account = 0
speed_dial.5.name = 
speed_dial.5.number = 
speed_dial.5.account = 0
speed_dial.6.name = 
speed_dial.6.number = 
speed_dial.6.account = 0
speed_dial.7.name = 
speed_dial.7.number = 
speed_dial.7.account = 0
speed_dial.8.name = 
speed_dial.8.number = 
speed_dial.8.account = 0
speed_dial.9.name = 
speed_dial.9.number = 
speed_dial.9.account = 0
ringersetting.1.ringer_text = 
ringersetting.1.ringer_type = 1
ringersetting.2.ringer_text = 
ringersetting.2.ringer_type = 1
ringersetting.3.ringer_text = 
ringersetting.3.ringer_type = 1
ringersetting.4.ringer_text = 
ringersetting.4.ringer_type = 1
ringersetting.5.ringer_text = 
ringersetting.5.ringer_type = 1
ringersetting.6.ringer_text = 
ringersetting.6.ringer_type = 1
ringersetting.7.ringer_text = 
ringersetting.7.ringer_type = 1
ringersetting.8.ringer_text = 
ringersetting.8.ringer_type = 1
hs_settings.handset_eu_pin_code = 0000
hs_settings.1.handset_name = HANDSET
','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink CP860','Yealink CP860 Conf phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink CP920','Yealink CP920 Conf phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink CP960','Yealink CP960 Conf phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T12','Yealink T12 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T18','Yealink T18 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T19','Yealink T19 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T20','Yealink T20 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T21','Yealink T21 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T22','Yealink T22 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T23','Yealink T22 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T26','yealink.Fkey','10','Yealink T26 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T27','yealink.Lkey','Yealink T27 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T28','yealink.Fkey','10','Yealink T28 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T29','yealink.Lkey','Yealink T29 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T32','Yealink T32 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,blfkeys,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T38','yealink.Fkey','10','Yealink T38 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T40','yealink.Lkey','Yealink T40 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T41','yealink.Lkey','Yealink T41 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T42','yealink.Lkey','Yealink T42 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T46','yealink.Lkey','Yealink T46 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T48','yealink.Lkey','Yealink T48 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T52','yealink.Lkey','Yealink T52 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T54','yealink.Lkey','Yealink T54 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T56','yealink.Fkey','Yealink T56 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink T58','yealink.Fkey','Yealink T58 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink VP530','Yealink VP530 phone','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink VXT','Yealink office phone range','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink W52P','None','Yealink W52P DECT','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,noproxy,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('Yealink W60P','Yealink W60P DECT','1','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

account.1.enable = 1
account.1.label = $ext
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
allow=ulaw','SIP','2018-02-24 12:49:23','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('aastra.Fkey','Aastra BLF template','softkey$seq type: $type
softkey$seq label: $label
softkey$seq value: $value
softkey$seq line: 1','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,technology,z_created,z_updated,z_updater) values ('aastra.cfg','Aastra descriptor','aastra.cfg','download protocol: TFTP
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
','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('cisco.Common','Cisco common config','<Resync_On_Reset>Yes</Resync_On_Reset>
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
<Outbound_Proxy_1_>$localip</Outbound_Proxy_1_>','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('cisco.Fkey','Cisco SPA BLF template','<!-- Line Key $seq -->
<Extension_$seq_ ua="na">Disabled</Extension_$seq_>
<Short_Name_$seq_ ua="na">$label</Short_Name_$seq_>
<Share_Call_Appearance_$seq_ ua="na">private</Share_Call_Appearance_$seq_>','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('directory.xml','polycom local directory','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- $Revision: 1.73.6.2 $  $Date: 2006/07/17 21:46:42 $ -->
<directory>
  <item_list>
    <item>
      <ln>Doe</ln>
      <fn>John</fn>
      <ct>503</ct>
    </item>
  </item_list>
</directory>','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('panasonic.Fkey','Panasonic Fkey','FLEX_BUTTON_FACILITY_ACT$seq="X_PANASONIC_IPTEL_$type"
FLEX_BUTTON_FACILITY_ARG$seq="$value"
FLEX_BUTTON_LABEL$seq="$label"','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('panasonic.tls','panasonic tls fragment','SIP_RGSTR_PORT_1="$tlsport"
SIP_PRXY_PORT_1="$tlsport"
SIP_SRC_PORT_1="$tlsport"
SIP_PRSNC_PORT_1="$tlsport"
SIP_TRANSPORT_1="2"
SIP_TLS_MODE_1="1"
SRTP_CONNECT_MODE_1="0"','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('panasonic.udp','panasonic udp fragment','SIP_RGSTR_PORT_1="5060"
SIP_PRXY_PORT_1="5060"
SIP_SRC_PORT_1="5060"
SIP_TRANSPORT_1="0"
SIP_TLS_MODE_1="0"
SRTP_CONNECT_MODE_1="1"','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('panasonicHDV.Fkey','Panasonic KX-HDV Fkey','FLEX_BUTTON_FACILITY_ACT$seq="X_PANASONIC_IPTEL_$type"
FLEX_BUTTON_FACILITY_ARG$seq="$value"
FLEX_BUTTON_LABEL$seq="$label"','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('phone.cfg','polycom phone.cfg','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,technology,z_created,z_updated,z_updater) values ('polycom-locals.cfg','polycom local settings','polycom-locals.cfg','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>

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
</localcfg>','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('polycom-phone1.cfg','polycom phone generic','<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<!-- Registration info -->
<userinfo>
  <reg reg.1.displayName="$desc" reg.1.address="$ext" reg.1.label="$desc" reg.1.auth.userId="$ext" reg.1.auth.password="$password" />
</userinfo>','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('polycom.Fkey','Polycom Fkey','attendant.resourceList.$seq.address="sip:$value@$localip" attendant.resourceList.$seq.label="$label"  attendant.resourceList.$seq.type="normal"','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,blfkeyname,desc,provision,sipiaxfriend,technology,z_created,z_updated,z_updater) values ('snom D785','snom.Fkey','Snom D785 series','#INCLUDE snom.Common
user_realname1$: $desc
user_name1$: $ext
user_pname1$: $ext
user_pass1$: $password
user_host1$: $localip
#INCLUDE snom.Fkey','type=friend
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
allow=ulaw','SIP','2018-02-24 12:49:24','2018-02-24 12:49:24','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('snom.Common','Snom common config','; thou shalt have no other provsioning servers before me
setting_server: http://$localip/provisioning?mac={mac}
update_policy$: 

edit_alpha_mode&: 123

ldap_server$: $localip
ldap_port!: 
ldap_base!: $ldapbase
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

admin_mode_password!: 2222','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('snom.Fkey','Snom BLF template','fkey$seq$: $type $value
fkey_label$seq$: $label','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('snom.tls','snom tls fragment','user_outbound1$: $localip:$tlsport;transport=tls
user_srtp1$: on
user_auth_tag1$: off
user_savp1$: mandatory
','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('snom.udp','snom udp fragment','user_outbound1$:
user_srtp1$: off
user_srtp2$: off
user_auth_tag1$: off
user_savp1$: off','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,device,provision,technology,z_created,z_updated,z_updater) values ('y000000000000.cfg','Yealink T28 descriptor','y000000000000.cfg','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000004.cfg','Yealink T26 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000005.cfg','Yealink T22 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000007.cfg','Yealink T20 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000009.cfg','Yealink T18 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000023.cfg','Yealink VP530 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000025.cfg','Yealink W52P descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000028.cfg','Yealink T46 descriptor ','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000029.cfg','Yealink T42 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000031.cfg','Yealink T19 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000032.cfg','Yealink T32 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000034.cfg','Yealink T21 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000035.cfg','Yealink T48 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000036.cfg','Yealink T41 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000038.cfg','Yealink T38 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('y000000000053.cfg','Yealink T19 descriptor','#INCLUDE yealink.Common','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('yealink.Common','Yealink common Y file','#!version:1.0.0.1

##File header "#!version:1.0.0.1" can not be edited or deleted, and must be placed in the first line.##

local_time.time_zone = 0
local_time.time_zone_name = United Kingdom(London) 
local_time.ntp_server1 = pool.ntp.org
local_time.ntp_server2 = $localip

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

ldap.base = $ldapbase
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
ldap.dial_lookup =  1','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('yealink.Fkey','Yealink BLF Template','memorykey.$seq.line = 0 
memorykey.$seq.value = $value 
memorykey.$seq.pickup_value = *8
memorykey.$seq.type =  $type','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('yealink.Lkey','Yealink Line key template','linekey.$seq.line = 0 
linekey.$seq.value = $value 
linekey.$seq.pickup_value = *8
linekey.$seq.type =  $type
linekey.$seq.label =  $label','BLF Template','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('yealink.tls','Yealink tls fragment','account.1.outbound_port = $tlsport
account.1.transport = 2
account.1.srtp_encryption = 2
','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
INSERT OR IGNORE INTO Device(pkey,desc,provision,technology,z_created,z_updated,z_updater) values ('yealink.udp','Yealink udp fragment','account.1.transport = 0
account.1.outbound_port = 5060
account.1.srtp_encryption = 0
security.trust_certificates = 0','Descriptor','2018-02-24 12:49:23','2018-02-24 12:49:23','system');
COMMIT;
