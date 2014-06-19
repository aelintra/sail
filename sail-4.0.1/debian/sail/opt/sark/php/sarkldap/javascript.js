
  $(document).ready(function() {
	  
	$('[title!=""]').qtip({
		position: {
			my: 'bottom right',
			at: 'top left',			
			viewport: $(window)
		},
		style: {
			classes: 'qtip-light qtip-rounded qtip-shadow'
		},
	});	

	
	$.validator.addMethod("alpha",function(value,element) {
		return this.optional(element) || /^[ A-Za-z0-9_-]{1,50}$/i.test(value); 
	},"field can only contain alphanumerics, underscores, hyphens and spaces (no special characters)");			
		  
	$("#sarkldapForm").validate ( {
	   rules: {
			sn: "required alpha",
			givenname: "alpha",
			telephonenumber: "digits",
			mobile: "digits",
			homephone: "digits"
	   },
	   messages: {
	   }					
	});  	  

	$('#ldaptable').dataTable ( {
		"sScrollY": "238px",
		"bPaginate": false,
		"bAutoWidth": true,
		"sDom": 'tf',
		"aoColumnDefs": [ 
			{ "bSortable": false, "aTargets": [ 5 ] },
		],
		"aoColumns": [ 
			{ "sName": "sn" },
			{ "sName": "givenname" },
			{ "sName": "telephonenumber" },
			{ "sName": "mobile" },
			{ "sName": "homephone" },
			{ "sName": "del" }
		],
		"fnRowCallback": function( nRow, aData, iDisplayIndex, iDisplayIndexFull ) {
          $('td:eq(0),td:eq(1),td:eq(2),td:eq(3),td:eq(4)', nRow).addClass( "bluetags" );
        }    

	} ).makeEditable({
			sUpdateURL: "/php/sarkldap/update.php",		
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set phone number',
					onblur: 'cancel'			
				}, 		// surname								
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set phone number',
					onblur: 'cancel',	
					placeholder: ' '				
				}, 		// forename				
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set phone number',
					onblur: 'cancel',	
					placeholder: 'None',				
				}, 		// phone								
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set cell phone number',
					onblur: 'cancel',	
					placeholder: 'None'				
				}, 		// mobile				
				{
					type: 'textarea',
					submit:'Save',
					tooltip: 'Double Click to set home number',
					onblur: 'cancel',	
					placeholder: 'None'				
				}, 		// home 																						
				null	// delete col
            ]
    });   

          
 });
 

      
