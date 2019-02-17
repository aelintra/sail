BEGIN TRANSACTION;

/* Agent */
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
queue6 TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Custom App */
CREATE TABLE IF NOT EXISTS Appl (
pkey TEXT PRIMARY KEY,
cluster TEXT,
desc TEXT,
extcode TEXT,
name TEXT,
span TEXT, 
striptags TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Class of service */
CREATE TABLE IF NOT EXISTS COS (
pkey TEXT PRIMARY KEY,
active TEXT,
defaultclosed TEXT,
defaultopen TEXT,
description TEXT,
dialplan TEXT, 
orideclosed TEXT, 
orideopen TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Carrier */
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
zapcarfixed TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Tenant */
CREATE TABLE IF NOT EXISTS Cluster (
pkey TEXT PRIMARY KEY,
abstimeout TEXT, 					-- absolute timeout (in seconds)
clusterclid TEXT,					-- cluster main CLID
callgroup TEXT,   					-- asterisk callgroup number (1-63)
chanmax TEXT,     					-- maximum active calls
description TEXT, 					-- freeform description
include TEXT,     					-- whitespace separated list of clusters OR, the keyword ALL
localarea TEXT,   					-- local area code
localdplan TEXT,  					-- local dialplan
masteroclo TEXT,  					-- master day/night throw
name TEXT,						-- V2; not used
oclo TEXT,        					-- calculated day/night throw
operator TEXT,    					-- tenant sysop
pickupgroup TEXT,  					-- asterisk pickupgroup number (1-63)
routeclassoverride TEXT,				-- Holiday scheduler route class override
routeoverride TEXT,					-- Holiday scheduler route override
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* phone types */
CREATE TABLE IF NOT EXISTS Device (
pkey TEXT PRIMARY KEY,
blfkeyname TEXT,
blfkeys INTEGER,
desc TEXT,
device TEXT,
fkeys INTEGER,
imageurl TEXT,
legacy TEXT,
noproxy TEXT,
owner TEXT DEFAULT 'system',
pkeys INTEGER,
provision TEXT,
sipiaxfriend TEXT,
technology TEXT,
tftpname TEXT,
zapdevfixed TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* system greetings */
CREATE TABLE IF NOT EXISTS Greeting (
pkey TEXT PRIMARY KEY,
cluster TEXT,							-- Tenant	
desc TEXT, 								-- Description
type TEXT,								-- MIME type
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Holiday overrides */
CREATE TABLE IF NOT EXISTS Holiday (
id INTEGER PRIMARY KEY,
pkey TEXT,								-- not really used but satisfies tuple builder
cluster TEXT DEFAULT 'default',			-- tenant
desc TEXT,								-- Description						
route TEXT,								-- Holiday scheduler route override
routeclass TEXT,						-- Holiday scheduler route class override
stime INTEGER,							-- Epoch start
etime INTEGER,							-- Epoch end
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Extensions */
CREATE TABLE IF NOT EXISTS IPphone (
pkey TEXT PRIMARY KEY,
abstimeout TEXT,
active TEXT DEFAULT 'YES',				-- Active/inactive flag
basemacaddr TEXT,                       -- not used             
callerid TEXT,                          -- CLID
callbackto TEXT DEFAULT 'desk',			-- who we callback (ext/cell)
cellphone TEXT,							-- cellphone twin
celltwin TEXT,							-- cell twin on/off
channel TEXT,                           -- not used
cluster TEXT,                           -- Tenant
desc TEXT,                              -- asterisk username
device TEXT,                            -- device vendor
devicemodel TEXT,						-- Harvested model number
devicerec TEXT,                         -- recopts
dialstring TEXT,                        -- not used
dvrvmail TEXT,                          -- mailbox
extalert TEXT,                          -- alert info
externalip TEXT,                        -- not used
firstseen TEXT,							-- first date provisioned (or NULL)
lastseen TEXT,							-- last date provisioned (or NULL)
location TEXT,                          -- local/remote
macaddr TEXT,                           -- macaddr
newformat TEXT,							-- set to YES for new format SIP entries
openfirewall TEXT,                      -- not used
passwd TEXT,                            -- asterisk password
protocol DEFAULT 'IPV4',				-- IPV4/IPV6
provision TEXT,                         -- provisioning string 
provisionwith TEXT DEFAULT 'IP',		-- how to provision my id - IP address or FQDN   
sndcreds TEXT DEFAULT 'Always',         -- send creds with provisioning
sipiaxfriend TEXT,                      -- asterisk SIP string
stealtime TEXT,                         -- time this extension was stolen by HD
stolen TEXT,                            -- HD thief 
technology TEXT,                        -- SIP 
tls TEXT,                        		-- SSIP on/off
transport TEXT DEFAULT 'udp',			-- transport(udp/tcp/tls)
twin TEXT,                              -- not used
vmailfwd TEXT,                          -- not used
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* phone function (blf) keys */
CREATE TABLE IF NOT EXISTS IPphone_FKEY (
pkey TEXT,                              -- owner extension
seq INTEGER,                            -- blf/dss number
device TEXT,                            -- device type
label TEXT,                             -- blf label
type TEXT,                              -- blf type
value TEXT,                             -- blf value
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system',
PRIMARY KEY (pkey, seq)
);

/* call queues */
CREATE TABLE IF NOT EXISTS Queue (
pkey TEXT PRIMARY KEY,
cluster TEXT,
conf TEXT,
devicerec TEXT,
greetnum TEXT DEFAULT 'None',
name TEXT,
options TEXT,
outcome TEXT, 
timeout INTEGER,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Outbound routing */
CREATE TABLE IF NOT EXISTS Route (
pkey TEXT PRIMARY KEY,
active TEXT DEFAULT 'YES',
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
strategy TEXT DEFAULT 'hunt',
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* callback fature */
CREATE TABLE IF NOT EXISTS callback (
pkey TEXT PRIMARY KEY,
channel TEXT,
cluster TEXT,
desc TEXT,
number TEXT,
prefix TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* open/closed automation */
CREATE TABLE IF NOT EXISTS dateSeg (
id INTEGER PRIMARY KEY,
pkey INTEGER,
cluster TEXT,
datemonth TEXT,
dayofweek TEXT,
desc TEXT,
month TEXT,
state TEXT DEFAULT 'IDLE',
timespan TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system',
UNIQUE (pkey)
);

/* system settings */
CREATE TABLE IF NOT EXISTS globals (
pkey TEXT PRIMARY KEY,
ABSTIMEOUT INTEGER DEFAULT 14400,   -- 4 hours
ACL TEXT,                           -- ON/OFF 
AGENTSTART TEXT DEFAULT 6001,	    -- Agent start number
ALERT TEXT,							-- not used in 4.x 
ALLOWHASHXFER TEXT,                 -- Allow asterisk non-SIP xfer
ASTDLIM TEXT,                       -- Asterisk delimiter ','
ATTEMPTRESTART TEXT,                -- not used in 4.x 
BINDADDR TEXT,                      -- Asterisk SIP bindaddr
BLINDBUSY TEXT,                     -- blind transfer busy bounce
BOUNCEALERT TEXT,                   -- alertinfo string for blind transfer bounce
CALLRECORD1 TEXT,					-- call recording defaults
CAMPONQONOFF TEXT,                  -- camp-on miniqueue enable
CAMPONQOPT TEXT,                    -- camp-on miniqueue options
CDR TEXT,                           -- not used
CFEXTRN TEXT,                       -- allow cforward to external numbers
CFWDEXTRNRULE TEXT,     			-- not used in 4.x 
CFWDPROGRESS TEXT,                  -- progress tones for cfwd
CFWDANSWER TEXT,                    -- take call off-hook before forward to external
CLUSTER TEXT DEFAULT 'OFF',		    -- dtenant support ON/OFF
CONFTYPE TEXT,                      -- conference type - deprecated in 4.1
COSSTART TEXT,                      -- COS on/off                      
COUNTRYCODE TEXT,                   -- countrycode
DIGITS TEXT,                    	-- not used in 4.x 
EDOMAIN TEXT,                       -- external IP address of this server
EURL TEXT							-- external URL for remote phones
EMAILALERT TEXT,                    -- email alert address
EMERGENCY TEXT,                     -- emergency numbers which bypass COS
EXTBLKLST TEXT DEFAULT NO,			-- YES/NO loads voipbl.com external SIP blacklist into an ipset
EXTLEN TEXT,                        -- extension length
EXTLIM TEXT,        				-- not used in 4.x 
FAX TEXT,                           -- FAX flag
FAXDETECT TEXT,                     -- FAX detect on/off
FOPPASS TEXT,                       -- Flash opeartor panel password
FQDN TEXT,							-- FQDN V5+
FQDNDROPBUFF TEXT DEFAULT 100,		-- fqdn drop set size (in entries)
FQDNINSPECT TEXT DEFAULT NO,		-- Require FQDN in SIP Ops Shorewall 4.6+
FQDNHTTP TEXT DEFAULT NO,			-- Require FQDN in remote HTTP Ops 
FQDNPROV TEXT,						-- use FQDN in remote provisioning YES/NO
FQDNTRUST TEXT DEFAULT NO,			-- construct an ipset of trusted IP's from a list of trusted fqdns
G729 TEXT,                          -- G729 switch - not used
HAAUTOFAILBACK TEXT,                -- not used after asha 2
HACLUSTERIP TEXT,                   -- cluster ip fr HA
HAENCRYPT TEXT,                     -- not used in 4.x 
HAMODE TEXT,                        -- not used in 4.x
HAPRINODE TEXT,                     -- not used in 4.x
HASECNODE TEXT,                     -- not used in 4.x
HASYNCH TEXT,                       -- not used in 4.x
HAUSECLUSTER TEXT,                  -- use cluster virt IP when provisioning
INTRINGDELAY TEXT,                  -- ring time before voicemail
IVRKEYWAIT TEXT,                    -- IVR key wait
IVRDIGITWAIT TEXT,                  -- IVR inter-digit wait
LACL TEXT,							-- Generate ACLs
LANGUAGE TEXT,                      -- not used
LDAPBASE text,                      -- LDAP base
LDAPOU text,                        -- LDAP OU
LDAPUSER text,                      -- LDAP user
LDAPPASS text,                      -- LDAP password
LEASEHDTIME TEXT,                   -- Hot desk lease time
LKEY TEXT,							-- not used
LOCALAREA TEXT,                     -- not used (See Cluster)
LOCALDLEN TEXT,                     -- not used (See Cluster)
LOCALIP TEXT,                       -- local ip address
LOGLEVEL TEXT DEFAULT 0,            -- internal log level
LOGOPTS TEXT,                       -- not used
LTERM TEXT,                         -- late termination flag
MAXIN TEXT,                         -- maximum inbound calls
MEETMEDIAL TEXT,                    -- not used in 4.x 
MISDNRUN TEXT,                      -- not used in 4.x 
MIXMONITOR TEXT,                    -- force mixmonitor on all recordings
MONITOROUT TEXT,                    -- monitorout folder
MONITORSTAGE TEXT,                  -- monstage folder
MONITORTYPE TEXT,					-- Monitor or Mixmonitor
MYCOMMIT TEXT,                      -- commit outstanding
NATDEFAULT TEXT DEFAULT local, 		-- V6 NAT defaiult local/remote
NUMGROUPS TEXT,                     -- not used in 4.x 
ONBOARDMENU TEXT,                   -- not used in 4.x 
OPERATOR TEXT DEFAULT 0,            -- sysop
OPRT TEXT,                          -- not used in 4.x 
PWDLEN TEXT,                        -- password length
PCICARDS TEXT,                      -- not used in 4.x 
PKTINSPECT TEXT,					-- not used
PLAYBEEP TEXT,                      -- play beep on failover
PLAYBUSY TEXT,                      -- play busy message or tones
PLAYCONGESTED TEXT,                 -- play congested message or tones
PLAYTRANSFER TEXT DEFAULT YES,     	-- play transfer message when transferring off the PBX
PROXY TEXT,                         -- allow proxy operations
PROXYIGNORE TEXT,                   -- not used in 4.x
RECFINALDEST TEXT,                  -- recordings folder
RECLIMIT TEXT,                      -- Recording folder max size
RECQDITHER TEXT,                    -- dither (ms) on queuelog searches
RECQSEARCHLIM TEXT,                 -- search limit on queuelog
RECRSYNCPARMS TEXT,                 -- not used in 4.x 
RESTART TEXT,                       -- not used in 4.x
RHINOSPF TEXT,                      -- not used in 4.x (see asha)
RINGDELAY TEXT,                     -- default ring timeout (seconds)
RUNFOP TEXT,                        -- generate FOP objects
SESSIONTIMOUT INTEGER DEFAULT 600,  -- sessiontimeout (10minutes)
SENDEDOMAIN TEXT DEFAULT YES,  		-- Send public IP in SIP header YES/NO
SIPIAXSTART TEXT,                   -- lowest extension number
SIPFLOOD TEXT DEFAULT NO,			-- detect SIP flood YES/NO
SIPMULTICAST TEXT,                  -- listen for multicast provisioning requests
SMSALERT TEXT,                      -- not used in 4.x 
SMSC TEXT,                          -- not used in 4.x 
SNO TEXT,                           -- not used in 4.x 
SPYPASS TEXT,                       -- password for SPY ops
SUPEMAIL TEXT,                      -- supervisor email
SYSOP TEXT DEFAULT 0,                         -- system operator real extension
SYSPASS TEXT,                       -- password for sysops
TFTP TEXT,                          -- deprecated in 4.0, deleted in 4.1
TLSPORT	TEXT,						-- TLS port (default 5061)
UNDO TEXT,                          -- not used in 4.x 
UNDONUM TEXT,                       -- not used in 4.x 
UNDOONOFF TEXT,                     -- not used in 4.x 
USBRECDISK TEXT,                    -- not used in 4.x 
USEROTP TEXT DEFAULT NULL,			-- V6 default OTP.  Seeded by the generator
USERCREATE TEXT DEFAULT NO,			-- V6 create user when extension created YES/NO		
VCL TEXT,							-- V5 cloud enabled (true/false)
VCLFULL TEXT,						-- V5 cloud param
VDELAY TEXT,                        -- artificial ring on inbound SIP
VLIBS TEXT,                         -- not used in 4.x 
VMAILAGE TEXT,                      -- oldest age of vmail
VOICEINSTR TEXT,                    -- play long or short Vmail instructions
VOIPMAX TEXT,                       -- MAX outbound up calls
XMPP TEXT,                          -- not used in 4.x 
XMPPSERV TEXT,                      -- not used in 4.x 
ZTP TEXT,                           -- Zero touch provisioning on/off
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* IVR menus */
CREATE TABLE IF NOT EXISTS ivrmenu (
pkey TEXT PRIMARY KEY,
alert0 TEXT,						-- Alertinfo for each keypress
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
description TEXT DEFAULT 'None',
cluster TEXT,
greetnum TEXT DEFAULT 'None',						-- greeting number to play
listenforext TEXT,
name TEXT,
option0 TEXT,						-- routed name for each keypress
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
routeclass0 TEXT,					-- routeclass for each keypress
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
tag0 TEXT,							-- alphatag for each keypress
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
timeout TEXT,						-- timeout name 					
timeoutrouteclass TEXT,				-- timeout routeclass
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* trunks and DDI's */
CREATE TABLE IF NOT EXISTS lineIO (
pkey TEXT PRIMARY KEY,
active TEXT DEFAULT 'YES',	-- Active/inactive flag
alertinfo TEXT,				-- distinctive ring
callback TEXT,				-- denotes callback trunk
callerid TEXT,				-- high-order (weak) CLID
callprogress TEXT,			-- send progress tones on dial
carrier TEXT,				-- Foreign key to carrier class
channel TEXT, 				-- used by analogue trunks
closecallback TEXT,			-- not used
closecustom TEXT,			-- not used
closedisa TEXT,				-- not used
closeext TEXT,				-- not used
closegreet TEXT,			-- default closed greeting
closeivr TEXT,				-- not used
closequeue TEXT,			-- not used
closeroute TEXT,			-- closed inbound route
closesibling TEXT,			-- not used
closespeed TEXT,			-- not used
cluster TEXT,				-- cluster (Tenant) this trunk belongs to
custom TEXT,				-- Custome dial string for non-standard technologies 
desc TEXT,					-- weak Asterisk username 
description TEXT,			-- description
devicerec TEXT,				-- RECOPTS
didnumber TEXT,				-- Used on older dual trunks with only one DiD
disa TEXT,					-- DISA capable trunk
disapass TEXT,				-- DISA password
ext TEXT,					-- unknown, not used	
faxdetect TEXT,				-- FAX detect for analgue lines
forceivr TEXT,				-- Not used in 4.x
host TEXT,					-- Host IP address
inprefix TEXT,				-- prepend prefix on inbound
lcl TEXT,					-- denotes a local endpoint (no longer used)
macaddr TEXT,				-- Not used
match TEXT,					-- trunk seize sequence
method TEXT,				-- referenced in extensions generator but no setter in 4.x
moh TEXT,					-- play moh instead of ring
monitor TEXT,				-- referenced in Helper but no setter
openfirewall TEXT, 			-- not used in 4.0.x+
opengreet TEXT,				-- default open greeting
openroute TEXT,				-- open inbound route
opensibling TEXT,			-- not used
password TEXT,				-- far end password
pat TEXT,					-- V2; no longer used
peername TEXT,				-- strong Asterisk username
postdial TEXT,				-- post dial string for custom trunks	
predial TEXT,				-- pre dial string for custom trunks
privileged TEXT,			-- IAX siblings ONLY
provision TEXT,				-- not used
queue TEXT,					-- not used
register TEXT,				-- registration string
remotenum TEXT,				-- used in sibling links
routeable TEXT,				-- denotes whether Analogue lines are routeable 
routeclassopen TEXT,		-- routeclass
routeclassclosed TEXT,		-- routeclass
service TEXT,				-- Not used
sipiaxpeer TEXT,			-- Asterisk peer stanza
sipiaxuser TEXT,			-- Asterisk user stanza
speed TEXT,					-- not used
swoclip TEXT DEFAULT 'YES',	-- Switch On CLIP
tag TEXT,					-- Alpha tag
technology TEXT,			-- SIP/IAX/DAHDI/Custom
transform TEXT,				-- Transformation mask
transformclip TEXT,			-- Not used
trunk TEXT,					-- old V2 value, no longer used
trunkname TEXT,				-- freeform trunkname
username TEXT,				-- far end username
zapcaruser TEXT,			-- Dahdi creds
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* manufacturer MAC roots */
CREATE TABLE IF NOT EXISTS mfgmac (
pkey TEXT PRIMARY KEY,
name TEXT,
notify TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* page groups */
CREATE TABLE IF NOT EXISTS page (
pkey TEXT PRIMARY KEY,
pagegroup TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* callgroups */
CREATE TABLE IF NOT EXISTS speed (
pkey TEXT PRIMARY KEY,
callerid TEXT,
calleridname TEXT,
cluster TEXT,
desc TEXT,
devicerec TEXT,
dialparamshunt TEXT,
dialparamsring TEXT,
divert TEXT,
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
trunk TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* Class of service */
CREATE TABLE IF NOT EXISTS IPphoneCOSopen (
IPphone_pkey TEXT,
COS_pkey TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system',
PRIMARY KEY (IPphone_pkey, COS_pkey)
);

/* Class of service */
CREATE TABLE IF NOT EXISTS IPphoneCOSclosed (
IPphone_pkey TEXT,
COS_pkey TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system',
PRIMARY KEY (IPphone_pkey, COS_pkey)
);

/* Intrusion attempts */
CREATE TABLE IF NOT EXISTS threat (
pkey TEXT PRIMARY KEY,
asn TEXT,
firstseen TEXT,
hits INTEGER,
isp TEXT,
lastseen TEXT,
loc TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* messages */
CREATE TABLE IF NOT EXISTS tt_help_core (
pkey TEXT PRIMARY KEY,
displayname TEXT,
htext TEXT,
name TEXT
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* navigation database */
CREATE TABLE IF NOT EXISTS Panel (
pkey INTEGER PRIMARY KEY,
classname TEXT,
displayname TEXT,
weight TEXT,
ability TEXT DEFAULT 'create',    
active TEXT DEFAULT 'yes',
fastnew TEXT DEFAULT 'yes'
);

/* menu tab headings */
CREATE TABLE IF NOT EXISTS PanelGroup (
pkey INTEGER PRIMARY KEY,
groupname TEXT
);

/* panel tab relationship */
CREATE TABLE IF NOT EXISTS PanelGroupPanel (
PanelGroup_pkey TEXT,
Panel_pkey TEXT,
PRIMARY KEY (PanelGroup_pkey, Panel_pkey)
);

/* system admins and users */
CREATE TABLE IF NOT EXISTS User (
id INTEGER PRIMARY KEY,
pkey TEXT NOT NULL,					-- UID
cluster TEXT,						-- Home tenant
email TEXT DEFAULT 'None',							-- email
extension TEXT,						-- extension
lastlogin datetime,					-- date/time of last login
password TEXT,						-- password
readonly TEXT,						-- read flag (not used)
realname TEXT,						-- User real name or nickname	
salt TEXT,							-- salt
selection TEXT,						-- (removed in V6) user class (enduser|poweruser|tenant|admin)
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system',
UNIQUE (pkey),
UNIQUE (extension)
);

/* user panel relationship */
CREATE TABLE IF NOT EXISTS UserPanel (
User_pkey TEXT,
Panel_pkey TEXT,
perms TEXT DEFAULT 'view',			-- permissions view/update/create
PRIMARY KEY (User_pkey, Panel_pkey)
);



/* multicast bcast */
CREATE TABLE IF NOT EXISTS mcast (
pkey TEXT PRIMARY KEY,
mcastdesc TEXT,
mcastip TEXT,
mcastport TEXT,
mcastlport TEXT,
mcasttype TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);


/* vendors by MAC deleted in 5.x
CREATE TABLE IF NOT EXISTS vendorxref(
pkey TEXT PRIMARY KEY, 
intpkey TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);
*/

/* Fkey templates */
CREATE TABLE IF NOT EXISTS Device_FKEY (
pkey TEXT,
seq INTEGER,
device TEXT,
label TEXT,
type TEXT,
value TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system',
PRIMARY KEY (pkey, seq)
);

/* device templates managed by Aelintra - replaced with flag in 5.x
CREATE TABLE IF NOT EXISTS Device_atl (
pkey TEXT PRIMARY KEY,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);
*/

/* conference rooms */
CREATE TABLE IF NOT EXISTS meetme (
pkey TEXT PRIMARY KEY,
cluster TEXT,
adminpin TEXT DEFAULT 'None',
description TEXT,
pin TEXT default 'None',
type TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

/* master xref */

CREATE TABLE IF NOT EXISTS master_xref (
id integer PRIMARY KEY,
pkey TEXT NOT NULL,
relation TEXT
);

CREATE TABLE IF NOT EXISTS shorewall_blacklist (
pkey integer PRIMARY KEY,
source TEXT,
comment TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

CREATE TABLE IF NOT EXISTS shorewall_whitelist (
pkey integer PRIMARY KEY,
fqdn TEXT,
comment TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

CREATE TABLE IF NOT EXISTS clid_blacklist (
pkey TEXT PRIMARY KEY,
action TEXT DEFAULT 'Hangup',
cluster TEXT DEFAULT 'default',
desc TEXT,
z_created datetime,
z_updated datetime,
z_updater TEXT DEFAULT 'system'
);

CREATE TABLE IF NOT EXISTS master_audit (
pkey integer PRIMARY KEY,
act TEXT,
owner TEXT,
relation TEXT,
tstamp datetime
);

/* audit triggers */

CREATE TRIGGER agent_insert AFTER INSERT ON agent
BEGIN
   UPDATE agent set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'agent', datetime('now'));   
END;
CREATE TRIGGER agent_update AFTER UPDATE ON agent
BEGIN
   UPDATE agent set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'agent', datetime('now'));
END;
CREATE TRIGGER agent_delete AFTER DELETE ON agent
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'agent', datetime('now'));
END;

CREATE TRIGGER appl_insert AFTER INSERT ON appl
BEGIN
   UPDATE appl set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'appl', datetime('now'));   
END;
CREATE TRIGGER appl_update AFTER UPDATE ON appl
BEGIN
   UPDATE appl set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'appl', datetime('now'));
END;
CREATE TRIGGER appl_delete AFTER DELETE ON appl
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'appl', datetime('now'));
END;

CREATE TRIGGER clid_blacklist_insert AFTER INSERT ON clid_blacklist
BEGIN
   UPDATE clid_blacklist set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'clid_blacklist', datetime('now'));   
END;
CREATE TRIGGER clid_blacklist_update AFTER UPDATE ON clid_blacklist
BEGIN
   UPDATE clid_blacklist set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'clid_blacklist', datetime('now'));
END;
CREATE TRIGGER clid_blacklist_delete AFTER DELETE ON clid_blacklist
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'clid_blacklist', datetime('now'));
END;

CREATE TRIGGER COS_insert AFTER INSERT ON COS
BEGIN
   UPDATE COS set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'COS', datetime('now'));   
END;
CREATE TRIGGER COS_update AFTER UPDATE ON COS
BEGIN
   UPDATE COS set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'COS', datetime('now'));
END;
CREATE TRIGGER COS_delete AFTER DELETE ON COS
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'COS', datetime('now'));
END;

CREATE TRIGGER Carrier_insert AFTER INSERT ON Carrier
BEGIN
   UPDATE Carrier set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Carrier', datetime('now'));   
END;
CREATE TRIGGER Carrier_update AFTER UPDATE ON Carrier
BEGIN
   UPDATE Carrier set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Carrier', datetime('now'));
END;
CREATE TRIGGER Carrier_delete AFTER DELETE ON Carrier
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Carrier', datetime('now'));
END;

CREATE TRIGGER Cluster_insert AFTER INSERT ON Cluster
BEGIN
   UPDATE Cluster set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Cluster', datetime('now'));   
END;
CREATE TRIGGER Cluster_update AFTER UPDATE ON Cluster
BEGIN
   UPDATE Cluster set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Cluster', datetime('now'));
END;
CREATE TRIGGER Cluster_delete AFTER DELETE ON Cluster
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Cluster', datetime('now'));
END;

CREATE TRIGGER Device_insert AFTER INSERT ON Device
BEGIN
   UPDATE Device set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Device', datetime('now'));   
END;
CREATE TRIGGER Device_update AFTER UPDATE ON Device
BEGIN
   UPDATE Device set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Device', datetime('now'));
END;
CREATE TRIGGER Device_delete AFTER DELETE ON Device
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Device', datetime('now'));
END;

CREATE TRIGGER Device_FKEY_insert AFTER INSERT ON Device_FKEY
BEGIN
   UPDATE Device_FKEY set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Device_FKEY', datetime('now'));   
END;
CREATE TRIGGER Device_FKEY_update AFTER UPDATE ON Device_FKEY
BEGIN
   UPDATE Device_FKEY set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Device_FKEY', datetime('now'));
END;
CREATE TRIGGER Device_FKEY_delete AFTER DELETE ON Device_FKEY
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Device_FKEY', datetime('now'));
END;
CREATE TRIGGER Greeting_insert AFTER INSERT ON Greeting
BEGIN
   UPDATE Greeting set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Greeting', datetime('now'));   
END;
CREATE TRIGGER Greeting_update AFTER UPDATE ON Greeting
BEGIN
   UPDATE Greeting set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Greeting', datetime('now'));
END;
CREATE TRIGGER Greeting_delete AFTER DELETE ON Greeting
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Greeting', datetime('now'));
END;

CREATE TRIGGER Holidy_insert AFTER INSERT ON Holiday
BEGIN
   UPDATE Holiday set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Holiday', datetime('now'));   
END;
CREATE TRIGGER Holiday_update AFTER UPDATE ON Holiday
BEGIN
   UPDATE Holiday set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Holiday', datetime('now'));
END;
CREATE TRIGGER Holiday_delete AFTER DELETE ON Holiday
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Holiday', datetime('now'));
END;

CREATE TRIGGER IPphone_insert AFTER INSERT ON IPphone
BEGIN
   UPDATE IPphone set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'IPphone', datetime('now'));   
END;
CREATE TRIGGER IPphone_update AFTER UPDATE ON IPphone
BEGIN
   UPDATE IPphone set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'IPphone', datetime('now'));
END;
CREATE TRIGGER IPphone_delete AFTER DELETE ON IPphone
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'IPphone', datetime('now'));
END;

CREATE TRIGGER IPphone_FKEY_insert AFTER INSERT ON IPphone_FKEY
BEGIN
   UPDATE IPphone_FKEY set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'IPphone_FKEY', datetime('now'));   
END;
CREATE TRIGGER IPphone_FKEY_update AFTER UPDATE ON IPphone_FKEY
BEGIN
   UPDATE IPphone_FKEY set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'IPphone_FKEY', datetime('now'));
END;
CREATE TRIGGER IPphone_FKEY_delete AFTER DELETE ON IPphone_FKEY
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'IPphone_FKEY', datetime('now'));
END;

CREATE TRIGGER Queue_insert AFTER INSERT ON Queue
BEGIN
   UPDATE Queue set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Queue', datetime('now'));   
END;
CREATE TRIGGER Queue_update AFTER UPDATE ON Queue
BEGIN
   UPDATE Queue set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Queue', datetime('now'));
END;
CREATE TRIGGER Queue_delete AFTER DELETE ON Queue
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Queue', datetime('now'));
END;

CREATE TRIGGER Route_insert AFTER INSERT ON Route
BEGIN
   UPDATE Route set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Route', datetime('now'));   
END;
CREATE TRIGGER Route_update AFTER UPDATE ON Route
BEGIN
   UPDATE Route set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Route', datetime('now'));
END;
CREATE TRIGGER Route_delete AFTER DELETE ON Route
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Route', datetime('now'));
END;

CREATE TRIGGER shorewall_blacklist_insert AFTER INSERT ON shorewall_blacklist
BEGIN
   UPDATE shorewall_blacklist set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'shorewall_blacklist', datetime('now'));   
END;
CREATE TRIGGER shorewall_blacklist_update AFTER UPDATE ON shorewall_blacklist
BEGIN
   UPDATE shorewall_blacklist set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'shorewall_blacklist', datetime('now'));
END;
CREATE TRIGGER shorewall_blacklist_delete AFTER DELETE ON shorewall_blacklist
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'shorewall_blacklist', datetime('now'));
END;

