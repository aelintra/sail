
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

	$('#ldaptable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 'fti',
		"bSort" : true,
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [5]
		}],
		"aoColumns": [ 
			{ "sName": "sn" },
			{ "sName": "givenname" },
			{ "sName": "telephonenumber" },
			{ "sName": "mobile" },
			{ "sName": "homephone" },
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
					onblur: 'submit'			
				}, 		// surname	
				{
					type: 'text',
					event: 'click',
					onblur: 'submit'			
				}, 		// givenname													
				{
					type: 'text',
					event: 'click',
					onblur: 'submit',	
					placeholder: ' '				
				}, 		// forename				
				{
					type: 'text',
					event: 'click',
					onblur: 'submit',	
					placeholder: 'None',				
				}, 		// phone
				{
					type: 'text',
					event: 'click',
					onblur: 'submit',	
					placeholder: 'None'				
				}, 		// mobile	
				null	// delete col
            ]
    }); 


    	$('#readldaptable').dataTable ( {
		"bPaginate": false,
		"bAutoWidth": true,
		"bStateSave": true,
		"sDom": 'fti',
		"aoColumnDefs" : [{
			"bSortable" : false,
			"aTargets" : [5]
		}],
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
 

      
