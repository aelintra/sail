
  $(document).ready(function() {
	  
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
	}, ' Invalid IP Address');

	$.validator.addMethod("startequalLen", function(value, element) {
		if (  $('#EXTLEN').val() == 3)
			if (value.length==3) 
			return true;
		if (  $('#EXTLEN').val() == 4)
			if (value.length==4) 
			return true;			
		return false;
	}, "* sipisaxstart should match extension length");	
            
	$("#sarkglobalForm").validate ( {
	   rules: {
		    EDOMAIN: "validIP",
			AGENTSTART: "required digits",			
			PWDLEN: {range:[6,18]},
			SYSPASS: {required: true, range:[1000,9999]},
			SPYPASS: {required: true, range:[1000,9999]},
			SUPEMAIL: {email: true},
			INTRINGDELAY: {required: true, range:[1,999]},
			ABSTIMEOUT: {required: true, range:[0,99999]},	
			VOIPMAX: {required: true, range:[0,999]},
			EXTLEN: {required: true, range:[3,4]},
//			SIPIAXSTART: "required digits",
//			SIPIAXSTART: {required: true, range:[100,9900]},
			SIPIAXSTART: "required digits startequalLen",
			AGENTSTART: {required: true, range:[1000,9900]},
			OPERATOR: {required: true, range:[0,9999]},
			LOGLEVEL: {required: true, range:[0,9]},
			VMAILAGE: "digits"
	   },
	   messages: {
		    PWDLEN: "Password length must be between 6 and 18 or empty for default",
			SYSPASS: "You must enter a 4 digit number between 1000 & 9999",
			SPYPASS: "You must enter a 4 digit number between 1000 & 9999",
			INTRINGDELAY: "You must enter a 4 digit number between 1 & 999",
			ABSTIMEOUT: "You must enter a number between 1 & 99999",
			VOIPMAX: "You must enter a 4 digit number between 1 & 99999",
			EXTLEN: "You must enter 3 or 4",
			SIPIAXSTART: "MUST be digits and MUST match extension length)",
			AGENTSTART: "You must enter a 4 digit number between 1000 & 9900",
			OPERATOR: "You must enter a number between 0 & 9999 but it should NOT be a real extension number",
			LOGLEVEL:"LOGLEVEL must be between 0 and 9"		   
	   }	
	   				
	});  


         
 });
 

      