CREATE TRIGGER User_insert AFTER INSERT ON User
BEGIN
   UPDATE User set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'User', datetime('now'));   
END;
CREATE TRIGGER User_update AFTER UPDATE ON User
BEGIN
   UPDATE User set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'User', datetime('now'));
END;
CREATE TRIGGER User_delete AFTER DELETE ON User
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'User', datetime('now'));
END;

CREATE TRIGGER Callback_insert AFTER INSERT ON Callback
BEGIN
   UPDATE Callback set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'Callback', datetime('now'));   
END;
CREATE TRIGGER Callback_update AFTER UPDATE ON Callback
BEGIN
   UPDATE Callback set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'Callback', datetime('now'));
END;
CREATE TRIGGER Callback_delete AFTER DELETE ON Callback
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'Callback', datetime('now'));
END;

CREATE TRIGGER dateseg_insert AFTER INSERT ON dateseg
BEGIN
   UPDATE dateseg set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'dateseg', datetime('now'));   
END;
CREATE TRIGGER dateseg_update AFTER UPDATE ON dateseg
BEGIN
   UPDATE dateseg set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'dateseg', datetime('now'));
END;
CREATE TRIGGER dateseg_delete AFTER DELETE ON dateseg
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'dateseg', datetime('now'));
END;

CREATE TRIGGER globals_insert AFTER INSERT ON globals
BEGIN
   UPDATE globals set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'globals', datetime('now'));   
