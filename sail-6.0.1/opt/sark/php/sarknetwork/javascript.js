
  $(document).ready(function() {

 	toggleDhcpStatus();
	toggleDhcpdStatus();

// readonly elements
	$('#ipv6elements :input').prop('readonly', true);
	$('#ipv6elements :input').css('background-color','#f1f1f1');
	$('#publicip :input').prop('readonly', true);
	$('#publicip :input').css('background-color','#f1f1f1');	
	      
    $.validator.addMethod('validIP', function(value) {
		if (value.length==0) 
			return true;

		var split = value.split('.');
		if (split.length != 4) 
			return false;
            
		for (var i=0; i<split.length; i++) {
			var s = split[i];
			if (s.length==0 || isNaN(s) || s<0 || s>255)
				return false;
		}
		return true;
	}, "Invalid IP/Mask");	
	
	$.validator.addMethod("domaincheck",function(value,element) {
		return this.optional(element) || /^([\da-z\.-]+)\.([a-z\.]{2,6})$/i.test(value); 
	},"This domain looks wrong");	
	
	$.validator.addMethod("hostcheck",function(value,element) {
		return this.optional(element) || /^[A-Za-z0-9\-]{2,63}$/i.test(value); 
	},"This hostname looks wrong - it should be just the leaf domain");	
	
	$.validator.addMethod("greaterThan", function(value, element, param) {
      return this.optional(element) || value >= $(param).val();
	}, 'Dhcpend must be greater than dhcpstart');		
	  
	$("#sarknetworkForm").validate ( {
	   rules: {
			ipaddr: "validIP",
			netmask: "validIP", 
			gatewayip: "validIP", 
			dhcpstart: "required validIP",
			dhcpend: "required validIP",		
			domain: "domaincheck",
			dns1: "validIP",
			dns2: "validIP",
			sshport: {required: true, range:[1,65535]},
			hostname: "required hostcheck"
			
	   },
	   messages: {
		   sshport: "1-65535 please"
	   }					
	});
	$('#toggleDhcpd').click(function(event) {  //on click 
            toggleDhcpdStatus();
               
    });	
	$('#toggleDhcpElement').click(function(event) {  //on click 
            toggleDhcpStatus();               
    });

 });
 
function toggleDhcpStatus() {
	if ( $('#toggleDhcpElement').length ) {
    	if ($('#toggleDhcpElement').is(':checked')) {
        	$('#elementsToOperateOnDhcp :input').prop('readonly', true);
        	$('#elementsToOperateOnDhcp :input').css('background-color','#f1f1f1');
        	$('#dhcp-srv').hide(); 
    	} else {
        	$('#elementsToOperateOnDhcp :input').prop('readonly', false);
        	$('#elementsToOperateOnDhcp :input').css('background-color','white');
        	$('#dhcp-srv').show(); 
    	}
    } else {
    	$('#elementsToOperateOnDhcp :input').prop('readonly', true);
        $('#elementsToOperateOnDhcp :input').css('background-color','#f1f1f1');
        $('#dhcp-srv').hide(); 
    }
}	
function toggleDhcpdStatus() {
    if ($('#toggleDhcpd').is(':checked')) {
        $('#elementsToOperateOnDhcpD :input').prop('readonly', false);
        $('#elementsToOperateOnDhcpD :input').css('background-color','white');
    } else {
        $('#elementsToOperateOnDhcpD :input').prop('readonly', true);
        $('#elementsToOperateOnDhcpD :input').css('background-color','#f1f1f1'); 
    }
} 
