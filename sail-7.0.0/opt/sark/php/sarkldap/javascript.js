
  $(document).ready(function() {

	$('#upimg').click(function() {
		$('#file').click();
	});
	
	$('#file').change(function() {
		$('#upimgclick').val("TRUE");
		$('#sarkldapForm').submit();
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

	if ( $('#userext').val() == 'none' ) {
		$('.ddialcls').hide();
	}

	var scrollPosition;
/*
	$('#ldaptable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 'fti',
		"bSort" : false,
		"aoColumns": [ 
			{ "sName": "sn" },
			{ "sName": "givenname" },
			{ "sName": "telephonenumber" },
			{ "sName": null},
			{ "sName": "mobile" },
			{ "sName": null},
			{ "sName": "homephone" },
			{ "sName": null},
			{ "sName": "del" }
		],
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}  

	} ).makeEditable({
			sUpdateURL: "/php/sarkldap/update.php",		
			sReadOnlyCellClass: "read_only",
			"aoColumns": [
				{
					type: 'text',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set phone number',
					onblur: 'submit'			
				}, 		// surname								
				{
					type: 'text',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set phone number',
					onblur: 'submit',	
					placeholder: ' '				
				}, 		// forename				
				{
					type: 'text',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set phone number',
					onblur: 'submit',	
					placeholder: 'None',				
				}, 		// phone
				null, //dial								
				{
					type: 'text',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set cell phone number',
					onblur: 'submit',	
					placeholder: 'None'				
				}, 		// mobile	
				null,   //dial			
				{
					type: 'text',
					event: 'click',
//					submit:'Save',
//					tooltip: 'Click to set home number',
					onblur: 'submit',	
					placeholder: 'None'				
				}, 		// home 
				null,   //dial																						
				null	// delete col
            ]
    }); 
*/

		

    	$('#readldaptable').dataTable ( {
//		"sScrollY": ($(window).height() - 300),
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 'fti',
		"bSort" : false,
        "drawCallback": function() {
			$(".dataTables_scrollBody").scrollTop(scrollPosition);
		}  

		})  
    	if ( $('#perms').val() == 'view' ) { 
    		$('.editcol').hide();
    	}

//    	srkPerms('readldaptable');
		
// save scroll for redraw	
	
	$(".dataTables_scrollBody").mousedown(function(){
		scrollPosition = $(".dataTables_scrollBody").scrollTop();
	})	 	
          
 });
 

      
