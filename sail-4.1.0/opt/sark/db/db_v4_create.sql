BEGIN TRANSACTION;
CREATE TABLE IF NOT EXISTS Agent (
pkey TEXT PRIMARY KEY,
cluster TEXT,
conf TEXT,
name TEXT,
num TEXT,
passwd TEXT,
queue1 TEXT,
queue2 TEXT,
queue3 TEXT,
queue4 TEXT,
queue5 TEXT,
queue6 TEXT
);

CREATE TABLE IF NOT EXISTS Appl (
pkey TEXT PRIMARY KEY,
cluster TEXT,
desc TEXT,
extcode TEXT,
name TEXT,
span TEXT, 
striptags TEXT
);

CREATE TABLE IF NOT EXISTS COS (
pkey TEXT PRIMARY KEY,
active TEXT,
defaultclosed TEXT,
defaultopen TEXT,
dialplan TEXT, 
orideclosed TEXT, 
orideopen TEXT
);

CREATE TABLE IF NOT EXISTS Carrier (
pkey TEXT PRIMARY KEY,
carrier TEXT,
carriertype TEXT,
desc TEXT,
host TEXT,
md5encrypt TEXT,
provision TEXT,
register TEXT,
sipiaxpeer TEXT,
sipiaxuser TEXT,
technology TEXT,
zapcarfixed TEXT
);

CREATE TABLE IF NOT EXISTS Cluster (
pkey TEXT PRIMARY KEY,
abstimeout TEXT, 
callgroup TEXT,
chanmax TEXT,
include TEXT,
localarea TEXT, 
localdplan TEXT,
masteroclo TEXT,
name TEXT,
oclo TEXT,
operator TEXT,
pickupgroup TEXT
);

CREATE TABLE IF NOT EXISTS Device (
pkey TEXT PRIMARY KEY,
blfkeyname TEXT,
blfkeys INTEGER,
desc TEXT,
device TEXT,
fkeys INTEGER,
legacy TEXT,
noproxy TEXT,
pkeys INTEGER,
provision TEXT,
sipiaxfriend TEXT,
technology TEXT,
tftpname TEXT,
zapdevfixed TEXT
);

CREATE TABLE IF NOT EXISTS Greeting (
pkey TEXT PRIMARY KEY,
cluster TEXT,
desc TEXT, 
type TEXT
);

CREATE TABLE IF NOT EXISTS IPphone (
pkey TEXT PRIMARY KEY,
abstimeout TEXT,
basemacaddr TEXT,
callerid TEXT,
channel TEXT,
cluster TEXT,
desc TEXT,
device TEXT,
devicerec TEXT,
dialstring TEXT,
dvrvmail TEXT,
extalert TEXT,
externalip TEXT,
location TEXT,
macaddr TEXT,
openfirewall TEXT,
passwd TEXT,
provision TEXT,
sndcreds TEXT,
sipiaxfriend TEXT,
stealtime TEXT,
stolen TEXT,
technology TEXT,
twin TEXT,
vmailfwd TEXT
);

CREATE TABLE IF NOT EXISTS IPphone_FKEY (
pkey TEXT,
seq INTEGER,
device TEXT,
label TEXT,
type TEXT,
value TEXT,
PRIMARY KEY (pkey, seq)
);

CREATE TABLE IF NOT EXISTS Queue (
pkey TEXT PRIMARY KEY,
cluster TEXT,
conf TEXT,
devicerec TEXT,
name TEXT,
options TEXT, 
timeout INTEGER
);

CREATE TABLE IF NOT EXISTS Route (
pkey TEXT PRIMARY KEY,
active TEXT,
alternate TEXT,
auth TEXT,
cluster TEXT,
desc TEXT,
dialplan TEXT,
path1 TEXT,
path2 TEXT,
path3 TEXT,
path4 TEXT,
route TEXT, 
strategy TEXT
);

CREATE TABLE IF NOT EXISTS callback (
pkey TEXT PRIMARY KEY,
channel TEXT,
cluster TEXT,
desc TEXT,
number TEXT,
prefix TEXT
);

CREATE TABLE IF NOT EXISTS dateSeg (
pkey TEXT PRIMARY KEY,
cluster TEXT,
datemonth TEXT,
dayofweek TEXT,
desc TEXT,
month TEXT,
timespan TEXT
);