END;
CREATE TRIGGER globals_update AFTER UPDATE ON globals
BEGIN
   UPDATE globals set z_updated=datetime('now') where pkey=new.pkey;
   
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'globals', datetime('now'));
   
END;
CREATE TRIGGER globals_delete AFTER DELETE ON globals
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'globals', datetime('now'));
END;

CREATE TRIGGER ivrmenu_insert AFTER INSERT ON ivrmenu
BEGIN
   UPDATE ivrmenu set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'ivrmenu', datetime('now'));   
END;
CREATE TRIGGER ivrmenu_update AFTER UPDATE ON ivrmenu
BEGIN
   UPDATE ivrmenu set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'ivrmenu', datetime('now'));
END;
CREATE TRIGGER ivrmenu_delete AFTER DELETE ON ivrmenu
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'ivrmenu', datetime('now'));
END;

CREATE TRIGGER lineIO_insert AFTER INSERT ON lineIO
BEGIN
   UPDATE lineIO set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'lineIO', datetime('now'));   
END;
CREATE TRIGGER lineIO_update AFTER UPDATE ON lineIO
BEGIN
   UPDATE lineIO set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'lineIO', datetime('now'));
END;
CREATE TRIGGER lineIO_delete AFTER DELETE ON lineIO
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'lineIO', datetime('now'));
END;

