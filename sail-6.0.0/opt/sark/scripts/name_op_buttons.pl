{
#
#----------------------------------------------------------------------
#
# This program is free software; you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation; either version 2 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with this program; if not, write to the Free Software
# Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA 02111-1307  USA
#
# © Copyright Selintra Ltd. www.selintra.com
#
# Technical support for this program is available from Selintra Ltd.
# Please visit our web site www.selintra.com for details.
#----------------------------------------------------------------------

use lib '/opt/sark/perl/modules';
use strict;
use sark::SarkSubs;

my @Lines;
my %nameLines;
my $seq = 1;
my $span= ();
my $pkey;
my $desc;
my $shortdesc;
my $OUT = "#begin generate\n\n";

my $dbh = SarkSubs::SQLiteConnect();
@Lines = sort (SarkSubs::SQLiteGetKeys($dbh, "SELECT pkey FROM IPphone" ));

foreach (@Lines) {
	my $desc = SarkSubs::SQLiteGet($dbh, "SELECT desc FROM IPphone where pkey = '$_'");
	$nameLines{$desc} = $_;
}

foreach $desc (sort keys %nameLines)  {
	my $pkey = $nameLines{$desc};
    my $technology = SarkSubs::SQLiteGet($dbh, "SELECT technology FROM IPphone where pkey = '$pkey'");
	$desc =~ /^(\w+)/;
	
	$shortdesc = $1;
	if ( length $shortdesc > 9 ) {
		$shortdesc = substr($shortdesc , 0, 8);
		$shortdesc .= '.';
    }	
	unless ($seq > 75 || $technology eq 'Analogue') {		
        $OUT .= "[$technology/$pkey]\n";
        $OUT .= "type=extension \n";	
#        $OUT .= "Position=$seq \n";
        $OUT .= "Label=$shortdesc \n";
        $OUT .= "Extension=$pkey \n";
        $OUT .= "Context=extensions \n";
#       $OUT .= "Mailbox=$_\@extensions \n";
#        $OUT .= "Icon=1 \n";
        $OUT .= "\n";
		$seq++;
	}
}
    $seq = 63;
    @Lines = ();
    @Lines = sort (SarkSubs::SQLiteGetKeys($dbh, "SELECT pkey FROM LineIO" ));

    $OUT .= "[_SIP/(.{5,}|[A-Z])]\n";
#    $OUT .= "[SIP/.*]\n";
    $OUT .= "Position = 62\n";
    $OUT .= "Count=12\n";
    $OUT .= "Label=SIP Trunks\n";
    $OUT .= "Extension=-1\n";
    $OUT .= "Icon=4\n";
    $OUT .= "groupcount=1\n";
    $OUT .= "\n";

    $OUT .= "[_IAX2/(.{5,}|[A-Z])]\n";
    $OUT .= "Position = 63\n";
    $OUT .= "Count=12\n";
    $OUT .= "Label=IAX Trunks\n";
    $OUT .= "Extension=-1\n";
    $OUT .= "Icon=4\n";
    $OUT .= "groupcount=1\n";
    $OUT .= "\n";

    $OUT .= "[_Zap/.*]\n";
    $OUT .= "Position = 64\n";
    $OUT .= "Label=ZAP Trunks\n";
    $OUT .= "Count=30\n";
    $OUT .= "Extension=-1\n";
    $OUT .= "Icon=4\n";
    $OUT .= "groupcount=1\n";
    $OUT .= "\n";

    $seq = 67;
    @Lines = ();
    @Lines = sort (SarkSubs::SQLiteGetKeys($dbh, "SELECT pkey FROM Queue" ));

	foreach (@Lines)
    	{
	     	unless ($seq > 69)
        		{
               	my $name = SarkSubs::SQLiteGet($dbh, "SELECT name FROM Queue where pkey = '$_'");
                $OUT .= "\n";
				$OUT .= "[QUEUE/$_]\n";
            	$OUT .= "Position=$seq \n";
            	$OUT .= "Label=$name\n";
            	$OUT .= "Extension=-1\n";
            	$OUT .= "Icon=5\n";
           		$OUT .= "\n";
			}
		$seq++;
	}

    $seq = 71;
    my @Rooms = `cat /etc/asterisk/meetme.conf`;
    foreach(@Rooms) {
		unless ( $seq > 74 ) {
			if ( /^conf\s*\=/ ) {
				$_ =~ /(\d{1,4})/;
            	$OUT .= "\n";
            	$OUT .= "[$1]\n";
            	$OUT .= "Position=$seq \n";
            	$OUT .= "Label=\"Conf $1\"\n";
            	$OUT .= "Extension=$1\n";
            	$OUT .= "Context=conferences\n";
                $OUT .= "Icon=6\n";
            	$OUT .= "\n";
				$seq++;
			}
		}
	}

    $seq = 76;
    my $park = `grep parkpos /etc/asterisk/sark_features_general.conf` || "";
    $park =~ /parkpos=>(\d{1,4})/;
    my $parkstart = $1;
    my $parkend = $parkstart + 4;
    while ($parkstart lt $parkend) {
      	$OUT .= "\n";
      	$OUT .= "[PARK/$parkstart]\n";
      	$OUT .= "Position=$seq \n";
      	$OUT .= "Label=\"Park $parkstart\"\n";
      	$OUT .= "Extension=$parkstart \n";
      	$OUT .= "Icon=3\n";
      	$OUT .= "\n";
		$parkstart++;
		$seq++;
    }
    SarkSubs::SQLiteDisconnect($dbh);

open MYOUT, ">/opt/sark/fop/name_op_buttons.cfg";
print MYOUT "$OUT";

print MYOUT <<"LABELS";

[LEGEND]
x=780
y=32
#hpe ( Hides the first line when fop active ... not that nice.)
text= Trunklines
font_size=24
font_color=000000
font_family=Verdana ; only used when use_embed_fonts=0
use_embed_fonts=0

[LEGEND]
x=780
y=170
#hpe ( Hides the first line when fop active ... not that nice.)
text= Queues
font_size=24
font_color=000000
font_family=Verdana ; only used when use_embed_fonts=0
use_embed_fonts=0

[LEGEND]
x=780
y=283
#hpe ( Hides the first line when fop active ... not that nice.)
text= Conferences
font_size=24
font_color=000000
font_family=Verdana ; only used when use_embed_fonts=0
use_embed_fonts=0

[LEGEND]
x=780
y=423
#hpe ( Hides the first line when fop active ... not that nice.)
text= ParkedCalls
font_size=24
font_color=000000
font_family=Verdana ; only used when use_embed_fonts=0
use_embed_fonts=0


[LEGEND]
x=40
y=-4
#hpe ( Hides the first line when fop active ... not that nice.)
text=
font_size=32
font_color=000000
font_family=Ariel ; only used when use_embed_fonts=0
use_embed_fonts=0
LABELS

close MYOUT;

}
