#!/bin/bash

COUNTRYURL='http://www.ipdeny.com/ipblocks/data/countries/all-zones.tar.gz'
COUNTRYTAR='all-zones.tar.gz';

wget $COUNTRYURL > /dev/null 2>&1
[ $? -ne 0 ] && logger "can't fetch countries files" && exit 4 
diff $COUNTRYTAR /opt/sark/countries/$COUNTRYTAR > /dev/null 2>&1
if [ $? -ne 0 ]; then
    logger SARK fetching new countries file
    rm -rf /opt/sark/countries/* 
    cp $COUNTRYTAR /opt/sark/countries
    tar xzf /opt/sark/countries/$COUNTRYTAR -C /opt/sark/countries > /dev/null 2>&1
    rm -rf $COUNTRYTAR
else
    logger SARK country files up to date - no action   
fi