CREATE TRIGGER mcast_insert AFTER INSERT ON mcast
BEGIN
   UPDATE mcast set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'mcast', datetime('now'));   
END;
CREATE TRIGGER mcast_update AFTER UPDATE ON mcast
BEGIN
   UPDATE mcast set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'mcast', datetime('now'));
END;
CREATE TRIGGER mcast_delete AFTER DELETE ON mcast
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'mcast', datetime('now'));
END;

CREATE TRIGGER meetme_insert AFTER INSERT ON meetme
BEGIN
   UPDATE meetme set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'meetme', datetime('now'));   
END;
CREATE TRIGGER meetme_update AFTER UPDATE ON meetme
BEGIN
   UPDATE meetme set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'meetme', datetime('now'));
END;
CREATE TRIGGER meetme_delete AFTER DELETE ON meetme
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'meetme', datetime('now'));
END;

CREATE TRIGGER mfgmac_insert AFTER INSERT ON mfgmac
BEGIN
   UPDATE mfgmac set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'mfgmac', datetime('now'));   
END;
CREATE TRIGGER mfgmac_update AFTER UPDATE ON mfgmac
BEGIN
   UPDATE mfgmac set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'mfgmac', datetime('now'));
END;
CREATE TRIGGER mfgmac_delete AFTER DELETE ON mfgmac
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'mfgmac', datetime('now'));
END;

