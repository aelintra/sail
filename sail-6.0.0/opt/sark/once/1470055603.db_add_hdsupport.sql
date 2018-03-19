INSERT OR IGNORE INTO "Appl" VALUES('hotdesk','default','hotdesk invocation',';
; LOGIN
;
exten=>_*0XX.,1,PlayBack(silence/1)
exten=>_*0XX.,n,VMAuthenticate(${EXTEN:2},j)
exten=>_*0XX.,n,System(/opt/sark/scripts/aelhdlon.pl login ${EXTEN:2} ${CALLERID(number)} )
exten=>_*0XX.,n,Hangup
exten=>_*0XX.,101,Playtones(congestion)

;
; LOGOUT
;
exten=>_*01,1,PlayBack(silence/1)
;exten=>_*1,n,VMAuthenticate(${CALLERID(number)},j)
exten=>_*01,n,System(/opt/sark/scripts/aelhdlon.pl logout ${CALLERID(number)} )
exten=>_*01,n,Hangup

exten=>_*01,101,Playtones(congestion)

;
; LOGOUT FROM ANOTHER EXTENSION
;
exten=>_*02,1,PlayBack(silence/1)
exten=>_*02,n,Read(exten,extension,${EXTLEN},2)
exten=>_*02,n,PlayBack(thankyou)
exten=>_*02,n,VMauthenticate(${exten},j)
exten=>_*02,n,System(/opt/sark/scripts/aeldohdlon.pl logout ${exten} )
exten=>_*02,n,PlayBack(agent-loggedoff)
exten=>_*02,n,Hangup

exten=>_*02,101,Playtones(congestion)

;
; SUPERVISOR LOGOUT
;
exten=>_*03,1,PlayBack(silence/1)
exten=>_*03,n,Authenticate(3243,j)
exten=>_*03,n,System(/opt/sark/scripts/aeldohdlon.pl logout ${CALLERID(number)} )
exten=>_*03,n,Hangup

exten=>_*03,101,Playtones(congestion)

;
; SUPERVISOR LOGOUT FROM ANOTHER TERMINAL
;
exten=>_*04,1,PlayBack(silence/1)
exten=>_*04,n,Read(exten,extension,${EXTLEN},2)
exten=>_*04,n,PlayBack(thankyou)
exten=>_*04,n,Authenticate(3243,j)
exten=>_*04,n,System(/opt/sark/scripts/aeldohdlon.pl logout ${exten} )
exten=>_*04,n,PlayBack(agent-loggedoff)

exten=>_*04,101,Playtones(congestion)',NULL,'Internal','YES','2016-07-24 18:53:46','2016-07-28 16:03:22','system');