CREATE TABLE IF NOT EXISTS globals (
pkey TEXT PRIMARY KEY,
ABSTIMEOUT TEXT,
ACL TEXT, 
AGENTSTART TEXT,
ALERT TEXT,
ALLOWHASHXFER TEXT,
ASTDLIM TEXT,
ATTEMPTRESTART TEXT,
BINDADDR TEXT,
BLINDBUSY TEXT,
BOUNCEALERT TEXT,
CALLRECORD1 TEXT,
CAMPONQONOFF TEXT,
CAMPONQOPT TEXT,
CDR TEXT,
CFEXTRN TEXT,
CFWDEXTRNRULE TEXT, 
CFWDPROGRESS TEXT, 
CFWDANSWER TEXT, 
CLUSTER TEXT, 
CONFTYPE TEXT,
COSSTART TEXT,
COUNTRYCODE TEXT,
DIGITS TEXT,
EDOMAIN TEXT,
EMAILALERT TEXT,
EMERGENCY TEXT,
EXTLEN TEXT,
EXTLIM TEXT, 
FAX TEXT,
FAXDETECT TEXT,
FOPPASS TEXT,
G729 TEXT,
HAAUTOFAILBACK TEXT,
HACLUSTERIP TEXT,
HAENCRYPT TEXT,
HAMODE TEXT,
HAPRINODE TEXT,
HASECNODE TEXT,
HASYNCH TEXT,
HAUSECLUSTER TEXT,
INTRINGDELAY TEXT,
IVRKEYWAIT TEXT, 
IVRDIGITWAIT TEXT, 
LACL TEXT,
LANGUAGE TEXT,
LDAPBASE text,
LDAPOU text,
LDAPUSER text,
LDAPPASS text,
LEASEHDTIME TEXT, 
LOCALAREA TEXT, 
LOCALDLEN TEXT, 
LOCALIP TEXT,
LOGLEVEL TEXT, 
LOGOPTS TEXT,
LTERM TEXT,
MAXIN TEXT,
MEETMEDIAL TEXT,
MISDNRUN TEXT,
MIXMONITOR TEXT, 
MONITOROUT TEXT,
MONITORSTAGE TEXT,
MONITORTYPE TEXT,
MYCOMMIT TEXT,
NUMGROUPS TEXT,
ONBOARDMENU TEXT,
OPERATOR TEXT,
OPRT TEXT,
PWDLEN TEXT,
PCICARDS TEXT,
PLAYBEEP TEXT,
PLAYBUSY TEXT,
PLAYCONGESTED TEXT,
PROXY TEXT,
PROXYIGNORE TEXT,
RECFINALDEST TEXT,
RECLIMIT TEXT,
RECQDITHER TEXT,
RECQSEARCHLIM TEXT,
RECRSYNCPARMS TEXT,
RESTART TEXT,
RHINOSPF TEXT,
RINGDELAY TEXT,
RUNFOP TEXT,
SIPIAXSTART TEXT,
SIPMULTICAST TEXT,
SMSALERT TEXT,
SMSC TEXT,
SNO TEXT,
SPYPASS TEXT,
SUPEMAIL TEXT,
SYSOP TEXT,
SYSPASS TEXT,
TFTP TEXT,
UNDO TEXT,
UNDONUM TEXT,
UNDOONOFF TEXT,
USBRECDISK TEXT,
VDELAY TEXT,
VLIBS TEXT,
VMAILAGE TEXT,
VOICEINSTR TEXT,
VOIPMAX TEXT, 
XMPP TEXT, 
XMPPSERV TEXT,
ZTP TEXT
);

CREATE TABLE IF NOT EXISTS ivrmenu (
pkey TEXT PRIMARY KEY,
alert0 TEXT,
alert1 TEXT,
alert10 TEXT,
alert11 TEXT,
alert2 TEXT,
alert3 TEXT,
alert4 TEXT,
alert5 TEXT,
alert6 TEXT,
alert7 TEXT,
alert8 TEXT,
alert9 TEXT,
cluster TEXT,
greetnum TEXT,
listenforext TEXT,
name TEXT,
option0 TEXT,
option1 TEXT,
option10 TEXT,
option11 TEXT,
option2 TEXT,
option3 TEXT,
option4 TEXT,
option5 TEXT,
option6 TEXT,
option7 TEXT,
option8 TEXT,
option9 TEXT,
routeclass0 TEXT,
routeclass1 TEXT,
routeclass10 TEXT,
routeclass11 TEXT,
routeclass2 TEXT,
routeclass3 TEXT,
routeclass4 TEXT,
routeclass5 TEXT,
routeclass6 TEXT,
routeclass7 TEXT,
routeclass8 TEXT,
routeclass9 TEXT,
tag0 TEXT,
tag1 TEXT,
tag10 TEXT,
tag11 TEXT,
tag2 TEXT,
tag3 TEXT,
tag4 TEXT,
tag5 TEXT,
tag6 TEXT,
tag7 TEXT,
tag8 TEXT,
tag9 TEXT,
timeout TEXT,
timeoutrouteclass TEXT
);