CREATE TRIGGER page_insert AFTER INSERT ON page
BEGIN
   UPDATE page set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'page', datetime('now'));   
END;
CREATE TRIGGER page_update AFTER UPDATE ON page
BEGIN
   UPDATE page set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'page', datetime('now'));
END;
CREATE TRIGGER page_delete AFTER DELETE ON page
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'page', datetime('now'));
END;

CREATE TRIGGER speed_insert AFTER INSERT ON speed
BEGIN
   UPDATE speed set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'speed', datetime('now'));   
END;
CREATE TRIGGER speed_update AFTER UPDATE ON speed
BEGIN
   UPDATE speed set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'speed', datetime('now'));
END;
CREATE TRIGGER speed_delete AFTER DELETE ON speed
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'speed', datetime('now'));
END;

CREATE TRIGGER threat_insert AFTER INSERT ON threat
BEGIN
   UPDATE threat set z_created=datetime('now'), z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('INSERT', new.pkey, 'threat', datetime('now'));   
END;
CREATE TRIGGER threat_update AFTER UPDATE ON threat
BEGIN
   UPDATE threat set z_updated=datetime('now') where pkey=new.pkey;
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('UPDATE', new.pkey, 'threat', datetime('now'));
END;
CREATE TRIGGER threat_delete AFTER DELETE ON threat
BEGIN
   INSERT INTO master_audit(act,owner,relation,tstamp) VALUES ('DELETE', old.pkey, 'threat', datetime('now'));
