#!/bin/sh
#
# Set ip and subnet for shorewall
#
/usr/bin/perl /opt/sark/scripts/setip.pl
#
# Run the generator
#
echo Running the Generator
/bin/sh /opt/sark/scripts/srkgenAst
echo Done