CREATE TABLE IF NOT EXISTS lineIO (
pkey TEXT PRIMARY KEY,
active TEXT,
alertinfo TEXT,
callback TEXT,
callerid TEXT,
callprogress TEXT,
carrier TEXT,
channel TEXT, 
closecallback TEXT,
closecustom TEXT,
closedisa TEXT,
closeext TEXT,
closegreet TEXT,
closeivr TEXT,
closequeue TEXT,
closeroute TEXT,
closesibling TEXT,
closespeed TEXT,
cluster TEXT,
custom TEXT,
desc TEXT,
devicerec TEXT,
didnumber TEXT,
disa TEXT,
disapass TEXT,
ext TEXT,
faxdetect TEXT,
forceivr TEXT,
host TEXT,
inprefix TEXT,
lcl TEXT,
macaddr TEXT,
match TEXT,
method TEXT,
moh TEXT,
monitor TEXT,
openfirewall TEXT, 
opengreet TEXT,
openroute TEXT,
opensibling TEXT,
password TEXT,
pat TEXT,
peername TEXT,
postdial TEXT,
predial TEXT,
privileged TEXT,
provision TEXT,
queue TEXT,
register TEXT,
remotenum TEXT,
routeable TEXT, 
routeclassopen TEXT,
routeclassclosed TEXT,
service TEXT,
sipiaxpeer TEXT,
sipiaxuser TEXT,
speed TEXT,
subnet TEXT, 
subnet1 TEXT, 
subnet2 TEXT, 
subnetstr TEXT, 
subnet1str TEXT, 
subnet2str TEXT,
swoclip TEXT,
tag TEXT,
technology TEXT,
transform TEXT,
transformclip TEXT,
trunk TEXT,
trunkname TEXT,
username TEXT,
zapcaruser TEXT
);

CREATE TABLE IF NOT EXISTS mfgmac (
pkey TEXT PRIMARY KEY,
name TEXT,
notify TEXT
);

CREATE TABLE IF NOT EXISTS netphone (
pkey TEXT PRIMARY KEY,
vendor TEXT,
model TEXT
);

CREATE TABLE IF NOT EXISTS page (
pkey TEXT PRIMARY KEY,
pagegroup TEXT
);

CREATE TABLE IF NOT EXISTS speed (
pkey TEXT PRIMARY KEY,
callerid TEXT,
calleridname TEXT,
cluster TEXT,
desc TEXT,
devicerec TEXT,
greeting TEXT,
grouptype TEXT,
longdesc TEXT,
obeydnd TEXT,
out TEXT,
outcome TEXT,
outcomerouteclass TEXT,
pagegroup TEXT,
ringdelay TEXT,
speedalert TEXT,
trunk TEXT
);

CREATE TABLE IF NOT EXISTS IPphoneCOSopen (
IPphone_pkey TEXT,
COS_pkey TEXT,
PRIMARY KEY (IPphone_pkey, COS_pkey)
);

CREATE TABLE IF NOT EXISTS IPphoneCOSclosed (
IPphone_pkey TEXT,
COS_pkey TEXT,
PRIMARY KEY (IPphone_pkey, COS_pkey)
);

CREATE TABLE IF NOT EXISTS tt_help_core (
pkey TEXT PRIMARY KEY,
displayname TEXT,
htext TEXT,
name TEXT
);

CREATE TABLE IF NOT EXISTS Panel (
pkey INTEGER PRIMARY KEY,
classname TEXT,
displayname TEXT,
weight text
);

CREATE TABLE IF NOT EXISTS PanelGroup (
pkey INTEGER PRIMARY KEY,
groupname TEXT
);

CREATE TABLE IF NOT EXISTS User (
pkey TEXT PRIMARY KEY,
cluster TEXT,
email TEXT,
extension text,
password,
readonly,
selection TEXT
);

CREATE TABLE IF NOT EXISTS UserPanel (
User_pkey TEXT,
Panel_pkey TEXT,
PRIMARY KEY (User_pkey, Panel_pkey)
);

CREATE TABLE IF NOT EXISTS PanelGroupPanel (
PanelGroup_pkey TEXT,
Panel_pkey TEXT,
PRIMARY KEY (PanelGroup_pkey, Panel_pkey)
);

CREATE TABLE IF NOT EXISTS mcast (
pkey TEXT PRIMARY KEY,
mcastdesc TEXT,
mcastip TEXT,
mcastport TEXT,
mcastlport TEXT,
mcasttype TEXT
);

CREATE TABLE IF NOT EXISTS vendorxref(
pkey TEXT PRIMARY KEY, 
intpkey TEXT
);

CREATE TABLE IF NOT EXISTS Device_FKEY (
pkey TEXT,
seq INTEGER,
device TEXT,
label TEXT,
type TEXT,
value TEXT,
PRIMARY KEY (pkey, seq)
);

CREATE TABLE IF NOT EXISTS Device_atl (
pkey TEXT PRIMARY KEY
);

CREATE TABLE IF NOT EXISTS meetme (
pkey TEXT PRIMARY KEY,
adminpin TEXT,
description TEXT,
pin TEXT,
type TEXT
);

COMMIT;