END;

/* system xref triggers */

CREATE TRIGGER agent_xref_insert AFTER INSERT ON agent
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'agent');
END;
CREATE TRIGGER agent_xref_delete AFTER DELETE ON agent
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='agent'; 
END;
CREATE TRIGGER agent_update_key AFTER UPDATE OF pkey ON agent
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='agent';
END;

CREATE TRIGGER appl_xref_insert AFTER INSERT ON appl
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'appl');
END;
CREATE TRIGGER appl_xref_delete AFTER DELETE ON appl
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='appl'; 
END;
CREATE TRIGGER appl_update_key AFTER UPDATE OF pkey ON appl
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='appl';
END;

CREATE TRIGGER COS_xref_insert AFTER INSERT ON COS
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'COS');
END;
CREATE TRIGGER COS_xref_delete AFTER DELETE ON COS
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='COS'; 
END;
CREATE TRIGGER COS_update_key AFTER UPDATE OF pkey ON COS
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='COS';
END;

CREATE TRIGGER Carrier_xref_insert AFTER INSERT ON Carrier
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'Carrier');
END;
CREATE TRIGGER Carrier_xref_delete AFTER DELETE ON Carrier
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='Carrier'; 
END;
CREATE TRIGGER Carrier_update_key AFTER UPDATE OF pkey ON Carrier
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='Carrier';
END;

