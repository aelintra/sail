#!/bin/bash

SARKROOT=/opt/sark				#sark directory
DBROOT=/opt/sark/db				#db directory
SARKDB=sark.db					#updateable copy of the db
SARKRUNDB=sark.rdonly.db		#runtime copy of the db
LASTDB=sark.last.db				#sark db previous release
CLEANDB=sark.clean.db			#factory reset copy of the db 
CREATEDB=db_v4_create.sql		#installed db create
SYSTEMDB=db_v4_system.sql		#installed db system data
SYSINIDB=db_v4_inidat.sql		#installed db defaults
SYSDEVICE=db_v4_device.sql		#installed db device table
SYSONCE=once					#once directory
SYSALWAYS=always				#always directory
SYSONCEDONE=oncedone			#applied once files
CUSTDATA=last_data.sql			#customer data previous release
CUSTDEVICE=last_device.sql		#device table previous release
DUMPER=/generator/srkdumper.php #loc. of the dumper
GENAST=/scripts/srkgenAst		#loc. of the generator
HTTPOWNER=www-data:www-data		#default apache user/group (Debian/Ubuntu)


#set www:www if RHEL/CentOS/SME Server
grep -q 'Red Hat' /proc/version && HTTPOWNER=www:www 

NEWINSTALL=true

#save and dump existing db if it exists then delete it
if [ -e $DBROOT/$SARKDB ]  ; then
	echo "Saving customer database as $DBROOT/last_sark.db"
	cp -a $DBROOT/$SARKDB $DBROOT/$LASTDB
	php $SARKROOT/$DUMPER 
	if [ $? -ne 0 ]; then
		echo DUMP ERROR
		exit 4
	fi
	rm -rf $DBROOT/$SARKDB
	NEWINSTALL=false
else
	echo No customer database - not saving
fi

sqlite3 $DBROOT/$SARKDB 'PRAGMA synchronous=0;'
sqlite3 $DBROOT/$SARKDB 'PRAGMA journal_mode=MEMORY;' >/dev/null 2>&1

#create the db from the system files
echo Creating new database
sqlite3 $DBROOT/$SARKDB < $DBROOT/$CREATEDB

#Load the system data
echo Loading initial system data
sqlite3 $DBROOT/$SARKDB < $DBROOT/$SYSTEMDB
echo Loading system device data
sqlite3 $DBROOT/$SARKDB < $DBROOT/$SYSDEVICE

#Reload any saved customer data
if [ "$NEWINSTALL" = false ]; then
	if [ -e $DBROOT/$CUSTDATA ]; then
		echo Loading customer data
		sqlite3 $DBROOT/$SARKDB < $DBROOT/$CUSTDATA
	fi

	if [ -e $DBROOT/$CUSTDEVICE ]; then
		echo Loading customer device data
		sqlite3 $DBROOT/$SARKDB < $DBROOT/$CUSTDEVICE
	fi
fi

#run the once files
echo Running ONCE files..
if [ "$(ls -A $SARKROOT/$SYSONCE)" ]; then
	for file in $(ls $SARKROOT/$SYSONCE/) ; do
		if [ ! -e $SARKROOT/$SYSONCEDONE/$file ]; then
			echo "Applying oncefile $file to the DB"
			sqlite3 $DBROOT/$SARKDB < $SARKROOT/$SYSONCE/$file
			cp -a $SARKROOT/$SYSONCE/$file $SARKROOT/$SYSONCEDONE/$file
		else 
			echo "Skipping oncefile $file because it is already applied"
		fi	
	done
else 
	echo "No ONCE files to apply (Directory is empty)"
fi

#run the always files
echo Running ALWAYS files..
if [ "$(ls -A $SARKROOT/$SYSALWAYS)" ]; then
	for file in $(ls $SARKROOT/$SYSALWAYS/) ; do
		echo "Applying alwaysfile $file to the DB"
		sqlite3 $DBROOT/$SARKDB < $SARKROOT/$SYSALWAYS/$file
	done
else 
	echo "No ALWAYS files to apply (Directory is empty)"
fi

sqlite3 $DBROOT/$SARKDB 'PRAGMA synchronous=1;'
sqlite3 $DBROOT/$SARKDB 'PRAGMA journal_mode=DELETE;' >/dev/null 2>&1

# save a copy of the original installed database (for factory reset)
[ "$NEWINSTALL" = true ] && cp $DBROOT/$SARKDB $DBROOT/$CLEANDB

# run the generator
echo Running the Generator
sh $SARKROOT$GENAST

#set ownership
chown $HTTPOWNER $DBROOT/*