CREATE TRIGGER Cluster_xref_insert AFTER INSERT ON Cluster
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'Cluster');
END;
CREATE TRIGGER Cluster_xref_delete AFTER DELETE ON Cluster
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='Cluster'; 
END;
CREATE TRIGGER Cluster_update_key AFTER UPDATE OF pkey ON Cluster
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='Cluster';
END;

CREATE TRIGGER Device_xref_insert AFTER INSERT ON Device
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'Device');
END;
CREATE TRIGGER Device_xref_delete AFTER DELETE ON Device
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='Device'; 
END;
CREATE TRIGGER Device_update_key AFTER UPDATE OF pkey ON Device
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='Device';
END;

CREATE TRIGGER Greeting_xref_insert AFTER INSERT ON Greeting
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'Greeting');
END;
CREATE TRIGGER Greeting_xref_delete AFTER DELETE ON Greeting
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='Greeting'; 
END;
CREATE TRIGGER Greeting_update_key AFTER UPDATE OF pkey ON Greeting
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='Greeting';
END;

CREATE TRIGGER IPphone_xref_insert AFTER INSERT ON IPphone
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'IPphone');
END;
CREATE TRIGGER IPphone_xref_delete AFTER DELETE ON IPphone
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='IPphone'; 
END;
CREATE TRIGGER IPphone_update_key AFTER UPDATE OF pkey ON IPphone
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='IPphone';
END;

CREATE TRIGGER Queue_xref_insert AFTER INSERT ON Queue
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'Queue');
END;
CREATE TRIGGER Queue_xref_delete AFTER DELETE ON Queue
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='Queue'; 
END;
CREATE TRIGGER Queue_update_key AFTER UPDATE OF pkey ON Queue
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='Queue';
END;

CREATE TRIGGER Route_xref_insert AFTER INSERT ON Route
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'Route');
END;
CREATE TRIGGER Route_xref_delete AFTER DELETE ON Route
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='Route'; 
END;
CREATE TRIGGER Route_update_key AFTER UPDATE OF pkey ON Route
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='Route';
END;

CREATE TRIGGER User_xref_insert AFTER INSERT ON User
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'User');
END;
CREATE TRIGGER User_xref_delete AFTER DELETE ON User
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='User'; 
END;
CREATE TRIGGER User_update_key AFTER UPDATE OF pkey ON User
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='User';
END;

CREATE TRIGGER ivrmenu_xref_insert AFTER INSERT ON ivrmenu
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'ivrmenu');
END;
CREATE TRIGGER ivrmenu_xref_delete AFTER DELETE ON ivrmenu
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='ivrmenu'; 
END;
CREATE TRIGGER ivrmenu_update_key AFTER UPDATE OF pkey ON ivrmenu
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='ivrmenu';
END;

CREATE TRIGGER lineIO_xref_insert AFTER INSERT ON lineIO
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'lineIO');
END;
CREATE TRIGGER lineIO_xref_delete AFTER DELETE ON lineIO
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='lineIO'; 
END;
CREATE TRIGGER lineIO_update_key AFTER UPDATE OF pkey ON lineIO
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='lineIO';
END;

CREATE TRIGGER mcast_xref_insert AFTER INSERT ON mcast
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'mcast');
END;
CREATE TRIGGER mcast_xref_delete AFTER DELETE ON mcast
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='mcast'; 
END;
CREATE TRIGGER mcast_update_key AFTER UPDATE OF pkey ON mcast
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='mcast';
END;

CREATE TRIGGER meetme_xref_insert AFTER INSERT ON meetme
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'meetme');
END;
CREATE TRIGGER meetme_xref_delete AFTER DELETE ON meetme
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='meetme'; 
END;
CREATE TRIGGER meetme_update_key AFTER UPDATE OF pkey ON meetme
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='meetme';
END;

CREATE TRIGGER speed_xref_insert AFTER INSERT ON speed
BEGIN
	INSERT INTO master_xref(pkey, relation) VALUES (new.pkey, 'speed');
END;
CREATE TRIGGER speed_xref_delete AFTER DELETE ON speed
BEGIN
   DELETE from master_xref WHERE pkey=old.pkey AND relation='speed'; 
END;
CREATE TRIGGER speed_update_key AFTER UPDATE OF pkey ON speed
BEGIN
   UPDATE master_xref set pkey=new.pkey where pkey=old.pkey AND relation='speed';
END;

COMMIT;


